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

            $table->foreignId('customer_id')
                  ->constrained('customers')
                  ->onDelete('cascade');
                
            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->onDelete('cascade');

            $table->string('description')->nullable();
            $table->decimal('value', 15, 4);
            $table->boolean('finished')->default(false);
            $table->boolean('canceled')->default(false);
            $table->boolean('deleted')->default(false);
            $table->timestamps();
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
