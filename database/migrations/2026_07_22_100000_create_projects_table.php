<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nama_proyek');
            $table->string('alamat_proyek')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('kategori')->nullable(); // Pembangunan Rumah, Gedung Komersial, Renovasi, Konsultasi
            $table->integer('progress_percentage')->default(0);
            $table->string('status')->default('aktif'); // aktif, selesai, dibatalkan
            $table->string('main_image')->nullable();
            $table->json('gallery')->nullable();
            $table->boolean('portfolio_approved')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
