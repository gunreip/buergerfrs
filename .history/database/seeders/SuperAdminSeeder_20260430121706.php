<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Passe die E-Mail-Adresse ggf. an
        $email = 'admin@example.com';
        $user = \App\Models\User::where('email', $email)->first();
        if ($user) {
            $user->assignRole('Super-Admin');
            $this->command->info("User {$user->email} wurde zum Super-Admin gemacht.");
        } else {
            $this->command->warn("Kein User mit E-Mail $email gefunden.");
        }
    }
}
