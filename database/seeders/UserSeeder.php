<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'email' => 'admin@diskominfo.id',
            'password' => Hash::make('admin12345'),
            'confirm_password' => Hash::make('admin12345'), // hanya kalau dipakai
            'role' => 'admin',
            'status' => 'active',
        ]);

        User::create([
            'email' => 'master@diskominfo.id',
            'password' => Hash::make('master12345'),
            'confirm_password' => Hash::make('master12345'), // hanya kalau dipakai
            'role' => 'master',
            'status' => 'active',
        ]);
    }
}
