<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            // ==========================================
            // CUSTOM ORDER MILESTONES (1, 5, 10, 20, 50, 100)
            // ==========================================
            [
                'name' => 'First Bite',
                'slug' => 'order_1',
                'icon_path' => 'images/badges/order_1.png',
                'description' => 'Placed your very first custom order!',
                'order_requirement' => 1,
            ],
            [
                'name' => 'Loaf Lover',
                'slug' => 'order_5',
                'icon_path' => 'images/badges/order_5.png',
                'description' => 'Completed 5 custom orders.',
                'order_requirement' => 5,
            ],
            [
                'name' => 'Bake Enthusiast',
                'slug' => 'order_10',
                'icon_path' => 'images/badges/order_10.png',
                'description' => 'Completed 10 custom orders.',
                'order_requirement' => 10,
            ],
            [
                'name' => 'Pastry Prodigy',
                'slug' => 'order_20',
                'icon_path' => 'images/badges/order_20.png',
                'description' => 'Completed 20 custom orders.',
                'order_requirement' => 20,
            ],
            [
                'name' => 'Dessert Master',
                'slug' => 'order_50',
                'icon_path' => 'images/badges/order_50.png',
                'description' => 'Completed 50 custom orders.',
                'order_requirement' => 50,
            ],
            [
                'name' => 'VIP Connoisseur',
                'slug' => 'order_100',
                'icon_path' => 'images/badges/order_100.png',
                'description' => 'Completed 100 custom orders. Legendary loyalty!',
                'order_requirement' => 100,
            ],

            // ==========================================
            // SHOP PURCHASE MILESTONES (10, 50, 100)
            // ==========================================
            [
                'name' => 'Shop Hopper',
                'slug' => 'shop_10',
                'icon_path' => 'images/badges/shop_10.png',
                'description' => 'Completed 10 shop purchases!',
                'order_requirement' => 0,
            ],
            [
                'name' => 'Cart Collector',
                'slug' => 'shop_50',
                'icon_path' => 'images/badges/shop_50.png',
                'description' => 'Completed 50 shop purchases!',
                'order_requirement' => 0,
            ],
            [
                'name' => 'Shopaholic VIP',
                'slug' => 'shop_100',
                'icon_path' => 'images/badges/shop_100.png',
                'description' => 'Completed 100 shop purchases. Ultimate shop master!',
                'order_requirement' => 0,
            ],

            // ==========================================
            // COMMUNITY POST MILESTONES (1, 5, 15, 30)
            // ==========================================
            [
                'name' => 'Batter Chatter',
                'slug' => 'post_1',
                'icon_path' => 'images/badges/post_1.png',
                'description' => 'Published your first community thread or review.',
                'order_requirement' => 0,
            ],
            [
                'name' => 'Crumb Conversationalist',
                'slug' => 'post_5',
                'icon_path' => 'images/badges/post_5.png',
                'description' => 'Published 5 community posts.',
                'order_requirement' => 0,
            ],
            [
                'name' => 'Sweet Storyteller',
                'slug' => 'post_15',
                'icon_path' => 'images/badges/post_15.png',
                'description' => 'Published 15 community posts.',
                'order_requirement' => 0,
            ],
            [
                'name' => 'Bakery Icon',
                'slug' => 'post_30',
                'icon_path' => 'images/badges/post_30.png',
                'description' => 'Published 30 community posts. Ultimate community legend!',
                'order_requirement' => 0,
            ],
        ];

        // Using updateOrCreate ensures we don't create duplicates if you run the seeder multiple times
        foreach ($badges as $badge) {
            Badge::updateOrCreate(['slug' => $badge['slug']], $badge);
        }
    }
}
