<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('owner')->change();
            });
        } else {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'admin', 'owner', 'accounting') NOT NULL DEFAULT 'owner'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('owner')->change();
            });
        } else {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'owner', 'accounting') NOT NULL DEFAULT 'owner'");
        }
    }
};
