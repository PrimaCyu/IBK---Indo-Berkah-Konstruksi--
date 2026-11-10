<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_cashflows', function (Blueprint $table) {
            $table->enum('status', ['pending', 'lunas', 'ditolak'])->default('lunas')->after('payment_method');
            $table->foreignId('approved_by')->nullable()->after('updated_by')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->index(['project_id', 'status'], 'cashflow_project_status_idx');
        });
    }

    public function down(): void
    {
        Schema::table('project_cashflows', function (Blueprint $table) {
            $table->dropIndex('cashflow_project_status_idx');
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn(['status', 'approved_at']);
        });
    }
};

