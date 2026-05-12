<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table): void {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('category');
            $table->string('document_number');
            $table->string('address');
            $table->string('city');
            $table->string('phone', 40);
            $table->string('responsible_adult_phone', 40)->nullable();
            $table->boolean('is_up_to_date')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};