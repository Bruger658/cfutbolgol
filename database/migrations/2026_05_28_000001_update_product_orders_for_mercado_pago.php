<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_orders', function (Blueprint $table): void {
            $table->foreignId('user_id')->nullable()->change();
            $table->string('provider_payment_id')->nullable()->after('provider_reference');
            $table->timestamp('paid_at')->nullable()->after('checkout_url');
        });
    }

    public function down(): void
    {
        Schema::table('product_orders', function (Blueprint $table): void {
            $table->dropColumn(['provider_payment_id', 'paid_at']);
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};