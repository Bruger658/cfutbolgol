<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollment_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('player_name');
            $table->date('birth_date');
            $table->string('guardian_email');
            $table->string('contact_phone', 40);
            $table->string('category', 120);
            $table->string('status', 40)->default('pendiente')->index();
            $table->timestamp('contacted_at')->nullable();
            $table->timestamp('trial_scheduled_at')->nullable();
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_requests');
    }
};