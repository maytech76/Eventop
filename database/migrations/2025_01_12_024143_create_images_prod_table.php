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
        Schema::create('images_prod', function (Blueprint $table) {
            
            $table->id();
            $table->foreignId('prod_id')->constrained('products')->onDelete('cascade');
            $table->string('name'); // Nombre de la imagen
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images_prod');
    }
};
