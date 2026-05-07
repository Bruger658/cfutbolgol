<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publications', function (Blueprint $table): void {
            $table->id();
            $table->string('category', 40);
            $table->string('title');
            $table->string('excerpt', 255);
            $table->text('content');
            $table->text('description')->nullable();
            $table->string('image_path');
            $table->date('published_at')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publications');
    }
};