<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /** Sample items so there is something to try. Safe to re-run. */
    public function run(): void
    {
        $items = [
            // type, name, category, unit, quantity, reorder level, price
            ['merchandise', 'Compact Digital Camera', 'Cameras', 'pcs', 5, 2, 12500],
            ['merchandise', 'Camera Battery NP-FW50', 'Camera Batteries', 'pcs', 10, 4, 1200],
            ['merchandise', 'SD Card 64GB', 'Memory Cards', 'pcs', 6, 3, 450],
            ['merchandise', 'Wooden Frame 8x10', 'Frames', 'pcs', 15, 5, 180],
            ['merchandise', 'Photo Album 4R (200 slots)', 'Albums', 'pcs', 9, 4, 295],

            ['raw_material', '4R Glossy Photo Paper (100 sheets)', 'Photo Paper', 'packs', 24, 8, 320],
            ['raw_material', '5R Glossy Photo Paper (100 sheets)', 'Photo Paper', 'packs', 4, 8, 420],
            ['raw_material', 'A4 Matte Photo Paper (50 sheets)', 'Photo Paper', 'packs', 0, 5, 380],
            ['raw_material', 'Black Ink Bottle 100ml', 'Ink & Toner', 'bottles', 12, 6, 250],
            ['raw_material', 'Photo Envelope 4R', 'Packaging', 'pcs', 150, 50, 3.5],
        ];

        foreach ($items as [$type, $name, $category, $unit, $qty, $reorder, $price]) {
            // skip anything that's already there
            if (InventoryItem::where('name', $name)->exists()) {
                continue;
            }

            InventoryItem::createWithCode([
                'item_type' => $type,
                'name' => $name,
                'category' => $category,
                'unit' => $unit,
                'quantity' => $qty,
                'reorder_level' => $reorder,
                'unit_price' => $price,
            ]);
        }
    }
}
