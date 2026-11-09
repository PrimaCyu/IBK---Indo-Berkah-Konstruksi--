<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_cashflows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->restrictOnDelete();
            $table->date('transaction_date');
            $table->enum('type', ['penerimaan', 'pengeluaran']);
            $table->string('code', 50)->nullable();
            $table->text('description');
            $table->string('unit', 30)->nullable();
            $table->unsignedBigInteger('quantity_millis');
            $table->unsignedBigInteger('unit_price_cents');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['project_id', 'transaction_date', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_cashflows');
    }
};
