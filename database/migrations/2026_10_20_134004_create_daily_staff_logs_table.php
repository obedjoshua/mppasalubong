<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_staff_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('branch');
            $table->enum('log_type', [
                'Shift Open',
                'Shift Close',
                'Spoilage Waste',
                'Stock Adjustment',
                'General Note',
            ]);
            $table->decimal('cash_drawer_amount', 10, 2)->nullable();
            $table->integer('completed_orders_count')->default(0);
            $table->text('notes');
            $table->json('meta_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_staff_logs');
    }
};
