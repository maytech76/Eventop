<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void{

        Schema::create('events', function (Blueprint $table) {

            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->string('title', 150);
            $table->text('description');
            $table->string('address', 250);
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('limit_guest');
            $table->string('rooms', 100);
            $table->enum('status', ['reservado', 'activo', 'culminado', 'cancelado'])->default('activo');
            $table->timestamps();

        });
    
    }

   
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
