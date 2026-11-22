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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Related customer bill
            $table->foreignId('bill_id')
                  ->constrained('bills')
                  ->restrictOnDelete();

            // Payment information
            $table->decimal('amount', 10, 2);

            $table->date('payment_date');

            $table->enum('payment_method', [
                'cash',
                'upi',
                'bank_transfer',
                'other'
            ])->default('cash');

            // Optional transaction details
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            // Faster bill-wise payment history
            $table->index(['bill_id', 'payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
