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
        Schema::table('users', function (Blueprint $table) {
            // Add the new settings columns
            $table->boolean('two_factor_auth')->default(false);
            $table->boolean('notify_email')->default(true);
            $table->boolean('notify_push')->default(true);
            $table->boolean('notify_sms')->default(false);
            $table->boolean('marketing_emails')->default(false);
            $table->boolean('data_sharing')->default(true);
            $table->string('theme')->default('system');
            $table->string('timezone')->default('Asia/Manila');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the columns if we ever rollback this migration
            $table->dropColumn([
                'two_factor_auth',
                'notify_email',
                'notify_push',
                'notify_sms',
                'marketing_emails',
                'data_sharing',
                'theme',
                'timezone',
            ]);
        });
    }
};
