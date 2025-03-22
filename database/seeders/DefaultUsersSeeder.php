<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DefaultUsersSeeder extends Seeder
{
    public function run()
    {
        // Utilisateur pour le module Inscription
        if (!User::where('email', 'inscription@synchrogest.edu')->exists()) {
            User::create([
                'name' => 'Module Inscription',
                'email' => 'inscription@synchrogest.edu',
                'password' => Hash::make('inscription123'),
                'profil' => 'inscription',
            ]);
        }

        // Utilisateur pour le module Finance
        if (!User::where('email', 'finance@synchrogest.edu')->exists()) {
            User::create([
                'name' => 'Module Finance',
                'email' => 'finance@synchrogest.edu',
                'password' => Hash::make('finance123'),
                'profil' => 'finance',
            ]);
        }

        // Utilisateur pour le module Matière
        if (!User::where('email', 'matiere@synchrogest.edu')->exists()) {
            User::create([
                'name' => 'Module Matière',
                'email' => 'matiere@synchrogest.edu',
                'password' => Hash::make('matiere123'),
                'profil' => 'matiere',
            ]);
        }
    }
}