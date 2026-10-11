<?php

namespace App\Http\Controllers;

use App\Models\CustomerTransaction;
use App\Models\InventoryItem;
use App\Models\ServiceMaterialRule;
use App\Models\TransactionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CustomerTransactionController extends Controller
{
    public function staffIndex(Request $request)
    {
        $transactions = $this->filteredTransactions($request)
            ->with(['staff', 'details', 'payments'])
            ->latest('transaction_date')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $summary = $this->statusSummary();

        return view('staff.transactions.index', compact('transactions', 'summary'));
    }

    public function managerIndex(Request $request)
    {
        $transactions = $this->filteredTransactions($request)
            ->with(['staff', 'details', 'payments'])
            ->latest('transaction_date')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $summary = $this->statusSummary();

        return view('branch-manager.transactions.index', compact('transactions', 'summary'));
    }

    public function managerShow(CustomerTransaction $transaction)
    {
        $transaction->load(['details', 'staff', 'payments.staff']);

        return view('branch-manager.transactions.show', compact('transaction'));
    }

    public function create()
    {
        $serviceCatalog = config('photoline_services.catalog', []);
        $merchandise = InventoryItem::where('item_type', 'merchandise')->where('quantity', '>', 0)->orderBy('name')->get();
        return view('staff.transactions.create', compact('serviceCatalog', 'merchandise'));
    }

    public function store(Request $request)
    {
        $data = $this->validateTransaction($request);

        $transaction = DB::transaction(function () use ($data) {
            $transaction = CustomerTransaction::create([
                'control_number' => $this->generateControlNumber(),
                'staff_id' => auth('staff')->id(),
                'customer_name' => trim($data['customer_name']),
                'customer_contact_number' => $data['customer_contact_number'] ?? null,
                'customer_address' => trim($data['customer_address'] ?? '') ?: null,
                'transaction_date' => $data['transaction_date'],
                'discount_type' => $data['discount_type'] ?? null,
                'discount_value' => $data['discount_type'] ? ($data['discount_value'] ?? 0) : 0,
                'status' => 'Pending',
            ]);

            $transaction->details()->createMany($data['details']);

            // If the customer paid something right away, save it as the first payment.
            // Later payments are added with recordPayment().
            $firstPayment = (float) ($data['amount_paid'] ?? 0);

            if ($firstPayment > 0) {
                $transaction->payments()->create([
                    'staff_id' => auth('staff')->id(),
                    'amount' => $firstPayment,
                    'payment_type' => $data['payment_type'],
                    'paid_at' => now(),
                    'is_initial' => true,
                ]);
            }

            return $transaction;
        });

        return redirect()
            ->route('staff.transactions.show', $transaction)
            ->with('success', 'Transaction created successfully.');
    }

    public function show(CustomerTransaction $transaction)
    {
        $transaction->load(['details', 'staff', 'payments.staff']);

        return view('staff.transactions.show', compact('transaction'));
    }

    public function edit(CustomerTransaction $transaction)
    {
        $this->ensurePending($transaction);
        $transaction->load('details');

        $serviceCatalog = config('photoline_services.catalog', []);
        $merchandise = InventoryItem::where('item_type', 'merchandise')->orderBy('name')->get();
        return view('staff.transactions.edit', compact('transaction', 'serviceCatalog', 'merchandise'));
    }

    public function update(Request $request, CustomerTransaction $transaction)
    {
        $this->ensurePending($transaction);

        // passing the transaction lets validation check the new total against what was already paid
        $data = $this->validateTransaction($request, $transaction);

        DB::transaction(function () use ($transaction, $data) {
            // payments are not touched here, they keep their own history
            $transaction->update([
                'customer_name' => trim($data['customer_name']),
                'customer_contact_number' => $data['customer_contact_number'] ?? null,
                'customer_address' => trim($data['customer_address'] ?? '') ?: null,
                'transaction_date' => $data['transaction_date'],
                'discount_type' => $data['discount_type'] ?? null,
                'discount_value' => $data['discount_type'] ? ($data['discount_value'] ?? 0) : 0,
            ]);

            $transaction->details()->delete();
            $transaction->details()->createMany($data['details']);
        });

        return redirect()
            ->route('staff.transactions.show', $transaction)
            ->with('success', 'Transaction updated successfully.');
    }

    /**
     * Add another payment to an existing transaction (deposit, balance, etc.).
     * It never changes the total due, it only lowers the remaining balance.
     */
    public function recordPayment(Request $request, CustomerTransaction $transaction)
    {
        if ($transaction->status === 'Voided') {
            abort(403, 'Payments cannot be added to a voided transaction.');
        }

        $transaction = DB::transaction(function () use ($request, $transaction) {
            // lock the row so a double click can't record the same payment twice
            $transaction = CustomerTransaction::whereKey($transaction->id)
                ->lockForUpdate()
                ->with(['details', 'payments'])
                ->firstOrFail();

            $balance = $transaction->balance;

            if ($balance <= 0) {
                throw ValidationException::withMessages(['amount' => 'This transaction is already fully paid.']);
            }

            $data = $request->validate([
                'amount' => ['required', 'numeric', 'min:0.01', 'max:' . $balance],
                'payment_type' => ['required', Rule::in(TransactionPayment::METHODS)],
                'paid_at' => ['required', 'date', 'before_or_equal:now'],
            ], [
                'amount.max' => 'The payment cannot be more than the remaining balance of ₱' . number_format($balance, 2) . '.',
            ]);

            $transaction->payments()->create([
                'staff_id' => auth('staff')->id(),
                'amount' => $data['amount'],
                'payment_type' => $data['payment_type'],
                'paid_at' => $data['paid_at'],
            ]);

            // reload so the balance below includes the new payment
            return $transaction->load('payments');
        });

        return redirect()
            ->route('staff.transactions.show', $transaction)
            ->with('success', 'Payment recorded. Remaining balance: ₱' . number_format($transaction->balance, 2) . '.');
    }

    public function complete(CustomerTransaction $transaction)
    {
        if ($transaction->status !== 'Pending') {
            abort(403, 'Only pending transactions can be completed.');
        }

        // services that have no material rule yet (we still complete, but tell the staff)
        $servicesWithoutRule = [];

        DB::transaction(function () use ($transaction, &$servicesWithoutRule) {
            $transaction = CustomerTransaction::whereKey($transaction->id)->lockForUpdate()->with(['details', 'payments'])->firstOrFail();
            if ($transaction->status !== 'Pending') {
                abort(403, 'This transaction was already completed or voided.');
            }

            // a transaction can only be completed once it is fully paid
            if ($transaction->balance > 0) {
                throw ValidationException::withMessages(['payment' => 'This transaction still has a balance of ₱' . number_format($transaction->balance, 2) . '. Record the remaining payment before completing it.']);
            }

            $usageRows = [];
            $productRows = [];
            foreach ($transaction->details as $detail) {
                if (($detail->line_type ?? 'service') === 'product' || $detail->inventory_item_id) {
                    $productRows[$detail->inventory_item_id] = ($productRows[$detail->inventory_item_id] ?? 0) + (int) $detail->quantity;
                    continue;
                }
                $serviceKey = $detail->service_type . '|' . ($detail->service_size ?? '') . '|';
                $rules = ServiceMaterialRule::where('service_name', $serviceKey)->get();
                if ($rules->isEmpty()) {
                    // the manager has not set what this service uses, so there is nothing to deduct
                    $servicesWithoutRule[$serviceKey] = trim($detail->service_type . ' ' . $detail->service_size);
                    continue;
                }
                foreach ($rules as $rule) {
                    $used = (int) $rule->quantity_per_service * (int) $detail->quantity;
                    if ($used < 1) {
                        throw ValidationException::withMessages(['materials' => 'Material recipe quantities must use whole inventory units greater than zero.']);
                    }
                    $usageRows[$rule->inventory_item_id] = ($usageRows[$rule->inventory_item_id] ?? 0) + $used;
                }
            }

            foreach ($productRows as $itemId => $sold) {
                $product = InventoryItem::whereKey($itemId)->lockForUpdate()->firstOrFail();
                if ($product->item_type !== 'merchandise' || $product->quantity < $sold) {
                    throw ValidationException::withMessages(['products' => 'A selected merchandise product is no longer available in the required quantity. No stock was changed.']);
                }
            }
            foreach ($productRows as $itemId => $sold) {
                $product = InventoryItem::whereKey($itemId)->lockForUpdate()->firstOrFail();
                $product->decrement('quantity', $sold);
            }

            foreach ($usageRows as $itemId => $used) {
                $item = InventoryItem::whereKey($itemId)->lockForUpdate()->firstOrFail();
                if ($item->item_type !== 'raw_material') {
                    throw ValidationException::withMessages(['materials' => 'A service recipe references merchandise. Only raw materials can be deducted automatically.']);
                }
                if ($item->quantity < $used) {
                    throw ValidationException::withMessages(['materials' => 'Not enough ' . $item->name . ' in stock. Required: ' . $used . ', available: ' . $item->quantity . '. Stock was not changed.']);
                }
            }

            foreach ($usageRows as $itemId => $used) {
                $item = InventoryItem::whereKey($itemId)->lockForUpdate()->firstOrFail();
                $item->decrement('quantity', $used);
                DB::table('transaction_material_usages')->insert([
                    'customer_transaction_id' => $transaction->id,
                    'inventory_item_id' => $itemId,
                    'quantity_used' => $used,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $transaction->update(['status' => 'Completed']);
        });

        $message = 'Transaction completed and configured raw materials deducted from inventory.';

        if ($servicesWithoutRule) {
            $message = 'Transaction completed. No raw materials were deducted for: ' . implode(', ', $servicesWithoutRule)
                . ' (no material rule is set for it yet).';
        }

        return redirect()->route('staff.transactions.show', $transaction)->with('success', $message);
    }

    public function void(Request $request, CustomerTransaction $transaction)
    {
        $this->ensurePending($transaction);

        $data = $request->validate([
            'void_reason' => ['required', 'string', 'max:255'],
        ]);

        $transaction->update([
            'status' => 'Voided',
            'void_reason' => trim($data['void_reason']),
        ]);

        return redirect()
            ->route('staff.transactions.show', $transaction)
            ->with('success', 'Transaction has been voided.');
    }

    // $existing is only passed when editing a transaction
    private function validateTransaction(Request $request, ?CustomerTransaction $existing = null): array
    {
        $catalog = config('photoline_services.catalog', []);

        $rules = [
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_contact_number' => ['nullable', 'string', 'max:30'],
            'customer_address' => ['nullable', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
            'discount_type' => ['nullable', Rule::in(['fixed', 'percentage'])],
            'discount_value' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'details' => ['required', 'array', 'min:1', 'max:30'],
            'details.*.line_type' => ['required', Rule::in(['service', 'product'])],
            'details.*.service_type' => ['nullable', 'string', Rule::in(array_keys($catalog))],
            'details.*.service_size' => ['nullable', 'string', 'max:40'],
            'details.*.inventory_item_id' => ['nullable', 'integer', 'exists:inventory_items,id'],
            'details.*.order_notes' => ['nullable', 'string', 'max:150'],
            'details.*.quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'details.*.unit_price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ];

        // the first payment is only taken when the transaction is created
        if (! $existing) {
            $rules['payment_type'] = ['required', Rule::in(TransactionPayment::METHODS)];
            $rules['amount_paid'] = ['nullable', 'numeric', 'min:0', 'max:99999999.99'];
        }

        $data = $request->validate($rules);

        $subtotalCents = 0;
        $requestedProductQuantities = [];
        foreach ($data['details'] as $index => &$detail) {
            $quantity = (int) $detail['quantity'];
            if ($detail['line_type'] === 'product') {
                if (empty($detail['inventory_item_id'])) {
                    throw ValidationException::withMessages(["details.{$index}.inventory_item_id" => 'Choose a merchandise product.']);
                }
                $item = InventoryItem::where('item_type', 'merchandise')->find($detail['inventory_item_id']);
                if (!$item) {
                    throw ValidationException::withMessages(["details.{$index}.inventory_item_id" => 'The selected product is not merchandise.']);
                }
                $requestedProductQuantities[$item->id] = ($requestedProductQuantities[$item->id] ?? 0) + $quantity;
                if ($requestedProductQuantities[$item->id] > (int) $item->quantity) {
                    $requested = $requestedProductQuantities[$item->id];
                    throw ValidationException::withMessages(["details.{$index}.quantity" => "This transaction requests {$requested} unit(s) of {$item->name}, but only {$item->quantity} are currently in stock. Reduce the combined quantity across its rows."]);
                }
                $detail['service_type'] = 'Merchandise';
                $detail['service_size'] = null;
                $detail['print_layout'] = null;
                $detail['description'] = $item->name;
                $detail['amount'] = number_format((float) $item->unit_price, 2, '.', '');
                $detail['inventory_item_id'] = $item->id;
                $subtotalCents += (int) round((float) $item->unit_price * 100) * $quantity;
                continue;
            }
            $service = $detail['service_type'] ?? '';
            $size = $detail['service_size'] ?? '';
            if (!isset($catalog[$service]['sizes'][$size])) {
                throw ValidationException::withMessages(["details.{$index}.service_size" => 'Choose a valid size for the selected service.']);
            }
            $price = $detail['unit_price'] ?? null;
            if (!is_numeric($price) || (float) $price < 0) {
                throw ValidationException::withMessages(["details.{$index}.unit_price" => "Enter a valid price for {$service} - {$size}."]);
            }
            $detail['inventory_item_id'] = null;
            $detail['description'] = trim((string) ($detail['order_notes'] ?? '')) ?: null;
            $detail['print_layout'] = null;
            $detail['amount'] = number_format((float) $price, 2, '.', '');
            $subtotalCents += (int) round((float) $price * 100) * $quantity;
        }
        unset($detail);

        $subtotal = $subtotalCents / 100;
        $discountValue = (float) ($data['discount_value'] ?? 0);
        if (($data['discount_type'] ?? null) === 'percentage' && $discountValue > 100) {
            throw ValidationException::withMessages(['discount_value' => 'A percentage discount cannot exceed 100%.']);
        }
        $discount = ($data['discount_type'] ?? null) === 'percentage'
            ? round($subtotal * $discountValue / 100, 2)
            : round($discountValue, 2);
        if ($discount > $subtotal) {
            throw ValidationException::withMessages(['discount_value' => 'The discount cannot exceed the subtotal.']);
        }
        $finalTotal = round($subtotal - $discount, 2);

        if ($existing) {
            // editing: the new total can't go below what the customer already paid
            if ($finalTotal < $existing->amount_paid) {
                throw ValidationException::withMessages(['details' => 'The new total due (₱' . number_format($finalTotal, 2) . ') is lower than the ₱' . number_format($existing->amount_paid, 2) . ' already paid on this transaction.']);
            }
        } elseif ((float) ($data['amount_paid'] ?? 0) > $finalTotal) {
            // creating: the first payment can't be more than the total due
            throw ValidationException::withMessages(['amount_paid' => 'The payment cannot be more than the total due (₱' . number_format($finalTotal, 2) . ').']);
        }

        return $data;
    }

    // the count tiles at the top of the transaction list
    private function statusSummary(): array
    {
        return [
            'total' => CustomerTransaction::count(),
            'pending' => CustomerTransaction::where('status', 'Pending')->count(),
            'completed' => CustomerTransaction::where('status', 'Completed')->count(),
            'voided' => CustomerTransaction::where('status', 'Voided')->count(),
        ];
    }

    private function filteredTransactions(Request $request)
    {
        return CustomerTransaction::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->string('search')->toString());
                $query->where(function ($subquery) use ($search) {
                    $subquery->where('control_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status') && in_array($request->status, ['Pending', 'Completed', 'Voided'], true),
                fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('transaction_date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('transaction_date', '<=', $request->date_to));
    }

    private function ensurePending(CustomerTransaction $transaction): void
    {
        if ($transaction->status !== 'Pending') {
            abort(403, 'Only pending transactions can be changed.');
        }
    }

    private function generateControlNumber(): string
    {
        do {
            $number = 'PL-' . now()->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        } while (CustomerTransaction::where('control_number', $number)->exists());

        return $number;
    }
}
