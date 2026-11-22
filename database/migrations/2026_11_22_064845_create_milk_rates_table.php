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
        Schema::create('milk_rates', function (Blueprint $table) {
            $table->id();

            // Farmer who sets the milk rate
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Price per litre
            $table->decimal('rate', 8, 2);

            // Date from which this rate becomes applicable
            $table->date('effective_from');

            $table->timestamps();

            // Prevent duplicate rate dates for the same farmer
            $table->unique(
                ['user_id', 'effective_from'],
                'unique_farmer_rate_date'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milk_rates');
    }
};
