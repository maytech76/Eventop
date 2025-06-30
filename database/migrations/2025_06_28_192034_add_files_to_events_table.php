<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {

            $table->integer('limit_partners')->after('limit_guest')->nullable();
            $table->enum('type', ['unico', 'grupo', 'profesional', 'deportes'])->after('limit_partners')->nullable();
            $table->string('certificate_template', 100)->after('type')->nullable();
            $table->boolean('has_certificate')->after('certificate_template')->default(false);

        });
    }

    
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'limit_partners',
                'type',
                'certificate_template',
                'has_certificate'
            ]);
        });
    }
};
