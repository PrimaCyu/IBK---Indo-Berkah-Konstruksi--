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

