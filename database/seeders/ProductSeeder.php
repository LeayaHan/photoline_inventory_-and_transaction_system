<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'sku' => 'CAM-001',
                'product_name' => 'Digital Camera',
                'category' => 'Camera',
                'unit' => 'piece',
            ],
            [
                'sku' => 'BAT-001',
                'product_name' => 'Camera Battery',
                'category' => 'Battery',
                'unit' => 'piece',
            ],
            [
                'sku' => 'SD-001',
                'product_name' => 'SD Card 64GB',
                'category' => 'Memory Card',
                'unit' => 'piece',
            ],
            [
                'sku' => 'FRM-001',
                'product_name' => 'Photo Frame',
                'category' => 'Frame',
                'unit' => 'piece',
            ],
            [
                'sku' => 'ALB-001',
                'product_name' => 'Photo Album',
                'category' => 'Album',
                'unit' => 'piece',
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['sku' => $product['sku']],
                $product
            );
        }
    }
}