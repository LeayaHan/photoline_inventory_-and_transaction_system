<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    /**
     * Display all transactions.
     */
    public function index(Request $request)
    {
        $query = Transaction::query();

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'control_number',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'customer_name',
                    'like',
                    "%{$search}%"
                );
            });
        }

        $transactions = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'transactions.index',
            compact('transactions')
        );
    }

    /**
     * Show create transaction form.
     */
    public function create()
    {
        return view('transactions.create');
    }

    /**
     * Store a new transaction.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => [
                'required',
                'string',
                'max:255',
            ],

            'service_type' => [
                'required',
                'string',
                'max:255',
            ],

            'transaction_date' => [
                'required',
                'date',
            ],

            'item_description' => [
                'nullable',
                'string',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Generate unique control number
        |--------------------------------------------------------------------------
        */

        do {

            $controlNumber =
                'TRX-' . strtoupper(Str::random(8));

        } while (
            Transaction::where(
                'control_number',
                $controlNumber
            )->exists()
        );

        $validated['control_number'] = $controlNumber;

        $validated['status'] = 'Pending';

        $validated['created_by'] = auth()->id();

        Transaction::create($validated);

        return redirect()
            ->route('transactions.index')
            ->with(
                'success',
                'Transaction created successfully.'
            );
    }

    /**
     * Display one transaction.
     */
    public function show(Transaction $transaction)
    {
        return view(
            'transactions.show',
            compact('transaction')
        );
    }

    /**
     * Show edit form.
     */
    public function edit(Transaction $transaction)
    {
        return view(
            'transactions.edit',
            compact('transaction')
        );
    }

    /**
     * Update transaction.
     */
    public function update(
        Request $request,
        Transaction $transaction
    ) {
        $validated = $request->validate([
            'customer_name' => [
                'required',
                'string',
                'max:255',
            ],

            'service_type' => [
                'required',
                'string',
                'max:255',
            ],

            'transaction_date' => [
                'required',
                'date',
            ],

            'item_description' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                'in:Pending,Claimed,Voided',
            ],
        ]);

        $transaction->update($validated);

        return redirect()
            ->route(
                'transactions.show',
                $transaction
            )
            ->with(
                'success',
                'Transaction updated successfully.'
            );
    }

    /**
     * Void transaction.
     */
    public function destroy(Transaction $transaction)
    {
        /*
        |--------------------------------------------------------------------------
        | We do not actually delete the record.
        | We change its status to Voided so history remains.
        |--------------------------------------------------------------------------
        */

        $transaction->update([
            'status' => 'Voided',
        ]);

        return redirect()
            ->route('transactions.index')
            ->with(
                'success',
                'Transaction has been voided.'
            );
    }
}