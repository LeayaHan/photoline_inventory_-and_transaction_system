<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('customer_transactions', 'voided_by_staff_id')) {
            Schema::table('customer_transactions', function (Blueprint $table) {
                $table->foreignId('voided_by_staff_id')->nullable()->constrained('staff')->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('customer_transactions', 'voided_at')) {
            Schema::table('customer_transactions', function (Blueprint $table) {
                $table->timestamp('voided_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('customer_transactions', 'voided_by_staff_id')) {
            Schema::table('customer_transactions', function (Blueprint $table) {
                $table->dropConstrainedForeignId('voided_by_staff_id');
            });
        }

        if (Schema::hasColumn('customer_transactions', 'voided_at')) {
            Schema::table('customer_transactions', function (Blueprint $table) {
                $table->dropColumn('voided_at');
            });
        }
    }
};
