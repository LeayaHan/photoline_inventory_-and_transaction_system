<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('customer_transactions', 'pos_reference_number')) {
            Schema::table('customer_transactions', function (Blueprint $table) {
                $table->dropColumn('pos_reference_number');
            });
        }

        if (Schema::hasColumn('transaction_details', 'serial_number')) {
            Schema::table('transaction_details', function (Blueprint $table) {
                $table->dropColumn('serial_number');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('customer_transactions', 'pos_reference_number')) {
            Schema::table('customer_transactions', function (Blueprint $table) {
                $table->string('pos_reference_number', 100)->nullable();
            });
        }

        if (!Schema::hasColumn('transaction_details', 'serial_number')) {
            Schema::table('transaction_details', function (Blueprint $table) {
                $table->string('serial_number', 100)->nullable();
            });
        }
    }
};
