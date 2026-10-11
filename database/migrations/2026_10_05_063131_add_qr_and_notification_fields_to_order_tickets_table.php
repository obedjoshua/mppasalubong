<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('order_tickets', function (Blueprint $table) {
            // Customer Contact & Facebook Integration
            $table->string('customer_email')->nullable()->after('customer_name');
            $table->string('customer_phone')->nullable()->after('customer_email');
            $table->string('facebook_messenger_id')->nullable()->after('customer_phone');
            
            // Ticket QR Verification & Channel Preferences
            $table->string('qr_token', 64)->nullable()->unique()->after('status');
            $table->string('preferred_notification_channel')->default('email')->after('qr_token'); // messenger, email, sms
            $table->timestamp('notification_sent_at')->nullable()->after('preferred_notification_channel');
        });

        Schema::table('order_ticket_items', function (Blueprint $table) {
            $table->string('reference_image_path')->nullable()->after('quantity');
        });
    }

    public function down(): void {
        Schema::table('order_tickets', function (Blueprint $table) {
            $table->dropColumn([
                'customer_email',
                'customer_phone',
                'facebook_messenger_id',
                'qr_token',
                'preferred_notification_channel',
                'notification_sent_at'
            ]);
        });

        Schema::table('order_ticket_items', function (Blueprint $table) {
            $table->dropColumn('reference_image_path');
        });
    }
};