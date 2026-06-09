<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Bidan. Eli',
                'username' => 'bidan.eli',
                'email' => 'eli@dinkes-bogor.go.id',
                'password' => Hash::make('password'),
                'role' => 'dinas',
                'puskesmas' => null,
            ],
            [
                'name' => 'Almira',
                'username' => 'almira',
                'email' => 'almira@puskesmas-puloarmyn.go.id',
                'password' => Hash::make('password'),
                'role' => 'puskesmas',
                'puskesmas' => User::PUSKESMAS_PULO_ARMYN,
            ],
            [
                'name' => 'Dr. Renna',
                'username' => 'dr.renna',
                'email' => 'renna@puskesmas-pancasan.go.id',
                'password' => Hash::make('password'),
                'role' => 'puskesmas',
                'puskesmas' => User::PUSKESMAS_PANCASAN,
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['username' => $data['username']],
                $data
            );
        }

        $this->command->info('User Dinas & Puskesmas berhasil dibuat.');
        $this->command->info('Login: bidan.eli | almira | dr.renna — password: password');
    }
}
