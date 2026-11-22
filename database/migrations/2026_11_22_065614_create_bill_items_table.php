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
        Schema::create('bill_items', function (Blueprint $table) {
            $table->id();

            // Parent monthly bill
            $table->foreignId('bill_id')
                  ->constrained('bills')
                  ->cascadeOnDelete();

            // Daily milk details
            $table->date('entry_date');

            $table->decimal('quantity', 10, 3)->default(0);

            // Rate applicable on that particular date
            $table->decimal('rate_per_litre', 10, 2)->default(0);

            // Quantity multiplied by rate
            $table->decimal('amount', 10, 2)->default(0);

            $table->timestamps();

            // One row per date in a bill
            $table->unique(
                ['bill_id', 'entry_date'],
                'unique_bill_item_date'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_items');
    }
};
