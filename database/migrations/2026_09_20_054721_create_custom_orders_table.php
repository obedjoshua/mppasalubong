<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_orders', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_id')->unique(); // e.g., TKT-A1B2C3
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Customer Info
            $table->string('customer_name');
            $table->string('location_address');
            $table->string('facebook_link')->nullable();

            // Cake Specs
            $table->string('theme');
            $table->string('shape');
            $table->string('size');
            $table->string('height');
            $table->string('flavor');

            // AI Design & Attachments
            $table->string('reference_image_path')->nullable();
            $table->string('color_base')->default('#ffffff');
            $table->string('color_top')->nullable();
            $table->string('color_border')->nullable();
            $table->string('color_sprinkles')->nullable();

            // Dedication & Custom Image
            $table->string('message_text')->nullable();
            $table->string('message_placement')->nullable();
            $table->string('custom_image_path')->nullable();
            $table->string('custom_image_placement')->nullable();

            // Scheduling & Notification
            $table->date('pickup_date');
            $table->time('pickup_time');
            $table->string('notification_preference');

            // System Status
            $table->string('status')->default('Pending Staff Review');
            $table->decimal('final_price', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_orders');
    }
};
