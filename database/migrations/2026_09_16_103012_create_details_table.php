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
        Schema::create('details', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_product')->nullable();
            $table->unsignedBigInteger('id_receipt')->nullable();

            // Clave foránea para id_product, relacionado con la tabla products
            $table->foreign('id_product')->references('id')->on('products')->onDelete('cascade');
            
            // Clave foránea para id_receipt, relacionado con la tabla receipts
            $table->foreign('id_receipt')->references('id')->on('receipts')->onDelete('cascade');

            $table->decimal('quantity', 8, 2);
            $table->decimal('unit_price', 8, 2)->nullable();
            $table->decimal('amount', 8, 2);
            
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('details');
    }
};
