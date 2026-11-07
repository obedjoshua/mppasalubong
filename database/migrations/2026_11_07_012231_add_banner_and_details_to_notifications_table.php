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
            if (!Schema::hasColumn('notifications', 'type')) {
                $table->string('type')->nullable();
            }
            if (!Schema::hasColumn('notifications', 'banner_path')) {
                $table->string('banner_path')->nullable();
            }
            if (!Schema::hasColumn('notifications', 'details')) {
                $table->text('details')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('notifications', 'type')) {
                $columnsToDrop[] = 'type';
            }
            if (Schema::hasColumn('notifications', 'banner_path')) {
                $columnsToDrop[] = 'banner_path';
            }
            if (Schema::hasColumn('notifications', 'details')) {
                $columnsToDrop[] = 'details';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};