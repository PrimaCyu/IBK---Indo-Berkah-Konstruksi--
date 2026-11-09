<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fail before applying DDL if a previous attempt or another installation owns any of these objects.
        foreach (['transaction_categories', 'receipt_sequences', 'transaction_attachments', 'transaction_audit_logs'] as $table) {
            if (Schema::hasTable($table)) {
                throw new RuntimeException("Tabel {$table} sudah ada; periksa skema sebelum menjalankan migrasi.");
            }
        }
        foreach ([
            'projects' => ['kode_proyek', 'tanggal_mulai', 'tanggal_selesai'],
            'project_cashflows' => ['transaction_number', 'receipt_number', 'category_id', 'payment_method', 'created_by', 'updated_by', 'last_edited_at'],
        ] as $table => $columns) {
            foreach ($columns as $column) {
                if (Schema::hasColumn($table, $column)) {
                    throw new RuntimeException("Kolom {$table}.{$column} sudah ada; periksa skema sebelum menjalankan migrasi.");
                }
            }
        }
        if (Schema::hasIndex('project_cashflows', 'project_cashflows_date_type_idx')) {
            throw new RuntimeException('Indeks project_cashflows_date_type_idx sudah ada; periksa skema sebelum menjalankan migrasi.');
        }

        Schema::table('projects', function (Blueprint $table) {
            $table->string('kode_proyek', 50)->nullable()->unique()->after('id');
            $table->date('tanggal_mulai')->nullable()->after('status');
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
        });

        Schema::create('transaction_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->enum('type', ['penerimaan', 'pengeluaran', 'both'])->default('both');
            $table->string('description', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['type', 'is_active', 'sort_order']);
        });

        $now = now();
        $defaults = [
            ['name' => 'Pembayaran Klien', 'slug' => 'pembayaran-klien', 'type' => 'penerimaan', 'description' => 'Termin pembayaran, DP, atau pelunasan proyek dari klien', 'sort_order' => 1],
            ['name' => 'Operasional', 'slug' => 'operasional', 'type' => 'both', 'description' => 'Kebutuhan operasional lapangan dan kantor proyek', 'sort_order' => 2],
            ['name' => 'Pembelian Material', 'slug' => 'pembelian-material', 'type' => 'pengeluaran', 'description' => 'Pengadaan semen, besi, pasir, bata, dan material bangunan', 'sort_order' => 3],
            ['name' => 'Transportasi', 'slug' => 'transportasi', 'type' => 'pengeluaran', 'description' => 'Ongkos kirim material, BBM, dan mobilitas tim lapangan', 'sort_order' => 4],
            ['name' => 'Konsumsi', 'slug' => 'konsumsi', 'type' => 'pengeluaran', 'description' => 'Konsumsi pekerja, rapat proyek, dan koordinasi lapangan', 'sort_order' => 5],
            ['name' => 'Gaji', 'slug' => 'gaji', 'type' => 'pengeluaran', 'description' => 'Upah tukang, mandor, dan honorarium tim teknis', 'sort_order' => 6],
            ['name' => 'Peralatan', 'slug' => 'peralatan', 'type' => 'pengeluaran', 'description' => 'Sewa alat berat, scaffolding, dan pembelian perkakas kerja', 'sort_order' => 7],
            ['name' => 'Administrasi', 'slug' => 'administrasi', 'type' => 'both', 'description' => 'Perizinan, dokumen kontrak, materai, dan biaya bank', 'sort_order' => 8],
            ['name' => 'Lainnya', 'slug' => 'lainnya', 'type' => 'both', 'description' => 'Penerimaan atau pengeluaran proyek di luar kategori utama', 'sort_order' => 9],
        ];

        DB::table('transaction_categories')->insert(array_map(
            fn (array $row) => $row + ['is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            $defaults
        ));

        Schema::create('receipt_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('sequence_key', 40)->unique();
            $table->unsignedBigInteger('last_number')->default(0);
            $table->timestamps();
        });

        Schema::table('project_cashflows', function (Blueprint $table) {
            $table->string('transaction_number', 40)->nullable()->unique()->after('project_id');
            $table->string('receipt_number', 40)->nullable()->unique()->after('transaction_number');
            $table->foreignId('category_id')->nullable()->after('type')->constrained('transaction_categories')->nullOnDelete();
            $table->string('payment_method', 50)->nullable()->after('unit_price_cents');
            $table->foreignId('created_by')->nullable()->after('notes')->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            $table->timestamp('last_edited_at')->nullable()->after('updated_by');
            $table->index(['transaction_date', 'type'], 'project_cashflows_date_type_idx');
        });

        Schema::create('transaction_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_cashflow_id')->constrained('project_cashflows')->cascadeOnDelete();
            $table->string('disk', 30)->default('local');
            $table->string('file_path', 500);
            $table->string('original_name', 255);
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size')->default(0);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index('project_cashflow_id');
        });

        Schema::create('transaction_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_cashflow_id')->nullable()->constrained('project_cashflows')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('transaction_number', 40)->nullable();
            $table->string('action', 30); // created, updated, deleted
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name', 150)->nullable();
            $table->string('user_role', 50)->nullable();
            $table->json('before_data')->nullable();
            $table->json('after_data')->nullable();
            $table->json('changed_fields')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('recorded_at');
            $table->index(['project_cashflow_id', 'recorded_at', 'id'], 'audit_cashflow_recorded_idx');
            $table->index(['project_id', 'recorded_at', 'id'], 'audit_project_recorded_idx');
            $table->index(['recorded_at', 'id'], 'audit_recorded_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_audit_logs');
        Schema::dropIfExists('transaction_attachments');

        Schema::table('project_cashflows', function (Blueprint $table) {
            $table->dropIndex('project_cashflows_date_type_idx');
            $table->dropConstrainedForeignId('category_id');
            $table->dropConstrainedForeignId('created_by');
            $table->dropConstrainedForeignId('updated_by');
            $table->dropColumn(['transaction_number', 'receipt_number', 'payment_method', 'last_edited_at']);
        });

        Schema::dropIfExists('receipt_sequences');
        Schema::dropIfExists('transaction_categories');

        Schema::table('projects', function (Blueprint $table) {
            $table->dropUnique(['kode_proyek']);
            $table->dropColumn(['kode_proyek', 'tanggal_mulai', 'tanggal_selesai']);
        });
    }
};
