<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PortalAccountSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['admin', 'developer'] as $role) {
            $email = config("portals.{$role}.email");
            $password = config("portals.{$role}.seed_password");

            if (! is_string($email) || $email === '' || ! is_string($password) || $password === '') {
                logger()->warning("The {$role} portal account was not seeded; configure its email and seed password.");

                continue;
            }

            User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => ucfirst($role),
                    'password' => Hash::make($password),
                    'role' => $role,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
