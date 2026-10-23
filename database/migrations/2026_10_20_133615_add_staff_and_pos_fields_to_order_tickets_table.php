<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_tickets', function (Blueprint $table) {
            $table->decimal('total_price', 10, 2)->default(0.00)->after('status');
            $table->decimal('deposit_required', 10, 2)->default(0.00)->after('total_price');
            $table->timestamp('completed_at')->nullable()->after('notification_sent_at');
            $table->foreignId('handled_by_staff_id')->nullable()->after('completed_at')->constrained('users')->nullOnDelete();
            $table->text('staff_notes')->nullable()->after('handled_by_staff_id');
            $table->boolean('is_rescheduled')->default(false)->after('pickup_time');
        });
    }

    public function down(): void
    {
        Schema::table('order_tickets', function (Blueprint $table) {
            $table->dropForeign(['handled_by_staff_id']);
            $table->dropColumn([
                'total_price',
                'deposit_required',
                'completed_at',
                'handled_by_staff_id',
                'staff_notes',
                'is_rescheduled',
            ]);
        });
    }
};
