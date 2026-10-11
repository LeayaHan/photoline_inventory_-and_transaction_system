<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('customer_transactions')) {
            if (! Schema::hasColumn('customer_transactions', 'completed_at')) {
                Schema::table('customer_transactions', function (Blueprint $table) {
                    $table->timestamp('completed_at')->nullable();
                });
            }
            if (! Schema::hasColumn('customer_transactions', 'stock_deducted_at')) {
                Schema::table('customer_transactions', function (Blueprint $table) {
                    $table->timestamp('stock_deducted_at')->nullable();
                });
            }
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE customer_transactions MODIFY status ENUM('Pending','Completed','Claimed','Voided') NOT NULL DEFAULT 'Pending'");
            }
        }

        if (Schema::hasTable('inventory_audit_items') && ! Schema::hasColumn('inventory_audit_items', 'counted_at')) {
            Schema::table('inventory_audit_items', function (Blueprint $table) {
                $table->timestamp('counted_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('inventory_audit_items') && Schema::hasColumn('inventory_audit_items', 'counted_at')) {
            Schema::table('inventory_audit_items', function (Blueprint $table) {
                $table->dropColumn('counted_at');
            });
        }
        if (Schema::hasTable('customer_transactions')) {
            if (Schema::hasColumn('customer_transactions', 'stock_deducted_at')) {
                Schema::table('customer_transactions', function (Blueprint $table) {
                    $table->dropColumn('stock_deducted_at');
                });
            }
            if (Schema::hasColumn('customer_transactions', 'completed_at')) {
                Schema::table('customer_transactions', function (Blueprint $table) {
                    $table->dropColumn('completed_at');
                });
            }
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE customer_transactions MODIFY status ENUM('Pending','Claimed','Voided') NOT NULL DEFAULT 'Pending'");
            }
        }
    }
};
