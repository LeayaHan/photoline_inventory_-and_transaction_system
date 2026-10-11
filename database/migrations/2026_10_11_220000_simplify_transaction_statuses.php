<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Transactions now only have three statuses: Pending, Completed and Voided.
     * Records saved with the old statuses are moved over so nothing is left in a status that no longer exists.
     */
    public function up(): void
    {
        // "In Progress" is gone. Stock was not deducted yet, so they go back to Pending.
        DB::table('customer_transactions')->where('status', 'In Progress')->update(['status' => 'Pending']);

        // "Claimed" is gone. They were already completed (stock already deducted).
        DB::table('customer_transactions')->where('status', 'Claimed')->update(['status' => 'Completed']);
    }

    public function down(): void
    {
        // nothing to undo
    }
};
