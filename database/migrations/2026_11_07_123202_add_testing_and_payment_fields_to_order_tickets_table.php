<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_tickets', function (Blueprint $table) {
            // Testing toggle for dummy payment gateway vs live gateway
            if (!Schema::hasColumn('order_tickets', 'is_testing')) {
                $table->boolean('is_testing')->default(false);
            }
            if (!Schema::hasColumn('order_tickets', 'payment_status')) {
                $table->string('payment_status')->default('pending'); // pending, paid, failed
            }
            if (!Schema::hasColumn('order_tickets', 'payment_gateway')) {
                $table->string('payment_gateway')->nullable(); // dummy_test, paymongo, etc.
            }
            if (!Schema::hasColumn('order_tickets', 'payment_transaction_id')) {
                $table->string('payment_transaction_id')->nullable();
            }
        });

        // Ensure order_ticket_items table has reference image storage
        if (Schema::hasTable('order_ticket_items')) {
            Schema::table('order_ticket_items', function (Blueprint $table) {
                if (!Schema::hasColumn('order_ticket_items', 'image_path')) {
                    $table->string('image_path')->nullable();
                }
                if (!Schema::hasColumn('order_ticket_items', 'r2_url')) {
                    $table->text('r2_url')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_tickets', function (Blueprint $table) {
            $table->dropColumn([
                'is_testing',
                'payment_status',
                'payment_gateway',
                'payment_transaction_id'
            ]);
        });

        if (Schema::hasTable('order_ticket_items')) {
            Schema::table('order_ticket_items', function (Blueprint $table) {
                $columnsToDrop = [];
                if (Schema::hasColumn('order_ticket_items', 'image_path')) {
                    $columnsToDrop[] = 'image_path';
                }
                if (Schema::hasColumn('order_ticket_items', 'r2_url')) {
                    $columnsToDrop[] = 'r2_url';
                }
                if (!empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
                }
            });
        }
    }
};