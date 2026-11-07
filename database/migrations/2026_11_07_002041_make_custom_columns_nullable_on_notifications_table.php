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
        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'title')) {
                $table->string('title')->nullable()->change();
            }
            if (Schema::hasColumn('notifications', 'message')) {
                $table->text('message')->nullable()->change();
            }
            if (Schema::hasColumn('notifications', 'action_url')) {
                $table->string('action_url')->nullable()->change();
            }
            if (Schema::hasColumn('notifications', 'type')) {
                $table->string('type')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'title')) {
                $table->string('title')->nullable(false)->change();
            }
            if (Schema::hasColumn('notifications', 'message')) {
                $table->text('message')->nullable(false)->change();
            }
            if (Schema::hasColumn('notifications', 'action_url')) {
                $table->string('action_url')->nullable(false)->change();
            }
            if (Schema::hasColumn('notifications', 'type')) {
                $table->string('type')->nullable(false)->change();
            }
        });
    }
};