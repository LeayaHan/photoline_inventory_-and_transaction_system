<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('inventory_audits')) {
            Schema::table('inventory_audits', function (Blueprint $table) {
                if (! Schema::hasColumn('inventory_audits', 'staff_id')) {
                    $table->unsignedBigInteger('staff_id')->nullable();
                }
                if (! Schema::hasColumn('inventory_audits', 'status')) {
                    $table->string('status')->default('draft');
                }
                if (! Schema::hasColumn('inventory_audits', 'finalized_at')) {
                    $table->timestamp('finalized_at')->nullable();
                }
                if (! Schema::hasColumn('inventory_audits', 'finalized_by')) {
                    $table->unsignedBigInteger('finalized_by')->nullable();
                }
            });
        }

        if (Schema::hasTable('inventory_audit_items')) {
            Schema::table('inventory_audit_items', function (Blueprint $table) {
                if (! Schema::hasColumn('inventory_audit_items', 'physical_quantity')) {
                    $table->unsignedInteger('physical_quantity')->nullable();
                }
                if (! Schema::hasColumn('inventory_audit_items', 'counted_quantity')) {
                    $table->unsignedInteger('counted_quantity')->nullable();
                }
                if (! Schema::hasColumn('inventory_audit_items', 'discrepancy')) {
                    $table->integer('discrepancy')->default(0);
                }
                if (! Schema::hasColumn('inventory_audit_items', 'status')) {
                    $table->string('status')->default('Not Counted');
                }
            });

            if (Schema::hasColumn('inventory_audit_items', 'counted_quantity') && Schema::hasColumn('inventory_audit_items', 'physical_quantity')) {
                DB::table('inventory_audit_items')
                    ->whereNull('physical_quantity')
                    ->where('counted_quantity', '>', 0)
                    ->update(['physical_quantity' => DB::raw('counted_quantity')]);
            }
        }
    }

    public function down(): void
    {
        // Keep added fields to protect existing audit data.
    }
};
