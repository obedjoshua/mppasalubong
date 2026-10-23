<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->string('sender_branch');
            $table->string('recipient_branch');
            $table->string('subject')->nullable();
            $table->text('message');
            $table->enum('priority', ['Normal', 'Urgent', 'High Alert'])->default('Normal');
            $table->boolean('is_read')->default(false);
            $table->string('order_ticket_id', 11)->nullable();
            $table->foreign('order_ticket_id')->references('id')->on('order_tickets')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_messages');
    }
};
