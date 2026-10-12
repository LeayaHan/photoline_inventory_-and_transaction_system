<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('audit_details')) {
            Schema::create('audit_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inventory_audit_id')->constrained('inventory_audits')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products');
                $table->integer('recorded_qty');
                $table->integer('counted_qty');
                $table->integer('discrepancy');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Intentionally do not drop this table: it may have existed before this migration was recorded.
    }
};
