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
        // Add new columns to the existing posts table ONLY if they don't exist yet
        Schema::table('posts', function (Blueprint $table) {
            if (! Schema::hasColumn('posts', 'is_pinned')) {
                $table->boolean('is_pinned')->default(false)->after('type');
            }
            if (! Schema::hasColumn('posts', 'review_metrics')) {
                $table->json('review_metrics')->nullable()->after('content');
            }
        });

        // We removed the Schema::create('comments') because your database already has it!
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'is_pinned')) {
                $table->dropColumn('is_pinned');
            }
            if (Schema::hasColumn('posts', 'review_metrics')) {
                $table->dropColumn('review_metrics');
            }
        });
    }
};
