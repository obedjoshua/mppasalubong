<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotional_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title', 120);
            $table->string('body', 240)->nullable();
            $table->string('link_url', 2048)->nullable();
            $table->string('image_path')->nullable();
            $table->string('cta_label', 40)->nullable();
            $table->string('background_color', 7)->default('#310669');
            $table->string('text_color', 7)->default('#FFFFFF');
            $table->string('alignment', 10)->default('left');
            $table->string('layout', 10)->default('split');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotional_banners');
    }
};
