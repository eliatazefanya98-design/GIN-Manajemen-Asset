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
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('asset_id')
                ->constrained('assets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->date('tanggal');
            $table->text('keluhan');

            $table->string('jenis')->nullable();
            $table->text('kerusakan')->nullable();

            $table->decimal('biaya', 15, 2)->default(0);
            $table->string('teknisi')->nullable();

            $table->text('hasil_tindakan')->nullable();
            $table->string('kondisi_sebelum')->nullable();
            $table->string('kondisi_sesudah')->nullable();

            $table->string('status')->default('terjadwal');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
    }
};
