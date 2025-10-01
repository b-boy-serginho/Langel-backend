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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_client')->nullable();            
            $table->foreign('id_client')->references('id')->on('clients')->onDelete('cascade');
            $table->string('nro')->nullable();   
            $table->decimal('total', 8, 2);         
            $table->date('date');
            $table->time('hour');  
            $table->string('day');            
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
