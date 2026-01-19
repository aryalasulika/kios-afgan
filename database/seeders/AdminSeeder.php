<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'username' => 'bundahara',
                'password' => 'R4yan1234',
            ],
            [
                'username' => 'pakbos',
                'password' => 'P4k4arman',
            ],
            [
                'username' => 'arya',
                'password' => 'arya2828',
            ],
            [
                'username' => 'admin',
                'password' => 'password',
            ],
        ];

        foreach ($admins as $admin) {
            User::firstOrCreate(
                ['username' => $admin['username']],
                [
                    'password' => Hash::make($admin['password']),
                    'role' => 'admin',
                    'is_active' => true,
                ]
            );
        }
    }
}
