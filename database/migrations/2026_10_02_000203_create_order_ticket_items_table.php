<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_ticket_items', function (Blueprint $table) {
            $table->id();
            $table->string('order_ticket_id', 11);
            $table->foreign('order_ticket_id')->references('id')->on('order_tickets')->cascadeOnDelete();
            $table->string('item_type');
            $table->integer('quantity');
            $table->json('custom_options')->nullable(); // Stores colors, sizes, AI themes
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_ticket_items');
    }
};
