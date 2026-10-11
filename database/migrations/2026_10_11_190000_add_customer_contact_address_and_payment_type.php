<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_transactions', 'customer_contact_number')) $table->string('customer_contact_number', 30)->nullable()->after('customer_name');
            if (!Schema::hasColumn('customer_transactions', 'customer_address')) $table->string('customer_address', 255)->nullable()->after('customer_contact_number');
            if (!Schema::hasColumn('customer_transactions', 'payment_type')) $table->string('payment_type', 30)->default('Cash')->after('deposit_amount');
        });
    }

    public function down(): void
    {
        Schema::table('customer_transactions', function (Blueprint $table) {
            $columns = array_filter(['customer_contact_number', 'customer_address', 'payment_type'], fn ($column) => Schema::hasColumn('customer_transactions', $column));
            if ($columns) $table->dropColumn($columns);
        });
    }
};
