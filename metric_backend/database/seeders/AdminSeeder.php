<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * 2 admins de test (v1 beta — identifiants en clair dans le README main).
     */
    public function run(): void
    {
        $admins = [
            ['name' => 'Brecheyn', 'email' => 'admin@alodo.mpme', 'password' => 'AlodoAdmin2026'],
            ['name' => 'Demo', 'email' => 'demo@alodo.mpme', 'password' => 'DemoAdmin2026'],
        ];

        foreach ($admins as $a) {
            Admin::updateOrCreate(
                ['email' => $a['email']],
                ['name' => $a['name'], 'password' => Hash::make($a['password'])]
            );
        }
    }
}
