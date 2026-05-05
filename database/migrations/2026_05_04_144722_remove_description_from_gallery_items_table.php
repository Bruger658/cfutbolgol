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
        Schema::table('gallery_items', function (Blueprint $table) {            
            if (Schema::hasColumn('gallery_items', 'description')) {
                $table->dropColumn('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gallery_items', function (Blueprint $table) {
            // Ajustá el tipo de dato si no era 'text' (ej. string)            
            if (! Schema::hasColumn('gallery_items', 'description')) {
                $table->text('description')->nullable();
            }
        });
    }
};