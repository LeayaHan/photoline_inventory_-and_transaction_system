<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Branch manager account
        User::updateOrCreate(
            ['email' => 'photolinemanager@gmail.com'],
            [
                'name' => 'Photoline Manager',
                'password' => Hash::make('photoline@123'),
            ]
        );

        // Sample inventory (optional - remove this line if you don't want it)
        $this->call(InventorySeeder::class);
    }
}
