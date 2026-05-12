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
        // Create default admin user
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@preeklampsia.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create sample doctor
        User::create([
            'name' => 'Dr. Sarah Johnson',
            'username' => 'dr.sarah',
            'email' => 'sarah@preeklampsia.com',
            'password' => Hash::make('password'),
            'role' => 'doctor',
        ]);

        // Create sample nurse
        User::create([
            'name' => 'Nurse Maria',
            'username' => 'nurse.maria',
            'email' => 'maria@preeklampsia.com',
            'password' => Hash::make('password'),
            'role' => 'nurse',
        ]);

        $this->command->info('Default users created successfully!');
        $this->command->info('Admin - username: admin, password: password');
        $this->command->info('Doctor - username: dr.sarah, password: password');
        $this->command->info('Nurse - username: nurse.maria, password: password');
    }
}
