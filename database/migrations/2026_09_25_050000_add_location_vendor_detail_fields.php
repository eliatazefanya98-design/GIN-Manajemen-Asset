<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            if (! Schema::hasColumn('locations', 'gedung')) {
                $table->string('gedung')->nullable();
            }
            if (! Schema::hasColumn('locations', 'lantai')) {
                $table->string('lantai')->nullable();
            }
            if (! Schema::hasColumn('locations', 'ruangan')) {
                $table->string('ruangan')->nullable();
            }
        });

        Schema::table('vendors', function (Blueprint $table) {
            if (! Schema::hasColumn('vendors', 'email')) {
                $table->string('email')->nullable();
            }
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
