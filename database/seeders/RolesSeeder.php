<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['name' => 'docente']);
        Role::create(['name' => 'director']);
        Role::create(['name' => 'padre']);
        Role::create(['name' => 'alumno']);
    }
}