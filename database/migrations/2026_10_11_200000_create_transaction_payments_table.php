<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_transaction_id')->constrained('customer_transactions')->cascadeOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->enum('payment_type', ['Cash', 'GCash', 'Card']);
            $table->timestamp('paid_at');
            $table->boolean('is_initial')->default(false);
            $table->timestamps();
            $table->index(['customer_transaction_id', 'paid_at']);
        });

        DB::table('customer_transactions')
            ->where('deposit_amount', '>', 0)
            ->orderBy('id')
            ->get(['id', 'staff_id', 'deposit_amount', 'payment_type', 'created_at', 'updated_at'])
            ->each(function ($transaction) {
                DB::table('transaction_payments')->insert([
                    'customer_transaction_id' => $transaction->id,
                    'staff_id' => $transaction->staff_id,
                    'amount' => $transaction->deposit_amount,
                    'payment_type' => in_array($transaction->payment_type, ['Cash', 'GCash', 'Card'], true) ? $transaction->payment_type : 'Cash',
                    'paid_at' => $transaction->created_at ?? now(),
                    'is_initial' => true,
                    'created_at' => $transaction->created_at ?? now(),
                    'updated_at' => $transaction->updated_at ?? now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_payments');
    }
};
