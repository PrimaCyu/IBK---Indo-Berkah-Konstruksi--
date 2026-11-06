@extends('admin.layouts.app')

@section('title', 'Pengaturan Website')
@section('page-title', 'Pengaturan Sistem & Branding')
@section('page-subtitle', 'Konfigurasi teks hero utama, nomor kontak, dan informasi publik PT Indo Berkah Konstruksi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item active">Pengaturan</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-sliders text-primary me-2"></i> Konfigurasi Tampilan Halaman Depan
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="/admin/settings" method="POST">
                    @csrf
                    @method('PUT')

                    <h6 class="fw-bold text-uppercase text-muted small mb-3">Teks Hero Banner (Halaman Utama)</h6>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Judul Baris 1</label>
                            <input type="text" name="hero_title_line1" class="form-control" value="{{ $settings['hero_title_line1'] ?? 'Menjaga Kualitas' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Judul Baris 2 (Italic)</label>
                            <input type="text" name="hero_title_line2" class="form-control" value="{{ $settings['hero_title_line2'] ?? 'mewujud' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Judul Baris 3</label>
                            <input type="text" name="hero_title_line3" class="form-control" value="{{ $settings['hero_title_line3'] ?? 'Berkah.' }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Deskripsi Subtitle Hero</label>
                        <textarea name="hero_subtitle" class="form-control" rows="3">{{ $settings['hero_subtitle'] ?? 'Membangun lingkungan masa depan yang berdampak. Kami memadukan estetika modern dengan ketahanan struktur tak tertandingi.' }}</textarea>
                    </div>

                    <h6 class="fw-bold text-uppercase text-muted small mb-3">Badge & Komitmen Kualitas</h6>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Teks Badge Kualitas</label>
                        <input type="text" name="hero_badge_title" class="form-control" value="{{ $settings['hero_badge_title'] ?? 'Fokus pada Kualitas' }}">
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-warning px-4 py-2 fw-bold" style="background-color: var(--ibk-gold); border-color: var(--ibk-gold); color: #111;">
                            <i class="bi bi-save me-1"></i> Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

