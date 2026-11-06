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
        
        Schema::table('user_files', function (Blueprint $table) {
            // 'design'   = diunggah oleh user (desain rumah)
            // 'progress' = diunggah oleh admin (foto perkembangan proyek)
            $table->string('type')->default('design')->after('file_path');
            $table->text('description')->nullable()->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_files', function (Blueprint $table) {
             $table->dropColumn(['type' , 'description']);
        });
    }
};
