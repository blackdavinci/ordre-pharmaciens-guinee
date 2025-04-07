<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@ordrepharmaciens.gn'],
            [
                'prenom' => 'Alfred',
                'nom' => 'Moran',
                'password' => bcrypt('supersecure'), // 🔐 à personnaliser
                'email_verified_at' => now(),
                'statut' => true,
            ]
        );

        $role = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->assignRole($role);
    }
}
