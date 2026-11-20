<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('order_tickets', 'amount_paid')) {
                $table->decimal('amount_paid', 10, 2)->default(0)->after('total_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_tickets', function (Blueprint $table) {
            if (Schema::hasColumn('order_tickets', 'amount_paid')) {
                $table->dropColumn('amount_paid');
            }
        });
    }
};