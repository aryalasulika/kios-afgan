<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'username' => 'bundahara',
            'password' => Hash::make('R4yan1234'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'username' => 'pakbos',
            'password' => Hash::make('P4k4arman'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'username' => 'arya',
            'password' => Hash::make('arya2828'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Kasir
        User::create([
            'username' => 'kasir',
            'pin' => Hash::make('1234'),
            'role' => 'kasir',
            'is_active' => true,
        ]);

        // Products
        $products = [
            ['name' => 'Indomie Goreng', 'barcode' => '8998866123456', 'price' => 3500, 'stock' => 100],
            ['name' => 'Aqua 600ml', 'barcode' => '8991234567890', 'price' => 4000, 'stock' => 50],
            ['name' => 'Teh Botol Sosro', 'barcode' => '8990987654321', 'price' => 5000, 'stock' => 50],
            ['name' => 'Kopi Kapal Api', 'barcode' => '8995555555555', 'price' => 1500, 'stock' => 200],
            ['name' => 'Telur Ayam (kg)', 'barcode' => '10001', 'price' => 28000, 'stock' => 20],
            ['name' => 'Gula Pasir (kg)', 'barcode' => '10002', 'price' => 14000, 'stock' => 30],
            ['name' => 'Minyak Goreng 1L', 'barcode' => '10003', 'price' => 16000, 'stock' => 15],
            ['name' => 'Beras (5kg)', 'barcode' => '10004', 'price' => 65000, 'stock' => 10],
            ['name' => 'Sabun Lifebuoy', 'barcode' => '8997777777777', 'price' => 4500, 'stock' => 40],
            ['name' => 'Sampo Sunsilk', 'barcode' => '8998888888888', 'price' => 12000, 'stock' => 25],
        ];

        foreach ($products as $p) {
            Product::create($p);
        }
    }
}
