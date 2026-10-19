<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_ticket_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_ticket_items', 'image_data')) {
                $table->longText('image_data')->nullable();
            }
            if (!Schema::hasColumn('order_ticket_items', 'r2_url')) {
                $table->string('r2_url')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_ticket_items', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('order_ticket_items', 'image_data')) {
                $columnsToDrop[] = 'image_data';
            }
            if (Schema::hasColumn('order_ticket_items', 'r2_url')) {
                $columnsToDrop[] = 'r2_url';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
