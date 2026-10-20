<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_records', function (Blueprint $table) {
            if (! Schema::hasColumn('maintenance_records', 'jenis')) {
                $table->text('jenis')->nullable();
            }
            if (! Schema::hasColumn('maintenance_records', 'keluhan')) {
                $table->text('keluhan')->nullable();
            }
            if (! Schema::hasColumn('maintenance_records', 'kerusakan')) {
                $table->text('kerusakan')->nullable();
            }
            if (! Schema::hasColumn('maintenance_records', 'hasil_tindakan')) {
                $table->text('hasil_tindakan')->nullable();
            }
            if (! Schema::hasColumn('maintenance_records', 'kondisi_sebelum')) {
                $table->string('kondisi_sebelum')->nullable();
            }
            if (! Schema::hasColumn('maintenance_records', 'kondisi_sesudah')) {
                $table->string('kondisi_sesudah')->nullable();
            }
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
