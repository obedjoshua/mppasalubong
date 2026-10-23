<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_code')->unique();
            $table->string('from_branch'); // 'main_shop' or 'san_jose'
            $table->string('to_branch');
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('item_name');
            $table->integer('quantity');
            $table->enum('status', ['Requested', 'Approved', 'In Transit', 'Received', 'Cancelled'])->default('Requested');
            $table->foreignId('requested_by_staff_id')->constrained('users');
            $table->foreignId('fulfilled_by_staff_id')->nullable()->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_stock_transfers');
    }
};
