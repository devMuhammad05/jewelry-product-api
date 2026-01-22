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
        Schema::create('variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            $table->string('sku')->unique();
            // $table->decimal('price', 15, 2)->nullable();          
            // $table->string('currency', 3)->default('USD');

            $table->integer('quantity')->default(1);
            $table->boolean('is_orderable')->default(true); 

            // Physical Specs
            $table->string('size')->nullable(); // e.g., "17", "Small", "52"
            $table->string('metal_type')->nullable(); // e.g., "18K Yellow Gold"
            $table->decimal('weight_grams', 8, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variants');
    }
};
