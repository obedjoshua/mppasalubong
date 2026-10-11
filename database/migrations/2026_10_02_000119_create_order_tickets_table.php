<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('order_tickets', function (Blueprint $table) {
            $table->string('id', 11)->primary(); // 11-character Base64 Primary Key
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('location_address')->nullable();
            $table->date('pickup_date');
            $table->string('pickup_time');
            $table->text('additional_notes')->nullable();
            $table->string('status')->default('Pending Staff Review');
            $table->timestamps();
        });
    }

    public function down(): void { 
        Schema::dropIfExists('order_tickets'); 
    }
};