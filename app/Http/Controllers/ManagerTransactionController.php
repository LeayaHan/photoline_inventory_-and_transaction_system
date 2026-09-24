<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class ManagerTransactionController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->isManager()) {
            abort(403, 'Unauthorized access.');
        }

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

        return view(
            'manager.transactions.index',
            compact('transactions')
        );
    }

    public function show(Transaction $transaction)
    {
        if (!auth()->user()->isManager()) {
            abort(403, 'Unauthorized access.');
        }

        return view(
            'manager.transactions.show',
            compact('transaction')
        );
    }
}   