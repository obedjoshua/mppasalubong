<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ClassicYemaCheeseCakeSeeder extends Seeder
{
    public function run(): void
    {
        Product::updateOrCreate(
            ["slug" => "classic-yema-cheese-cake"],
            [
                "name" => "Classic Yema Cheese Cake",
                "slug" => "classic-yema-cheese-cake",
                "category" => "Cakes",
                "description" => "Our signature Classic Yema Cheese Cake - a delightful fusion of creamy cheesecake and sweet yema (custard) topping. Made with premium cream cheese, rich yema made from condensed milk and egg yolks, on a buttery graham cracker crust. Perfect for celebrations or a sweet treat any day.",
                "price" => 850.00,
                "stock_quantity" => 50,
                "stock_status" => "In Stock",
                "is_active" => true,
                "image_url" => null,
                "attributes" => [
                    "features" => [
                        "Premium cream cheese filling",
                        "Rich yema topping made from condensed milk and egg yolks",
                        "Buttery graham cracker crust",
                        "Available in 6-inch and 8-inch sizes",
                        "Made fresh daily",
                    ],
                    "gallery" => [],
                ],
            ]
        );

        $this->command->info("Classic Yema Cheese Cake product created/updated successfully.");
    }
}
