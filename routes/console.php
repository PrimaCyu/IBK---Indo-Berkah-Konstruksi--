<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('make:admin {email=admin@admin.com} {password=admin123} {name=Administrator}', function () {
    $email = $this->argument('email');
    $password = $this->argument('password');
    $name = $this->argument('name');

    $user = \App\Models\User::updateOrCreate(
        ['email' => $email],
        [
            'name' => $name,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]
    );

    $this->info("User admin berhasil disiapkan:");
    $this->line("Nama  : {$user->name}");
    $this->line("Email : {$user->email}");
    $this->line("Role  : {$user->role}");
})->purpose('Buat atau perbarui akun admin');

Artisan::command('user:check', function () {
    $users = \App\Models\User::all(['id', 'name', 'email', 'role']);
    $this->table(['ID', 'Nama', 'Email', 'Role'], $users->toArray());
})->purpose('Cek daftar akun user');

Artisan::command('project:check', function () {
    $projects = \App\Models\Project::with('user')->get();
    $rows = $projects->map(fn ($p) => [
        'ID' => $p->id,
        'Nama Proyek' => $p->nama_proyek,
        'Klien' => $p->user?->name ?? '-',
        'Alamat' => $p->alamat_proyek,
        'Total Kas' => $p->cashflows()->count(),
        'Pending Kas' => $p->cashflows()->where('status', 'pending')->count(),
    ]);
    $this->table(['ID', 'Nama Proyek', 'Klien', 'Alamat', 'Total Kas', 'Pending Kas'], $rows->toArray());
})->purpose('Cek daftar proyek dan jumlah kas');

Artisan::command('cashflow:seed-all', function () {
    $this->call(\Database\Seeders\AllProjectsCashflowSeeder::class);
    $this->info("Data kas proyek berhasil di-generate!");
    $this->call('project:check');
})->purpose('Isi data keuangan dummy untuk semua proyek');

Artisan::command('cashbook:summary', function () {
    $totalPending = \App\Models\ProjectCashflow::where('status', 'pending')->count();
    $totalLunas = \App\Models\ProjectCashflow::where('status', 'lunas')->count();
    $this->info("Total Transaksi Lunas   : {$totalLunas}");
    $this->info("Total Transaksi Pending : {$totalPending}");
})->purpose('Cek ringkasan transaksi buku kas');

Artisan::command('berita:dummy', function () {
    $this->call(\Database\Seeders\BeritaDummySeeder::class);
    $this->info("10 berita dummy berhasil ditambahkan!");
})->purpose('Generate 10 berita dummy beserta kategori dan views');



