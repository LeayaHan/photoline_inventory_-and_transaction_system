<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('inventory_audits')) {
            Schema::table('inventory_audits', function (Blueprint $table) {
                if (!Schema::hasColumn('inventory_audits', 'staff_id')) {
                    $table->unsignedBigInteger('staff_id')->nullable();
                }
                if (!Schema::hasColumn('inventory_audits', 'status')) {
                    $table->string('status')->default('finalized');
                }
                if (!Schema::hasColumn('inventory_audits', 'finalized_at')) {
                    $table->timestamp('finalized_at')->nullable();
                }
                if (!Schema::hasColumn('inventory_audits', 'finalized_by')) {
                    $table->unsignedBigInteger('finalized_by')->nullable();
                }
            });
        }

        if (Schema::hasTable('inventory_audit_items') && !Schema::hasColumn('inventory_audit_items', 'physical_quantity')) {
            Schema::table('inventory_audit_items', function (Blueprint $table) {
                $table->unsignedInteger('physical_quantity')->nullable();
            });

            if (Schema::hasColumn('inventory_audit_items', 'counted_quantity')) {
                \Illuminate\Support\Facades\DB::table('inventory_audit_items')->update([
                    'physical_quantity' => \Illuminate\Support\Facades\DB::raw('counted_quantity'),
                ]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('inventory_audit_items') && Schema::hasColumn('inventory_audit_items', 'physical_quantity')) {
            Schema::table('inventory_audit_items', function (Blueprint $table) {
                $table->dropColumn('physical_quantity');
            });
        }

        if (Schema::hasTable('inventory_audits')) {
            Schema::table('inventory_audits', function (Blueprint $table) {
                foreach (['staff_id', 'status', 'finalized_at', 'finalized_by'] as $column) {
                    if (Schema::hasColumn('inventory_audits', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
