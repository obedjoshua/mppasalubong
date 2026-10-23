<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('category')->default('General')->after('slug');
            $table->enum('stock_status', ['In Stock', 'Low Stock', 'Out of Stock'])->default('In Stock')->after('stock_quantity');
            $table->json('attributes')->nullable()->after('is_active');
            $table->softDeletes()->after('attributes');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['category', 'stock_status', 'attributes']);
        });
    }
};
