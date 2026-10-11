<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('inventory_audits') && ! Schema::hasColumn('inventory_audits', 'staff_id')) {
            Schema::table('inventory_audits', function (Blueprint $table) {
                $table->unsignedBigInteger('staff_id')->nullable()->after('auditor_name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('inventory_audits') && Schema::hasColumn('inventory_audits', 'staff_id')) {
            Schema::table('inventory_audits', function (Blueprint $table) {
                $table->dropColumn('staff_id');
            });
        }
    }
};
