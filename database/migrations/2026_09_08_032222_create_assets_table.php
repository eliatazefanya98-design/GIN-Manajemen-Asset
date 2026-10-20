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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('location_id')
                ->nullable()
                ->constrained('locations')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('vendor_id')
                ->nullable()
                ->constrained('vendors')
                ->nullOnDelete();

            $table->string('kode_asset')->unique('assets_asset_code_unique');
            $table->string('nama_asset');
            $table->text('deskripsi')->nullable();

            $table->date('tanggal_perolehan')->nullable();
            $table->decimal('harga', 15, 2)->default(0);

            $table->string('kondisi')->default('baik');
            $table->string('status')->default('aktif');

            $table->string('merk')->nullable();
            $table->string('tipe')->nullable();
            $table->string('penanggung_jawab')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
