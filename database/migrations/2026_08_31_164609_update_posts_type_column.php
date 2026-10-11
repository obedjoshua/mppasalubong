<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Converts the strict ENUM column into a flexible string column
        // We rely on the Laravel Controller for security validation instead!
        DB::statement("ALTER TABLE posts MODIFY type VARCHAR(50) NOT NULL DEFAULT 'thread'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverts it back if needed
        DB::statement("ALTER TABLE posts MODIFY type ENUM('thread', 'review', 'announcement') NOT NULL DEFAULT 'thread'");
    }
};