<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_transactions', function (Blueprint $table) {
            $table->string('pos_reference_number', 100)->nullable()->after('customer_name');
        });

        Schema::table('transaction_details', function (Blueprint $table) {
            $table->string('serial_number', 100)->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->dropColumn('serial_number');
        });

        Schema::table('customer_transactions', function (Blueprint $table) {
            $table->dropColumn('pos_reference_number');
        });
    }
};
