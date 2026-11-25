<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@famfit.com'],
            [
                'name' => 'Administrador',
                'username' => 'admin', //
                'password' => Hash::make('Admin12345'),
                'role_id' => 1,
                'terms_accepted_at' => now(),
                'privacy_accepted_at' => now(),
            ]
        );
    }
}