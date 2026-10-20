<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->string('gedung')->nullable();
            $table->string('lantai')->nullable();
            $table->string('ruangan')->nullable();
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->string('email')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn(['gedung', 'lantai', 'ruangan']);
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
};
