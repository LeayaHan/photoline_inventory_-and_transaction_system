<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remember which staff member voided a transaction and when.
     * (The void reason was already saved in the void_reason column.)
     */
    public function up(): void
    {
        Schema::table('customer_transactions', function (Blueprint $table) {
            $table->foreignId('voided_by_staff_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->timestamp('voided_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('customer_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('voided_by_staff_id');
            $table->dropColumn('voided_at');
        });
    }
};
