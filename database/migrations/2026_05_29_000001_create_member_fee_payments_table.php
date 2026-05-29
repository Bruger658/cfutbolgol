database/migrations/2026_05_29_000001_create_member_fee_payments_table.php<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_fee_payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->json('months');
            $table->decimal('base_amount', 12, 2);
            $table->decimal('surcharge_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->string('status', 40)->default('pending');
            $table->string('payment_provider', 40)->default('mercado_pago');
            $table->string('provider_reference')->nullable();
            $table->string('provider_payment_id')->nullable();
            $table->text('checkout_url')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_fee_payments');
    }
};