<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Current recorded (system / POS) stock for the item.
            $table->unsignedInteger('quantity')->default(0)->after('unit');

            // When quantity falls to this level or below the item shows up in Replenishment.
            $table->unsignedInteger('reorder_level')->default(5)->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'reorder_level']);
        });
    }
};
