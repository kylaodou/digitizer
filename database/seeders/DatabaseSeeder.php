<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a test user
        User::create([
            'name' => 'Utilisateur Test',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'phone' => '+226 70 00 00 00',
            'organization' => 'Entreprise Test',
            'user_type' => 'enterprise',
            'is_verified' => true,
            'account_balance' => 50000, // 50,000 FCFA
        ]);

        // Create additional test users
        User::create([
            'name' => 'Service Public Test',
            'email' => 'service@gouv.bf',
            'password' => Hash::make('password'),
            'phone' => '+226 25 00 00 00',
            'organization' => 'Ministère de la Digitalisation',
            'user_type' => 'public_service',
            'is_verified' => true,
            'account_balance' => 100000, // 100,000 FCFA
        ]);

        User::create([
            'name' => 'Particulier Test',
            'email' => 'particulier@test.bf',
            'password' => Hash::make('password'),
            'phone' => '+226 60 00 00 00',
            'user_type' => 'individual',
            'is_verified' => true,
            'account_balance' => 10000, // 10,000 FCFA
        ]);
    }
}
