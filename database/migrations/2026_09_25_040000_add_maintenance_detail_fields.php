<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_records', function (Blueprint $table) {
            $table->text('jenis')->nullable();
            $table->text('keluhan')->nullable();
            $table->text('kerusakan')->nullable();
            $table->text('hasil_tindakan')->nullable();
            $table->string('kondisi_sebelum')->nullable();
            $table->string('kondisi_sesudah')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_records', function (Blueprint $table) {
            $table->dropColumn([
                'jenis',
                'keluhan',
                'kerusakan',
                'hasil_tindakan',
                'kondisi_sebelum',
                'kondisi_sesudah',
            ]);
        });
    }
};
