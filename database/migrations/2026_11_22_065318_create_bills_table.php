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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();

            // Unique bill number
            $table->string('bill_number')->unique();

            // Farmer and customer ownership
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('customer_id')
                  ->constrained('customers')
                  ->restrictOnDelete();

            // Farmer details saved on bill
            $table->string('farmer_name', 100);
            $table->string('farmer_mobile', 15);

            // Customer details saved on bill
            $table->string('customer_name', 100);
            $table->string('customer_mobile', 15)->nullable();

            // Billing period
            $table->date('period_start');
            $table->date('period_end');

            // Bill calculations
            $table->decimal('total_litres', 10, 3)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);

            // Payment details
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('balance_amount', 10, 2)->default(0);

            $table->enum('payment_status', [
                'pending',
                'partial',
                'paid'
            ])->default('pending');

            // Bill generation date
            $table->timestamp('generated_at')->nullable();

            $table->timestamps();

            // One monthly bill per customer
            $table->unique(
                ['customer_id', 'period_start', 'period_end'],
                'unique_customer_billing_period'
            );

            $table->index(['user_id', 'period_start']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
