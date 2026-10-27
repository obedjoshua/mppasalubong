<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed default accounts for offline/online use
        $accounts = [
            // Regular User Account
            [
                'name' => 'Demo Customer',
                'email' => 'customer@demo.local',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'email_verified_at' => now(),
                'phone' => '+639000000001',
                'selected_frame' => 'frame_reg01',
            ],
            // Staff Account
            [
                'name' => 'Main Shop Staff',
                'email' => 'staff@demo.local',
                'password' => Hash::make('staff12345'),
                'role' => 'staff',
                'branch' => 'main_shop',
                'email_verified_at' => now(),
                'phone' => '+639000000002',
                'selected_frame' => 'frame_reg01',
            ],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                $account
            );
        }

        // Call other seeders
        $this->call([
            BadgeSeeder::class,
            StaffUserSeeder::class,
            PortalAccountSeeder::class,
        ]);
    }
}
