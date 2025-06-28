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
        Schema::create('guests', function (Blueprint $table) {

            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('photo')->nullable();
            $table->string('name', 150);
            $table->string('last_name', 150);
            $table->string('phone', 15);
            $table->string('email', 150);
            $table->string('code_in')->unique()->nullable();
            $table->string('qr_code_path')->nullable();
            $table->boolean('is_attended')->default(false);
            $table->timestamp('checkin_time')->nullable();
            $table->timestamp('checkout_time')->nullable();
            $table->timestamp('attendance_time')->nullable();
            $table->enum('status', ['checkIn', 'active', 'CheckOut', 'retire'])->default('active');
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest');
    }
};
