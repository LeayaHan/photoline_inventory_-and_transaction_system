<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('control_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        $transactions = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('staff.transactions.index', compact('transactions'));
    }

    public function create()
    {
        $formToken = (string) Str::uuid();

        session()->put("transaction_form_tokens.{$formToken}", true);

        return view('staff.transactions.create', compact('formToken'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'form_token' => ['required', 'string', 'uuid'],
            'customer_name' => ['required', 'string', 'max:255'],
            'service_type' => ['required', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
            'item_description' => ['nullable', 'string'],
        ]);

        $tokenKey = "transaction_form_tokens.{$validated['form_token']}";

        if (! session()->pull($tokenKey, false)) {
            return redirect()
                ->route('transactions.index')
                ->with('error', 'This transaction form was already submitted.');
        }

        do {
            $controlNumber = 'TRX-' . strtoupper(Str::random(8));
        } while (Transaction::where('control_number', $controlNumber)->exists());

        Transaction::create([
            'control_number' => $controlNumber,
            'customer_name' => $validated['customer_name'],
            'service_type' => $validated['service_type'],
            'transaction_date' => $validated['transaction_date'],
            'item_description' => $validated['item_description'] ?? null,
            'status' => 'Pending',
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaction created successfully.');
    }

    public function show(Transaction $transaction)
    {
        return view('staff.transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        return view('staff.transactions.edit', compact('transaction'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'service_type' => ['required', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
            'item_description' => ['nullable', 'string'],
            'status' => ['required', 'in:Pending,Claimed,Voided'],
        ]);

        $transaction->update($validated);

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('success', 'Transaction updated successfully.');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->update(['status' => 'Voided']);

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaction has been voided.');
    }
}
