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
        Schema::create('milk_entries', function (Blueprint $table) {
            $table->id();

            // Farmer who records the milk delivery
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Customer receiving the milk
            $table->foreignId('customer_id')
                  ->constrained('customers')
                  ->restrictOnDelete();

            // Date of milk delivery
            $table->date('entry_date');

            // Milk quantity in litres
            $table->decimal('quantity', 8, 3)->default(0);

            $table->timestamps();

            // One entry per customer per day
            $table->unique(
                ['customer_id', 'entry_date'],
                'unique_customer_daily_milk'
            );

            // Faster farmer-wise daily searches
            $table->index(['user_id', 'entry_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milk_entries');
    }
};
