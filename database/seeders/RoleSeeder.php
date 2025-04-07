<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin Role and assign all permissions
        Role::create(['name' => 'super_admin']);
        // Create Member Role
        Role::create(['name' => 'membre']);
        // Create President Role
        Role::create(['name' => 'president']);

    }
}
