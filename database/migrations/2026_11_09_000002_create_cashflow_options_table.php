<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cashflow_options')) {
            throw new RuntimeException('Tabel cashflow_options sudah ada; periksa skema sebelum menjalankan migrasi.');
        }

        Schema::create('cashflow_options', function (Blueprint $table) {
            $table->id();
            $table->string('type', 10);
            $table->string('value', 50);
            $table->char('normalized_hash', 64);
            $table->timestamps();
            $table->unique(['type', 'normalized_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashflow_options');
    }
};
