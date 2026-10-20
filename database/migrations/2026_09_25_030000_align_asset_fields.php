<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('merk')->nullable();
            $table->string('tipe')->nullable();
            $table->string('penanggung_jawab')->nullable();
        });

        if (DB::connection()->getDriverName() === 'sqlite') {
            Schema::table('assets', function (Blueprint $table) {
                $table->string('condition')->default('baik')->change();
                $table->string('status')->default('aktif')->change();
            });

            return;
        }

        DB::statement('ALTER TABLE assets DROP CONSTRAINT IF EXISTS assets_condition_check');
        DB::statement('ALTER TABLE assets DROP CONSTRAINT IF EXISTS assets_status_check');
        DB::statement("ALTER TABLE assets ADD CONSTRAINT assets_condition_check CHECK (condition IN ('baik', 'rusak_ringan', 'rusak_berat'))");
        DB::statement("ALTER TABLE assets ADD CONSTRAINT assets_status_check CHECK (status IN ('aktif', 'dipinjam', 'maintenance', 'rusak', 'dihapus', 'tidak_aktif'))");
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            Schema::table('assets', function (Blueprint $table) {
                $table->enum('condition', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik')->change();
                $table->enum('status', ['aktif', 'maintenance', 'tidak_aktif'])->default('aktif')->change();
            });
        } else {
            DB::statement('ALTER TABLE assets DROP CONSTRAINT IF EXISTS assets_condition_check');
            DB::statement('ALTER TABLE assets DROP CONSTRAINT IF EXISTS assets_status_check');
            DB::statement("ALTER TABLE assets ADD CONSTRAINT assets_condition_check CHECK (condition IN ('baik', 'rusak_ringan', 'rusak_berat'))");
            DB::statement("ALTER TABLE assets ADD CONSTRAINT assets_status_check CHECK (status IN ('aktif', 'maintenance', 'tidak_aktif'))");
        }

        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['merk', 'tipe', 'penanggung_jawab']);
        });
    }
};
