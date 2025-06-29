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
        Schema::table('events', function (Blueprint $table) {

            $table->string('limit_partners')->after('limit_guest')->nullable();
            $table->enum('type', ['grupo', 'unico'])->after('limit_partners')->nullable();
            $table->string('certificate_template', 100)->after('type')->nullable();
            $table->boolean('has_certificate')->after('certificate_template')->default('false');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            
            $table->dropIfExists('limit_partners');
        });
    }
};
