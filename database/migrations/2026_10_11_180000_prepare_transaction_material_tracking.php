<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_transactions', function (Blueprint $table) {
            $table->string('status', 20)->default('Pending')->change();
        });

        Schema::table('service_material_rules', function (Blueprint $table) {
            $table->dropUnique('service_material_rules_service_name_unique');
            $table->unique(['service_name', 'inventory_item_id'], 'service_material_rules_service_item_unique');
        });

        Schema::create('transaction_material_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_transaction_id')->constrained('customer_transactions')->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->restrictOnDelete();
            $table->unsignedInteger('quantity_used');
            $table->timestamps();
            $table->unique(['customer_transaction_id', 'inventory_item_id'], 'transaction_material_usages_transaction_item_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_material_usages');

        Schema::table('service_material_rules', function (Blueprint $table) {
            $table->dropUnique('service_material_rules_service_item_unique');
            $table->unique('service_name', 'service_material_rules_service_name_unique');
        });

        Schema::table('customer_transactions', function (Blueprint $table) {
            $table->enum('status', ['Pending', 'Claimed', 'Voided'])->default('Pending')->change();
        });
    }
};
