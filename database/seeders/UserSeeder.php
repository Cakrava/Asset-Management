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
            'name' => 'Admin',
            'email' => 'admin@diskominfo.id',
            'password' => Hash::make('admin12345'),
            'confirm_password' => Hash::make('admin12345'), // hanya kalau dipakai
            'role' => 'admin',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Master',
            'email' => 'master@diskominfo.id',
            'password' => Hash::make('master12345'),
            'confirm_password' => Hash::make('master12345'), // hanya kalau dipakai
            'role' => 'master',
            'status' => 'active',
        ]);
    }
}
