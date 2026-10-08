<?php

namespace App\Http\Controllers;

use App\Models\AuditDetail;
use App\Models\InventoryAudit;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class InventoryAuditController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryAudit::with('user');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereDate('audit_date', $search)
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $audits = $query->latest()->paginate(10)->withQueryString();

        return view('staff.audits.index', compact('audits'));
    }

    public function create()
    {
        $products = Product::orderBy('product_name')->get();
        $formToken = (string) Str::uuid();

        session()->put("audit_form_tokens.{$formToken}", true);

        return view('staff.audits.create', compact('products', 'formToken'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'form_token' => ['required', 'string', 'uuid'],
            'audit_date' => ['required', 'date'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'exists:products,id'],
            'products.*.counted_qty' => ['required', 'integer', 'min:0'],
        ]);

        $tokenKey = "audit_form_tokens.{$validated['form_token']}";

        if (! session()->pull($tokenKey, false)) {
            return redirect()
                ->route('audits.index')
                ->with('error', 'This audit form was already submitted.');
        }

        $audit = DB::transaction(function () use ($validated) {
            $audit = InventoryAudit::create([
                'user_id' => auth()->id(),
                'audit_date' => $validated['audit_date'],
                'status' => 'Ongoing',
            ]);

            foreach ($validated['products'] as $product) {
                $inventoryItem = Product::findOrFail($product['product_id']);
                $recordedQty = (int) $inventoryItem->quantity;

                AuditDetail::create([
                    'inventory_audit_id' => $audit->id,
                    'product_id' => $inventoryItem->id,
                    'recorded_qty' => $recordedQty,
                    'counted_qty' => $product['counted_qty'],
                    'discrepancy' => $product['counted_qty'] - $recordedQty,
                ]);
            }

            return $audit;
        });

        return redirect()
            ->route('audits.show', $audit)
            ->with('success', 'Inventory audit created successfully.');
    }

    public function show(InventoryAudit $audit)
    {
        $audit->load(['user', 'details.product']);

        return view('staff.audits.show', compact('audit'));
    }

    public function edit(InventoryAudit $audit)
    {
        $audit->load('details.product');

        return view('staff.audits.edit', compact('audit'));
    }

    public function update(Request $request, InventoryAudit $audit)
    {
        abort_if($audit->status === 'Completed', 403, 'Completed audits cannot be edited.');

        $validated = $request->validate([
            'audit_date' => ['required', 'date'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.detail_id' => ['required', 'exists:audit_details,id'],
            'products.*.counted_qty' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($validated, $audit) {
            $audit->update(['audit_date' => $validated['audit_date']]);

            foreach ($validated['products'] as $product) {
                $detail = AuditDetail::where('id', $product['detail_id'])
                    ->where('inventory_audit_id', $audit->id)
                    ->firstOrFail();

                $detail->update([
                    'counted_qty' => $product['counted_qty'],
                    'discrepancy' => $product['counted_qty'] - $detail->recorded_qty,
                ]);
            }
        });

        return redirect()
            ->route('audits.show', $audit)
            ->with('success', 'Inventory audit updated successfully.');
    }

    public function complete(InventoryAudit $audit)
    {
        if ($audit->status === 'Completed') {
            return redirect()
                ->route('audits.show', $audit)
                ->with('success', 'This audit is already completed.');
        }

        DB::transaction(function () use ($audit) {
            $audit->load('details');

            foreach ($audit->details as $detail) {
                Product::whereKey($detail->product_id)->update([
                    'quantity' => $detail->counted_qty,
                ]);
            }

            $audit->update(['status' => 'Completed']);
        });

        return redirect()
            ->route('audits.show', $audit)
            ->with('success', 'Inventory audit marked as completed.');
    }
}
