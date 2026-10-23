<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Safely update the ENUM column to include 'announcement'
        DB::statement("ALTER TABLE posts MODIFY COLUMN type ENUM('thread', 'review', 'announcement') DEFAULT 'thread'");
    }

    public function down(): void
    {
        // Revert it back if we ever need to rollback
        DB::statement("ALTER TABLE posts MODIFY COLUMN type ENUM('thread', 'review') DEFAULT 'thread'");
    }
};
