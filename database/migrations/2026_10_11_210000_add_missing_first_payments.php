<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Older transactions saved the "amount paid now" on the transaction itself and never
     * created a payment record, so they showed as Unpaid. This gives each of them the
     * missing first payment. Transactions that already have payments are left alone.
     */
    public function up(): void
    {
        $transactions = DB::table('customer_transactions')
            ->where('deposit_amount', '>', 0)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('transaction_payments')
                    ->whereColumn('transaction_payments.customer_transaction_id', 'customer_transactions.id');
            })
            ->get();

        foreach ($transactions as $transaction) {
            DB::table('transaction_payments')->insert([
                'customer_transaction_id' => $transaction->id,
                'staff_id' => $transaction->staff_id,
                'amount' => $transaction->deposit_amount,
                'payment_type' => in_array($transaction->payment_type, ['Cash', 'GCash', 'Card']) ? $transaction->payment_type : 'Cash',
                'paid_at' => $transaction->created_at ?? now(),
                'is_initial' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // nothing to undo: the payment records are real data
    }
};
