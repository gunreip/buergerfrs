<?php

// php artisan db:seed --class=DatabaseSeeder

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $super = User::firstOrCreate([
            'email' => 'super@buergerfrs.test'
        ], [
            'name' => 'Super Admin',
            'password' => bcrypt('password12345'),
        ]);
        $admin = User::firstOrCreate([
            'email' => 'admin@buergerfrs.test'
        ], [
            'name' => 'Admin',
            'password' => bcrypt('password12345'),
        ]);
        $user = User::firstOrCreate([
            'email' => 'user@buergerfrs.test'
        ], [
            'name' => 'Normal User',
            'password' => bcrypt('password12345'),
        ]);

        // Rollen zuweisen
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(SuperAdminSeeder::class);
    }
}
