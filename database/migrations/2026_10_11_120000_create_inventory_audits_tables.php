<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_audits', function (Blueprint $table) {
            $table->id();
            $table->string('audit_number')->unique();
            $table->date('audit_date');
            $table->string('auditor_name');
            $table->unsignedBigInteger('staff_id')->nullable();
            $table->string('status')->default('draft');
            $table->text('remarks')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->unsignedBigInteger('finalized_by')->nullable();
            $table->timestamps();
            $table->index(['status', 'audit_date']);
        });

        Schema::create('inventory_audit_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_audit_id')->constrained('inventory_audits')->cascadeOnDelete();
            $table->unsignedBigInteger('inventory_item_id')->nullable();
            $table->string('item_code');
            $table->string('item_name');
            $table->string('unit', 30)->default('pcs');
            $table->unsignedInteger('recorded_quantity');
            $table->unsignedInteger('physical_quantity')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->index(['inventory_audit_id', 'item_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_audit_items');
        Schema::dropIfExists('inventory_audits');
    }
};
