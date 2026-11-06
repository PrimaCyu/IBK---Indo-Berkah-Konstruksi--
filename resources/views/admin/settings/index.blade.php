@extends('admin.layouts.app')

@section('title', 'Pengaturan Website')
@section('page-title', 'Pengaturan Website')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item active">Pengaturan</li>
@endsection

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-10">
        <form action="/admin/settings" method="POST">
            @csrf
            @method('PUT')

            <div class="card card-primary card-outline card-outline-tabs">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" id="settingTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active fw-semibold" id="tab-hero-link" data-bs-toggle="pill" href="#tab-hero" role="tab">
                                <i class="fas fa-home me-1"></i> Hero Beranda
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-semibold" id="tab-about-link" data-bs-toggle="pill" href="#tab-about" role="tab">
                                <i class="fas fa-info-circle me-1"></i> Tentang Perusahaan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-semibold" id="tab-contact-link" data-bs-toggle="pill" href="#tab-contact" role="tab">
                                <i class="fas fa-address-book me-1"></i> Kontak & Media Sosial
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="settingTabsContent">
                        
                        <!-- TAB 1: HERO BERANDA -->
                        <div class="tab-pane fade show active" id="tab-hero" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Judul Utama (Baris 1)</label>
                                    <input type="text" name="hero_title_line1" class="form-control form-control-sm" value="{{ $settings['hero_title_line1'] ?? 'Menjaga Kualitas' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Judul Utama (Baris 2)</label>
                                    <input type="text" name="hero_title_line2" class="form-control form-control-sm" value="{{ $settings['hero_title_line2'] ?? 'mewujud' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Judul Utama (Baris 3)</label>
                                    <input type="text" name="hero_title_line3" class="form-control form-control-sm" value="{{ $settings['hero_title_line3'] ?? 'Berkah.' }}">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Subjudul / Deskripsi Hero</label>
                                    <textarea name="hero_subtitle" class="form-control form-control-sm" rows="3">{{ $settings['hero_subtitle'] ?? 'Membangun lingkungan masa depan yang berdampak. Kami memadukan estetika modern dengan ketahanan struktur tak tertandingi.' }}</textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Teks Badge Kualitas</label>
                                    <input type="text" name="hero_badge_title" class="form-control form-control-sm" value="{{ $settings['hero_badge_title'] ?? 'Fokus pada Kualitas' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Subjudul Badge</label>
                                    <input type="text" name="hero_badge_subtitle" class="form-control form-control-sm" value="{{ $settings['hero_badge_subtitle'] ?? 'Presisi tingkat tinggi dalam setiap tahap konstruksi.' }}">
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: TENTANG KAMI -->
                        <div class="tab-pane fade" id="tab-about" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Label Seksi Tentang</label>
                                    <input type="text" name="about_label" class="form-control form-control-sm" value="{{ $settings['about_label'] ?? 'Tentang BestBuild Indo Berkah' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Judul Tentang (Baris 1)</label>
                                    <input type="text" name="about_title_line1" class="form-control form-control-sm" value="{{ $settings['about_title_line1'] ?? 'Pelopor Kualitas' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Judul Tentang (Baris 2)</label>
                                    <input type="text" name="about_title_line2" class="form-control form-control-sm" value="{{ $settings['about_title_line2'] ?? 'dan Keunggulan' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Judul Tentang (Baris 3)</label>
                                    <input type="text" name="about_title_line3" class="form-control form-control-sm" value="{{ $settings['about_title_line3'] ?? 'dalam Setiap Proyek' }}">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Deskripsi Profil Perusahaan</label>
                                    <textarea name="about_description" class="form-control form-control-sm" rows="4">{{ $settings['about_description'] ?? 'INDO BERKAH KONSTRUKSI adalah perusahaan jasa konstruksi yang menyediakan layanan pembangunan rumah, gedung, infrastruktur, renovasi, serta konstruksi besi dan baja.' }}</textarea>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Pengalaman (Tahun)</label>
                                    <input type="text" name="about_experience" class="form-control form-control-sm" value="{{ $settings['about_experience'] ?? '10+' }}">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Teks Keterangan Pengalaman</label>
                                    <input type="text" name="about_experience_text" class="form-control form-control-sm" value="{{ $settings['about_experience_text'] ?? 'Tahun Pengalaman Membangun Kepercayaan' }}">
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: KONTAK & MEDSOS -->
                        <div class="tab-pane fade" id="tab-contact" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label"><i class="fas fa-phone-alt me-1 text-primary"></i> Nomor Telepon / WhatsApp Kantor</label>
                                    <input type="text" name="footer_telepon" class="form-control form-control-sm" value="{{ $settings['footer_telepon'] ?? '+62 878 6530 9966' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="fas fa-envelope me-1 text-primary"></i> Email Resmi Perusahaan</label>
                                    <input type="email" name="footer_email" class="form-control form-control-sm" value="{{ $settings['footer_email'] ?? 'partners@indoberkahkonstruksi.com' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="fab fa-facebook me-1 text-primary"></i> URL Akun Facebook</label>
                                    <input type="url" name="footer_facebook" class="form-control form-control-sm" value="{{ $settings['footer_facebook'] ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="fab fa-instagram me-1 text-danger"></i> URL Akun Instagram</label>
                                    <input type="url" name="footer_instagram" class="form-control form-control-sm" value="{{ $settings['footer_instagram'] ?? '' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Teks Tagline Footer</label>
                                    <input type="text" name="footer_tagline" class="form-control form-control-sm" value="{{ $settings['footer_tagline'] ?? 'Menjaga Kualitas Mewujud Berkah' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Deskripsi Ringkas Perusahaan (Footer)</label>
                                    <textarea name="footer_company_text" class="form-control form-control-sm" rows="2">{{ $settings['footer_company_text'] ?? 'Menghadirkan mahakarya arsitektur dan konstruksi dengan standar kualitas premium, presisi, dan dedikasi penuh.' }}</textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Semua perubahan langsung diterapkan ke website publik.</span>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Semua Pengaturan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
