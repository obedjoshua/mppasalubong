<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Badges Definition Table (for your custom badges)
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique(); // e.g. 'order_10', 'first_review'
            $table->string('icon_path'); // e.g. 'images/badges/badge_10.png'
            $table->text('description');
            $table->integer('order_requirement')->default(0);
            $table->timestamps();
        });

        // 2. User-Badge Pivot Table
        Schema::create('user_badge', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('badge_id')->constrained()->onDelete('cascade');
            $table->timestamp('unlocked_at')->useCurrent();
        });

        // 3. Posts & Reviews Table
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['thread', 'review'])->default('thread');
            $table->text('content');
            $table->string('image_path')->nullable();
            $table->timestamps();
        });

        // 4. Comments Table
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('comment');
            $table->timestamps();
        });

        // 5. Discord/Duolingo-style Emoji Reactions Pivot Table
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('emoji'); // e.g. '🍰', '🔥', '😍', '🎂'
            $table->timestamps();

            $table->unique(['post_id', 'user_id', 'emoji']); // 1 user = 1 of each emoji per post
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reactions');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('user_badge');
        Schema::dropIfExists('badges');
    }
};