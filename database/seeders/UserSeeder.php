<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = (string) config('auth.seed_password', 'password');

        $users = [
            [
                'name' => 'Fathin Zulian Tsany',
                'username' => '22071998',
                'email' => 'superadmin@laporanpol.test',
                'role' => UserRole::SuperAdmin,
            ],
            [
                'name' => 'Admin',
                'username' => '96000002',
                'email' => 'admin@laporanpol.test',
                'role' => UserRole::Admin,
            ],
            [
                'name' => 'User',
                'username' => '96000003',
                'email' => 'user@laporanpol.test',
                'role' => UserRole::User,
            ],
        ];

        foreach ($users as $data) {
            $user = User::query()
                ->where('username', $data['username'])
                ->orWhere('email', $data['email'])
                ->first();

            if ($user === null) {
                $user = User::query()->create([
                    'name' => $data['name'],
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'password' => $password,
                    'email_verified_at' => now(),
                ]);
            } else {
                $user->update([
                    'name' => $data['name'],
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'password' => $password,
                    'email_verified_at' => now(),
                ]);
            }

            $user->syncRoles([$data['role']->value]);
        }
    }
}
