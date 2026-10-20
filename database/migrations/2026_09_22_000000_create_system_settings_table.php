<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('organization_name');
            $table->string('organization_unit');
            $table->string('timezone')->default('Asia/Jakarta (GMT+07:00)');
            $table->string('date_format')->default('DD/MM/YYYY');
            $table->string('asset_prefix', 20)->default('AST-GIN');
            $table->string('asset_separator', 3)->default('-');
            $table->unsignedTinyInteger('asset_digits')->default(4);
            $table->unsignedInteger('asset_next_number')->default(1);
            $table->string('logo_path')->nullable();
            $table->boolean('email_notifications')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
