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
        Schema::table('penawarans', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('alamat'); // pending, approved, rejected
            $table->foreignId('project_id')->nullable()->after('status')->constrained('projects')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penawarans', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn(['status', 'project_id']);
        });
    }
};
