<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Roles
        foreach (['Admin', 'HR', 'Karyawan'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Users
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('Admin');

        $hr = User::factory()->create([
            'name' => 'HR',
            'email' => 'hr@example.com',
            'password' => bcrypt('password'),
        ]);
        $hr->assignRole('HR');

        $emp = User::factory()->create([
            'name' => 'Karyawan Satu',
            'email' => 'karyawan1@example.com',
            'password' => bcrypt('password'),
        ]);
        $emp->assignRole('Karyawan');
    }
}
