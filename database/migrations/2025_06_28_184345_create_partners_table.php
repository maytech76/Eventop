<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void{

        Schema::create('partners', function (Blueprint $table) {

            $table->id();
            $table->foreignId('guest_id')->constrained('guests')->onDelete('cascade');
            $table->string('name', 50);
            $table->time('checkin_time');// se debe actualizar con el mismo tiempo del invitado
            $table->enum('status',  ['checkin', 'guest'])->default('guest');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
