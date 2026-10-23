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
            $table->string('pickup_branch')->default('main_shop')->after('facebook_messenger_id');
            $table->decimal('customer_latitude', 10, 7)->nullable()->after('location_address');
            $table->decimal('customer_longitude', 10, 7)->nullable()->after('customer_latitude');
            $table->decimal('calculated_distance_km', 8, 2)->nullable()->after('customer_longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_tickets', function (Blueprint $table) {
            $table->dropColumn([
                'pickup_branch',
                'customer_latitude',
                'customer_longitude',
                'calculated_distance_km',
            ]);
        });
    }
};
