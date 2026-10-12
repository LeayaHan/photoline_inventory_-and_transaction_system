<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ManagerSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'manager@123',
            ],
            [
                'name' => 'Photoline Manager',
                'password' => Hash::make('password123'),
                'role' => 'manager',
            ]
        );
    }
}