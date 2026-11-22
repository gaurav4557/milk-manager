
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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            // Farmer who owns this customer
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Customer details
            $table->string('name', 100);
            $table->string('mobile', 15)->nullable();
            $table->text('address')->nullable();

            // Useful for farmer-wise customer searches
            $table->index(['user_id', 'name']);

            // Keep previous records when customer is removed
            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
