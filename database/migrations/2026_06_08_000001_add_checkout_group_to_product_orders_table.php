<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_orders', function (Blueprint $table): void {
            $table->uuid('checkout_group')->nullable()->after('user_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('product_orders', function (Blueprint $table): void {
            $table->dropColumn('checkout_group');
        });
    }
};