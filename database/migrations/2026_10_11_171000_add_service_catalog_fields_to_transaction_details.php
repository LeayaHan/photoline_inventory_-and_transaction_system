<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->string('service_size', 40)->nullable()->after('service_type');
            $table->string('print_layout', 40)->nullable()->after('service_size');
            $table->string('order_notes', 150)->nullable()->after('print_layout');
        });
    }

    public function down(): void
    {
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->dropColumn(['service_size', 'print_layout', 'order_notes']);
        });
    }
};
