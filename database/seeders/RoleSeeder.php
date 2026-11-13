<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Administrador',
            'Usuario',
           
        ];

        foreach ($roles as $rol) {
            Role::firstOrCreate([
                'name' => $rol,
            ]);
        }
    }
}