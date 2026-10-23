<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffAccounts = [
            [
                'name' => 'Main Shop Staff',
                'email' => 'cakesbymasaganangpagpapala@gmail.com',
                'password' => Hash::make('MasaganaStaff2026!'),
                'role' => 'staff',
                'branch' => 'main_shop',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($staffAccounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                $account
            );
        }
    }
}
