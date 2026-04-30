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
        // Weise super@buergerfrs.test die Super-Admin-Rolle zu
        $super = \App\Models\User::where('email', 'super@buergerfrs.test')->first();
        if ($super) {
            $super->assignRole('Super-Admin');
            $this->command->info("User {$super->email} wurde zum Super-Admin gemacht.");
        } else {
            $this->command->warn("Kein User mit E-Mail super@buergerfrs.test gefunden.");
        }

        // Weise admin@buergerfrs.test die Admin-Rolle zu
        $admin = \App\Models\User::where('email', 'admin@buergerfrs.test')->first();
        if ($admin) {
            $admin->assignRole('Admin');
            $this->command->info("User {$admin->email} wurde zum Admin gemacht.");
        } else {
            $this->command->warn("Kein User mit E-Mail admin@buergerfrs.test gefunden.");
        }

        // Weise user@buergerfrs.test die User-Rolle zu
        $user = \App\Models\User::where('email', 'user@buergerfrs.test')->first();
        if ($user) {
            $user->assignRole('User');
            $this->command->info("User {$user->email} wurde zum User gemacht.");
        } else {
            $this->command->warn("Kein User mit E-Mail user@buergerfrs.test gefunden.");
        }
    }
}
