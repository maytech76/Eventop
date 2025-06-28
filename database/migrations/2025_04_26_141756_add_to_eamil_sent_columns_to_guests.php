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
        Schema::table('guests', function (Blueprint $table) {
            
            if (!Schema::hasColumn('guests', 'email_sent')) {
                Schema::table('guests', function (Blueprint $table) {
                    $table->boolean('email_sent')->default(false)->after('status');
                });
            }
        });
    }

     /**
     * Reverse the migrations.
     */

    public function down(): void
    {
        if (Schema::hasColumn('guests', 'email_sent')) {
            Schema::table('guests', function (Blueprint $table) {
                $table->dropColumn('email_sent');
            });
        }
    }
};
