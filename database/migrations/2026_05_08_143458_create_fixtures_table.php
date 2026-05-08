<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fixtures', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['edefi', 'bafi', 'futsala', 'femenino']);
            $table->date('fixture_date');
            $table->string('home_team_name');
            $table->string('home_team_badge_path');
            $table->string('away_team_name');
            $table->string('away_team_badge_path');
            $table->time('match_time');
            $table->string('weekday', 30);
            $table->string('venue_name');
            $table->boolean('is_home_venue')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category', 'fixture_date']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixtures');
    }
};