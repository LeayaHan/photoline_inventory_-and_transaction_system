<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->string('line_type', 20)->default('service')->after('transaction_id');
            $table->foreignId('inventory_item_id')->nullable()->after('line_type')->constrained('inventory_items')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('inventory_item_id');
            $table->dropColumn('line_type');
        });
    }
};
