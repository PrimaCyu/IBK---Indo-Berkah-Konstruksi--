<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'owner', 'accounting'])->change();
        });
    }

    public function down(): void
    {
        if (DB::table('users')->where('role', 'accounting')->exists()) {
            throw new RuntimeException('Ubah hak akses akun accounting sebelum membatalkan migrasi ini.');
        }

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'owner'])->change();
        });
    }
};
