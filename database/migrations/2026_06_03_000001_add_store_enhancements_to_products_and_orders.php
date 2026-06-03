<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->json('gallery_images')->nullable()->after('image_url');
        });

        Schema::table('product_orders', function (Blueprint $table): void {
            $table->string('delivery_method', 40)->default('shipping')->after('payment_provider');
        });
    }

    public function down(): void
    {
        Schema::table('product_orders', function (Blueprint $table): void {
            $table->dropColumn('delivery_method');
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn('gallery_images');
        });
    }
};