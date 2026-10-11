<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Keep transaction statuses extensible and normalize older case variants.
     * Voided records are preserved; this migration does not delete transactions.
     */
    public function up(): void
    {
        Schema::table('customer_transactions', function (Blueprint $table) {
            $table->string('status', 20)->default('Pending')->change();
        });

        DB::table('customer_transactions')
            ->whereRaw('LOWER(status) = ?', ['voided'])
            ->where('status', '!=', 'Voided')
            ->update(['status' => 'Voided']);

        DB::table('customer_transactions')
            ->whereRaw('LOWER(status) = ?', ['completed'])
            ->where('status', '!=', 'Completed')
            ->update(['status' => 'Completed']);

        DB::table('customer_transactions')
            ->whereRaw('LOWER(status) = ?', ['pending'])
            ->where('status', '!=', 'Pending')
            ->update(['status' => 'Pending']);
    }

    public function down(): void
    {
        // Intentionally keep the string column so saved statuses are never rejected.
    }
};
