<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('system_settings', 'theme')) {
            Schema::table('system_settings', function (Blueprint $table) {
                $table->string('theme', 10)->default('light')->after('organization_unit');
            });
        }
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn('theme');
        });
    }
};