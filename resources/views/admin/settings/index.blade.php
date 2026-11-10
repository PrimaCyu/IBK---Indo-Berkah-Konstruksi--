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

            <div class="card mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <ul class="nav nav-pills gap-2" id="settingTabs" role="tablist">
                        <li class="nav-item">
                            <button type="button" class="nav-link active px-3 py-2 fw-semibold" id="tab-hero-link" data-bs-toggle="pill" data-bs-target="#tab-hero" role="tab">
                                <i class="fas fa-home me-2 text-primary"></i>Hero Beranda
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link px-3 py-2 fw-semibold" id="tab-about-link" data-bs-toggle="pill" data-bs-target="#tab-about" role="tab">
                                <i class="fas fa-info-circle me-2 text-info"></i>Tentang Perusahaan
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link px-3 py-2 fw-semibold" id="tab-contact-link" data-bs-toggle="pill" data-bs-target="#tab-contact" role="tab">
                                <i class="fas fa-address-book me-2 text-emerald"></i>Kontak & Medsos
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content" id="settingTabsContent">
                        
                        <!-- TAB 1: HERO BERANDA -->
                        <div class="tab-pane fade show active" id="tab-hero" role="tabpanel">
                            <h6 class="fw-bold text-dark mb-3">Pengaturan Banner Utama (Hero Section)</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-bold">JUDUL UTAMA (BARIS 1)</label>
                                    <input type="text" name="hero_title_line1" class="form-control" value="{{ $settings['hero_title_line1'] ?? 'Menjaga Kualitas' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-bold">JUDUL UTAMA (BARIS 2)</label>
                                    <input type="text" name="hero_title_line2" class="form-control" value="{{ $settings['hero_title_line2'] ?? 'mewujud' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-bold">JUDUL UTAMA (BARIS 3)</label>
                                    <input type="text" name="hero_title_line3" class="form-control" value="{{ $settings['hero_title_line3'] ?? 'Berkah.' }}">
                                </div>

                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold">SUBJUDUL / DESKRIPSI HERO</label>
                                    <textarea name="hero_subtitle" class="form-control" rows="3">{{ $settings['hero_subtitle'] ?? 'Membangun lingkungan masa depan yang berdampak. Kami memadukan estetika modern dengan ketahanan struktur tak tertandingi.' }}</textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">TEKS BADGE KUALITAS</label>
                                    <input type="text" name="hero_badge_title" class="form-control" value="{{ $settings['hero_badge_title'] ?? 'Fokus pada Kualitas' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">SUBJUDUL BADGE</label>
                                    <input type="text" name="hero_badge_subtitle" class="form-control" value="{{ $settings['hero_badge_subtitle'] ?? 'Presisi tingkat tinggi dalam setiap tahap konstruksi.' }}">
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: TENTANG KAMI -->
                        <div class="tab-pane fade" id="tab-about" role="tabpanel">
                            <h6 class="fw-bold text-dark mb-3">Profil & Informasi Perusahaan</h6>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label text-muted small fw-bold">LABEL SEKSI TENTANG</label>
                                    <input type="text" name="about_label" class="form-control" value="{{ $settings['about_label'] ?? 'Tentang BestBuild Indo Berkah' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-bold">JUDUL TENTANG (BARIS 1)</label>
                                    <input type="text" name="about_title_line1" class="form-control" value="{{ $settings['about_title_line1'] ?? 'Pelopor Kualitas' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-bold">JUDUL TENTANG (BARIS 2)</label>
                                    <input type="text" name="about_title_line2" class="form-control" value="{{ $settings['about_title_line2'] ?? 'dan Keunggulan' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-bold">JUDUL TENTANG (BARIS 3)</label>
                                    <input type="text" name="about_title_line3" class="form-control" value="{{ $settings['about_title_line3'] ?? 'dalam Setiap Proyek' }}">
                                </div>

                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold">DESKRIPSI PROFIL PERUSAHAAN</label>
                                    <textarea name="about_description" class="form-control" rows="4">{{ $settings['about_description'] ?? 'INDO BERKAH KONSTRUKSI adalah perusahaan jasa konstruksi yang menyediakan layanan pembangunan rumah, gedung, infrastruktur, renovasi, serta konstruksi besi dan baja.' }}</textarea>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-bold">PENGALAMAN (TAHUN)</label>
                                    <input type="text" name="about_experience" class="form-control" value="{{ $settings['about_experience'] ?? '10+' }}">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label text-muted small fw-bold">KETERANGAN PENGALAMAN</label>
                                    <input type="text" name="about_experience_text" class="form-control" value="{{ $settings['about_experience_text'] ?? 'Tahun Pengalaman Membangun Kepercayaan' }}">
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: KONTAK & MEDSOS -->
                        <div class="tab-pane fade" id="tab-contact" role="tabpanel">
                            <h6 class="fw-bold text-dark mb-3">Kontak Resmi & Media Sosial</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold"><i class="fas fa-phone-alt me-1 text-primary"></i> NO. TELEPON / WHATSAPP</label>
                                    <input type="text" name="footer_telepon" class="form-control" value="{{ $settings['footer_telepon'] ?? '+62 878 6530 9966' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold"><i class="fas fa-envelope me-1 text-primary"></i> EMAIL RESMI PERUSAHAAN</label>
                                    <input type="email" name="footer_email" class="form-control" value="{{ $settings['footer_email'] ?? 'partners@indoberkahkonstruksi.com' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold"><i class="fab fa-facebook me-1 text-primary"></i> URL FACEBOOK</label>
                                    <input type="url" name="footer_facebook" class="form-control" value="{{ $settings['footer_facebook'] ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold"><i class="fab fa-instagram me-1 text-danger"></i> URL INSTAGRAM</label>
                                    <input type="url" name="footer_instagram" class="form-control" value="{{ $settings['footer_instagram'] ?? '' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold">TAGLINE FOOTER</label>
                                    <input type="text" name="footer_tagline" class="form-control" value="{{ $settings['footer_tagline'] ?? 'Menjaga Kualitas Mewujud Berkah' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold">DESKRIPSI RINGKAS (FOOTER)</label>
                                    <textarea name="footer_company_text" class="form-control" rows="2">{{ $settings['footer_company_text'] ?? 'Menghadirkan mahakarya arsitektur dan konstruksi dengan standar kualitas premium, presisi, dan dedikasi penuh.' }}</textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                    <span class="text-muted small"><i class="fas fa-check-circle text-success me-1"></i> Perubahan langsung diterapkan ke tampilan publik.</span>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Simpan Pengaturan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
