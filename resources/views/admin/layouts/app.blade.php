<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>@yield('title', 'Admin Dashboard') | PT Indo Berkah Konstruksi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <!-- Google Fonts: Source Sans 3 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" />
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    
    <!-- AdminLTE v4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta3/dist/css/adminlte.min.css" />

    <!-- ApexCharts CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css" />

    <style>
        :root {
            --ibk-gold: #C5A880;
            --ibk-gold-hover: #b19369;
            --ibk-dark: #111111;
            --ibk-dark-soft: #1a1a1a;
            --sidebar-width: 270px;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Source Sans 3', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f4f6f9;
        }

        /* Full App Wrapper Layout */
        .app-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: 100%;
            position: relative;
        }

        /* ==============================================
           SIDEBAR ARCHITECTURE (FIX BUG POTONG / BLEEDING)
           ============================================== */
        .app-sidebar {
            position: fixed !important;
            top: 0 !important;
            bottom: 0 !important;
            left: 0 !important;
            width: var(--sidebar-width) !important;
            height: 100vh !important;
            background-color: var(--ibk-dark) !important;
            z-index: 1045 !important;
            display: flex !important;
            flex-direction: column !important;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15) !important;
            border-right: 1px solid rgba(255, 255, 255, 0.06) !important;
            overflow: hidden !important;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        /* Brand Header Pinned */
        .sidebar-brand {
            flex-shrink: 0;
            padding: 1.15rem 1.25rem !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            background-color: #0c0c0c !important;
        }
        .sidebar-brand .brand-logo-badge {
            background: linear-gradient(135deg, var(--ibk-gold), #8f744e);
            color: #fff;
            font-weight: 800;
            padding: 6px 11px;
            border-radius: 8px;
            letter-spacing: 1px;
            font-size: 0.95rem;
            box-shadow: 0 4px 10px rgba(197, 168, 128, 0.3);
        }
        .sidebar-brand .brand-text {
            color: #ffffff;
            font-weight: 700;
            font-size: 1.05rem;
            letter-spacing: 0.5px;
            line-height: 1.2;
        }
        .sidebar-brand .brand-subtext {
            color: var(--ibk-gold);
            font-size: 0.72rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 700;
        }

        /* Scrollable Navigation Body */
        .sidebar-wrapper {
            flex: 1 1 auto !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            padding: 0.75rem 0.65rem 1.5rem !important;
        }
        .sidebar-wrapper::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-wrapper::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-wrapper::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 4px;
        }
        .sidebar-wrapper::-webkit-scrollbar-thumb:hover {
            background: var(--ibk-gold);
        }

        /* Sidebar Nav Items & Badges (FIX BUG BADGE NEMPEL) */
        .nav-header {
            color: rgba(255, 255, 255, 0.38) !important;
            font-size: 0.72rem !important;
            letter-spacing: 1.5px;
            font-weight: 700;
            padding: 1.25rem 0.85rem 0.4rem !important;
        }
        .nav-sidebar {
            width: 100% !important;
        }
        .nav-sidebar .nav-item {
            margin-bottom: 2px;
            width: 100%;
        }
        .nav-sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
            border-radius: 10px;
            padding: 0.65rem 0.85rem !important;
            font-size: 0.92rem;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
            border: 1px solid transparent;
        }
        .nav-sidebar .nav-link .nav-link-content {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
            flex-grow: 1;
        }
        .nav-sidebar .nav-link .nav-link-content i {
            font-size: 1.15rem;
            width: 22px;
            text-align: center;
            flex-shrink: 0;
            color: rgba(255, 255, 255, 0.6);
            transition: color 0.2s;
        }
        .nav-sidebar .nav-link .nav-link-content span {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .nav-sidebar .nav-link:hover {
            color: #ffffff;
            background-color: rgba(197, 168, 128, 0.12);
            border-color: rgba(197, 168, 128, 0.2);
        }
        .nav-sidebar .nav-link:hover .nav-link-content i {
            color: var(--ibk-gold);
        }
        .nav-sidebar .nav-link.active {
            background: linear-gradient(90deg, var(--ibk-gold), #a6885e) !important;
            color: #111111 !important;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(197, 168, 128, 0.35);
        }
        .nav-sidebar .nav-link.active .nav-link-content i {
            color: #111111 !important;
        }
        .nav-sidebar .nav-link .badge {
            margin-left: 8px !important;
            flex-shrink: 0;
            font-size: 0.72rem;
            padding: 0.3rem 0.55rem;
        }

        /* Pinned Sidebar Footer */
        .sidebar-footer {
            flex-shrink: 0;
            padding: 1rem 1.25rem;
            background-color: #0c0c0c;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* ==============================================
           CONTENT, HEADER & FOOTER OFFSET ON DESKTOP
           ============================================== */
        @media (min-width: 992px) {
            .app-header,
            .app-main,
            .app-footer {
                margin-left: var(--sidebar-width) !important;
                width: calc(100% - var(--sidebar-width)) !important;
            }
        }

        /* Mobile Sidebar Drawer */
        @media (max-width: 991.98px) {
            .app-sidebar {
                transform: translateX(-100%) !important;
            }
            body.sidebar-open .app-sidebar {
                transform: translateX(0) !important;
            }
            body.sidebar-open::after {
                content: "";
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.6);
                z-index: 1040;
                backdrop-filter: blur(2px);
            }
            .app-header,
            .app-main,
            .app-footer {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }

        /* Sticky Footer Guarantee */
        .app-main {
            flex: 1 0 auto !important;
            padding-bottom: 2rem;
        }
        .app-footer {
            flex-shrink: 0 !important;
            background-color: #ffffff;
            border-top: 1px solid #e2e8f0;
        }

        /* Cards & Components Styling */
        .card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 14px;
            box-shadow: 0 4px 18px -2px rgba(0, 0, 0, 0.04);
            margin-bottom: 1.5rem;
            background: #ffffff;
        }
        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            padding: 1.1rem 1.35rem;
            font-weight: 600;
        }

        /* Stats Cards (Info Boxes) */
        .ibk-stat-card {
            border-radius: 16px;
            padding: 1.35rem 1.25rem;
            position: relative;
            overflow: hidden;
            border: 0;
            color: #ffffff;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .ibk-stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -5px rgba(0, 0, 0, 0.15);
        }
        .ibk-stat-card .stat-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 3.5rem;
            opacity: 0.18;
        }
        .ibk-stat-card .stat-title {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            margin-bottom: 0.35rem;
            opacity: 0.9;
        }
        .ibk-stat-card .stat-value {
            font-size: 2.1rem;
            font-weight: 700;
            line-height: 1.1;
        }
        .ibk-stat-card .stat-desc {
            font-size: 0.8rem;
            margin-top: 0.5rem;
            opacity: 0.85;
        }

        /* Gradients */
        .bg-grad-gold {
            background: linear-gradient(135deg, #C5A880 0%, #8E6F45 100%);
        }
        .bg-grad-blue {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        }
        .bg-grad-green {
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
        }
        .bg-grad-purple {
            background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
        }

        .badge-gold {
            background-color: rgba(197, 168, 128, 0.18);
            color: #8b683a;
            border: 1px solid rgba(197, 168, 128, 0.4);
            font-weight: 600;
        }
        
        .app-header {
            background: #ffffff !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06) !important;
        }

        .table-modern thead th {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 700;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
            padding: 0.85rem 1rem;
        }
        .table-modern tbody td {
            padding: 0.85rem 1rem;
            vertical-align: middle;
            font-size: 0.92rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .btn-whatsapp {
            background-color: #25D366;
            color: #fff;
            border: 0;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-whatsapp:hover {
            background-color: #1eb956;
            color: #fff;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="app-wrapper">

        <!-- ==============================================
             NAVBAR HEADER
             ============================================== -->
        <nav class="app-header navbar navbar-expand bg-white py-2 px-3 shadow-none">
            <div class="container-fluid px-0">
                <!-- Left Nav -->
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item">
                        <button class="btn btn-link text-dark p-2 border-0" id="sidebarToggleBtn" type="button" aria-label="Toggle Navigation">
                            <i class="bi bi-list fs-3"></i>
                        </button>
                    </li>
                    <li class="nav-item d-none d-md-block ms-2">
                        <span class="badge bg-dark px-3 py-2 rounded-pill font-monospace" style="font-size: 0.78rem;">
                            <i class="bi bi-shield-check text-warning me-1"></i> ADMIN WORKSPACE
                        </span>
                    </li>
                    <li class="nav-item d-none d-sm-block ms-3">
                        <a href="/" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Buka Website Utama
                        </a>
                    </li>
                </ul>

                <!-- Right Nav -->
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    @php
                        $unreadQuotes = \App\Models\Penawaran::count();
                        $unreadSurat = \App\Models\SuratPerjanjian::where('status', 'pending')->count();
                    @endphp
                    <li class="nav-item dropdown">
                        <a class="nav-link text-dark position-relative p-2" data-bs-toggle="dropdown" href="#" title="Notifikasi">
                            <i class="bi bi-bell fs-5"></i>
                            @if($unreadQuotes + $unreadSurat > 0)
                                <span class="position-absolute top-1 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                                    {{ $unreadQuotes + $unreadSurat }}
                                </span>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end shadow-sm border-0 rounded-3 mt-2">
                            <span class="dropdown-item dropdown-header font-weight-bold text-muted">
                                {{ $unreadQuotes + $unreadSurat }} Notifikasi Masuk
                            </span>
                            <div class="dropdown-divider"></div>
                            <a href="/admin/penawaran" class="dropdown-item d-flex align-items-center py-2">
                                <i class="bi bi-file-earmark-text text-primary fs-5 me-3"></i>
                                <div>
                                    <div class="fw-semibold">{{ $unreadQuotes }} Penawaran Biaya</div>
                                    <small class="text-muted">Dari calon klien</small>
                                </div>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="/admin/surat-perjanjian" class="dropdown-item d-flex align-items-center py-2">
                                <i class="bi bi-file-earmark-check text-warning fs-5 me-3"></i>
                                <div>
                                    <div class="fw-semibold">{{ $unreadSurat }} Surat Perjanjian Pending</div>
                                    <small class="text-muted">Menunggu verifikasi</small>
                                </div>
                            </a>
                        </div>
                    </li>

                    <!-- User Profile Dropdown -->
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle text-dark d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" 
                                 style="width: 36px; height: 36px; background: linear-gradient(135deg, #111111, #333333); font-size: 0.85rem; border: 2px solid var(--ibk-gold);">
                                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
                            </div>
                            <span class="d-none d-md-inline fw-semibold" style="font-size: 0.9rem;">
                                {{ auth()->user()->name ?? 'Administrator' }}
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end shadow-sm border-0 rounded-3 mt-2">
                            <li class="p-3 text-center bg-dark text-white rounded-top-3" style="background: linear-gradient(135deg, #111111, #242424) !important;">
                                <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center text-white fw-bold mb-2 shadow" 
                                     style="width: 50px; height: 50px; background: var(--ibk-gold); font-size: 1.2rem;">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                                </div>
                                <p class="mb-0 fw-bold">{{ auth()->user()->name ?? 'Admin IBK' }}</p>
                                <small class="text-white-50">{{ auth()->user()->email ?? 'admin@indoberkah.com' }}</small>
                                <div class="mt-2">
                                    <span class="badge bg-warning text-dark font-monospace px-3 py-1">SUPER ADMIN</span>
                                </div>
                            </li>
                            <li class="p-2">
                                <a href="/" class="dropdown-item rounded-2 py-2">
                                    <i class="bi bi-globe me-2 text-primary"></i> Kunjungi Website
                                </a>
                                <a href="/filament" class="dropdown-item rounded-2 py-2">
                                    <i class="bi bi-grid-fill me-2 text-warning"></i> Filament Resource Panel
                                </a>
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="/logout">
                                    @csrf
                                    <button type="submit" class="dropdown-item rounded-2 py-2 text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i> Keluar (Logout)
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- ==============================================
             SIDEBAR (FULL HEIGHT PINNED & SCROLLABLE)
             ============================================== -->
        <aside class="app-sidebar" id="appSidebar">
            <!-- Sidebar Brand Header -->
            <div class="sidebar-brand">
                <a href="/admin" class="d-flex align-items-center text-decoration-none">
                    <span class="brand-logo-badge me-2">IBK</span>
                    <div>
                        <div class="brand-text">INDO BERKAH</div>
                        <div class="brand-subtext">Konstruksi Panel</div>
                    </div>
                </a>
            </div>

            <!-- Sidebar Navigation Body -->
            <div class="sidebar-wrapper">
                <ul class="nav nav-pills nav-sidebar flex-column">
                    
                    <li class="nav-item">
                        <a href="/admin" class="nav-link {{ request()->is('admin') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="bi bi-speedometer2"></i>
                                <span>Dashboard Utama</span>
                            </div>
                        </a>
                    </li>

                    <!-- GROUP: OPERASIONAL PROYEK -->
                    <li class="nav-header">OPERASIONAL PROYEK</li>
                    
                    <li class="nav-item">
                        <a href="/admin/projects" class="nav-link {{ request()->is('admin/projects*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="bi bi-building-gear"></i>
                                <span>Daftar Proyek</span>
                            </div>
                            @php $projCount = \App\Models\Project::count(); @endphp
                            @if($projCount > 0)
                                <span class="badge bg-warning text-dark rounded-pill">{{ $projCount }}</span>
                            @endif
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/admin/user-files" class="nav-link {{ request()->is('admin/user-files*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="bi bi-images"></i>
                                <span>File & Desain Klien</span>
                            </div>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/admin/feedbacks" class="nav-link {{ request()->is('admin/feedbacks*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="bi bi-chat-left-dots"></i>
                                <span>Masukan Lapangan</span>
                            </div>
                            @php $pendingFb = \App\Models\UserFeedback::where('status', 'pending')->count(); @endphp
                            @if($pendingFb > 0)
                                <span class="badge bg-danger text-white rounded-pill">{{ $pendingFb }}</span>
                            @endif
                        </a>
                    </li>

                    <!-- GROUP: PENJUALAN & KLIEN -->
                    <li class="nav-header">PENJUALAN & KLIEN</li>

                    <li class="nav-item">
                        <a href="/admin/penawaran" class="nav-link {{ request()->is('admin/penawaran*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="bi bi-cash-coin"></i>
                                <span>Penawaran (RFQ)</span>
                            </div>
                            @php $quotesCount = \App\Models\Penawaran::count(); @endphp
                            @if($quotesCount > 0)
                                <span class="badge bg-primary text-white rounded-pill">{{ $quotesCount }}</span>
                            @endif
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/admin/surat-perjanjian" class="nav-link {{ request()->is('admin/surat-perjanjian*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="bi bi-file-earmark-lock"></i>
                                <span>Surat Perjanjian (SPK)</span>
                            </div>
                            @if($unreadSurat > 0)
                                <span class="badge bg-warning text-dark rounded-pill">{{ $unreadSurat }}</span>
                            @endif
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/admin/users" class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="bi bi-people"></i>
                                <span>Data Pengguna & Klien</span>
                            </div>
                        </a>
                    </li>

                    <!-- GROUP: KONTEN WEBSITE -->
                    <li class="nav-header">KONTEN WEBSITE PUBLIK</li>

                    <li class="nav-item">
                        <a href="/admin/portfolios" class="nav-link {{ request()->is('admin/portfolios*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="bi bi-award"></i>
                                <span>Portofolio Proyek</span>
                            </div>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/admin/berita" class="nav-link {{ request()->is('admin/berita*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="bi bi-newspaper"></i>
                                <span>Berita & Artikel</span>
                            </div>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/admin/reviews" class="nav-link {{ request()->is('admin/reviews*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="bi bi-star-half"></i>
                                <span>Review & Testimoni</span>
                            </div>
                            @php $pendingRev = \App\Models\Review::where('is_approved', false)->count(); @endphp
                            @if($pendingRev > 0)
                                <span class="badge bg-warning text-dark rounded-pill">{{ $pendingRev }}</span>
                            @endif
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/admin/team" class="nav-link {{ request()->is('admin/team*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="bi bi-person-badge"></i>
                                <span>Tim & Pendaftar Join Us</span>
                            </div>
                        </a>
                    </li>

                    <!-- GROUP: SISTEM -->
                    <li class="nav-header">SISTEM & TOOLS</li>

                    <li class="nav-item">
                        <a href="/admin/settings" class="nav-link {{ request()->is('admin/settings*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="bi bi-sliders"></i>
                                <span>Pengaturan Website</span>
                            </div>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/filament" target="_blank" class="nav-link">
                            <div class="nav-link-content">
                                <i class="bi bi-lightning-charge text-warning"></i>
                                <span>Panel Filament (Alt)</span>
                            </div>
                        </a>
                    </li>

                </ul>
            </div>

            <!-- Pinned Sidebar Footer -->
            <div class="sidebar-footer">
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100 rounded-pill d-flex align-items-center justify-content-center gap-2 py-2">
                        <i class="bi bi-box-arrow-left"></i>
                        <span class="fw-semibold">Keluar (Logout)</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- ==============================================
             MAIN CONTENT
             ============================================== -->
        <main class="app-main">
            <!-- App Content Header -->
            <div class="app-content-header py-3">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <h3 class="mb-0 fw-bold text-dark">@yield('page-title', 'Dashboard')</h3>
                            <small class="text-muted">@yield('page-subtitle', 'Panel Kendali Operasional PT Indo Berkah Konstruksi')</small>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end mb-0 bg-transparent p-0">
                                <li class="breadcrumb-item"><a href="/admin" class="text-decoration-none text-muted">Home</a></li>
                                @yield('breadcrumb')
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flash Notifications -->
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2 mb-4" role="alert">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2 mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>

            <!-- Page Content Injection -->
            <div class="app-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
        </main>

        <!-- ==============================================
             STICKY FOOTER
             ============================================== -->
        <footer class="app-footer text-muted py-3 px-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <strong>Copyright &copy; 2026 <a href="/" class="text-decoration-none text-dark fw-bold">PT Indo Berkah Konstruksi</a>.</strong>
                    Seluruh hak cipta dilindungi.
                </div>
                <div class="d-none d-sm-inline">
                    <span class="badge bg-light text-dark border px-3 py-1 font-monospace">AdminLTE v4 Enterprise Edition</span>
                </div>
            </div>
        </footer>

    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"></script>

    <!-- Mobile Sidebar Toggle Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const toggleBtn = document.getElementById("sidebarToggleBtn");
            if (toggleBtn) {
                toggleBtn.addEventListener("click", function (e) {
                    e.preventDefault();
                    document.body.classList.toggle("sidebar-open");
                });
            }

            // Close sidebar when clicking outside on mobile
            document.addEventListener("click", function (e) {
                if (document.body.classList.contains("sidebar-open")) {
                    const sidebar = document.getElementById("appSidebar");
                    const toggleBtn = document.getElementById("sidebarToggleBtn");
                    if (sidebar && !sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                        document.body.classList.remove("sidebar-open");
                    }
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
