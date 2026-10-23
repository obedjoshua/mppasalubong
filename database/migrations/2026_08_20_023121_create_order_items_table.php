<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');

            // Nullable because it might be a fully custom order not on the shelf
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');

            // Custom request fields
            $table->string('item_name')->nullable();
            $table->text('customization_details')->nullable();
            $table->date('target_delivery_date')->nullable();

            $table->integer('quantity');
            $table->decimal('price_at_time_of_purchase', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
    }
};
