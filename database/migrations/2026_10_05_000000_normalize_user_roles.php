<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->where('role', 'pemimpin')->update(['role' => 'pimpinan']);
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'pimpinan')->update(['role' => 'pemimpin']);
    }
};