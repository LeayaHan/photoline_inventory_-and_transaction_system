<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            // merchandise = sold to customers, raw_material = used up for printing/services
            $table->string('item_type', 20)->default('merchandise')->after('item_code');
        });

        // Items already in the database: move the supplies over to raw materials
        DB::table('inventory_items')
            ->whereIn('category', ['Photo Paper', 'Ink & Toner', 'Packaging'])
            ->update(['item_type' => 'raw_material']);

        // The old "Equipment" category is now called "Cameras"
        DB::table('inventory_items')
            ->where('category', 'Equipment')
            ->update(['category' => 'Cameras']);

        // Give the old ITM-000001 codes their new MER-/RAW- prefix
        foreach (DB::table('inventory_items')->get() as $item) {
            $prefix = $item->item_type === 'raw_material' ? 'RAW' : 'MER';

            DB::table('inventory_items')->where('id', $item->id)->update([
                'item_code' => $prefix . '-' . str_pad((string) $item->id, 6, '0', STR_PAD_LEFT),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropColumn('item_type');
        });
    }
};
