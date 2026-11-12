<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>@yield('title', 'Admin Panel') | PT Indo Berkah Konstruksi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <!-- Google Font: Plus Jakarta Sans / Source Sans 3 -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    
    <!-- AdminLTE v4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta3/dist/css/adminlte.min.css" />

    <style>
        :root {
            --lte-sidebar-width: 250px;
            --lte-brand-bg: #181b1f;
            --lte-sidebar-bg: #212529;
            --lte-sidebar-hover: rgba(255, 255, 255, 0.08);
            --lte-sidebar-active: #0d6efd;
            --lte-body-bg: #f8fafc;
            --lte-card-border: #e2e8f0;
            --lte-header-height: 60px;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background-color: var(--lte-body-bg);
            color: #1e293b;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
            max-width: 100%;
        }

        /* Layout Structure */
        .app-wrapper {
            display: flex !important;
            flex-direction: column !important;
            min-height: 100vh !important;
            width: 100% !important;
            position: relative !important;
        }

        /* Fixed Sidebar */
        .app-sidebar {
            position: fixed !important;
            top: 0 !important;
            bottom: 0 !important;
            left: 0 !important;
            width: var(--lte-sidebar-width) !important;
            height: 100vh !important;
            background-color: var(--lte-sidebar-bg) !important;
            z-index: 1040 !important;
            display: flex !important;
            flex-direction: column !important;
            border-right: 1px solid #2d3238 !important;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.08) !important;
            transition: transform 0.25s ease-in-out, margin 0.25s ease-in-out !important;
        }

        /* Brand */
        .sidebar-brand {
            flex-shrink: 0;
            height: var(--lte-header-height);
            padding: 0 1.15rem;
            display: flex;
            align-items: center;
            background-color: var(--lte-brand-bg);
            border-bottom: 1px solid #2d3238;
        }
        .sidebar-brand a {
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        .sidebar-brand img {
            display: block;
            width: 230px;
            max-width: 100%;
            height: auto;
            max-height: 54px;
            object-fit: contain;
        }

        /* Sidebar Mini User Profile */
        .sidebar-user {
            flex-shrink: 0;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid #2d3238;
            display: flex;
            align-items: center;
            gap: 12px;
            background-color: rgba(0, 0, 0, 0.15);
        }
        .sidebar-user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background-color: #3b424a;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 700;
            flex-shrink: 0;
        }
        .sidebar-user-info {
            line-height: 1.25;
            min-width: 0;
            flex: 1;
        }
        .sidebar-user-name {
            color: #f8fafc;
            font-size: 0.85rem;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        /* Scrollable Sidebar Nav */
        .sidebar-wrapper {
            flex: 1 1 auto;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0.75rem 0.5rem 1.5rem;
        }
        .sidebar-wrapper::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-wrapper::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 2px;
        }

        /* Nav Items */
        .nav-header {
            color: #8a94a0 !important;
            font-size: 0.68rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.8px !important;
            padding: 0.85rem 0.75rem 0.35rem !important;
        }
        .nav-sidebar .nav-item {
            margin-bottom: 2px;
            width: 100%;
        }
        .nav-sidebar .nav-link {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding: 0.5rem 0.75rem !important;
            font-size: 0.875rem !important;
            color: #c2c7d0 !important;
            border-radius: 6px !important;
            text-decoration: none !important;
            transition: background-color 0.15s, color 0.15s;
        }
        .nav-sidebar .nav-link .nav-left {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
            flex-grow: 1;
        }
        .nav-sidebar .nav-link .nav-left i {
            width: 20px;
            text-align: center;
            font-size: 0.95rem;
            color: #8e949e;
            flex-shrink: 0;
        }
        .nav-sidebar .nav-link .nav-left span {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .nav-sidebar .nav-link:hover {
            color: #ffffff !important;
            background-color: var(--lte-sidebar-hover) !important;
        }
        .nav-sidebar .nav-link:hover .nav-left i {
            color: #ffffff;
        }
        .nav-sidebar .nav-link.active {
            background-color: var(--lte-sidebar-active) !important;
            color: #ffffff !important;
            font-weight: 600 !important;
        }
        .nav-sidebar .nav-link.active .nav-left i {
            color: #ffffff !important;
        }
        .nav-sidebar .nav-link .badge {
            font-size: 0.7rem;
            padding: 0.22rem 0.45rem;
            margin-left: auto;
            flex-shrink: 0;
        }

        /* Sidebar Footer (Logout) */
        .sidebar-footer {
            flex-shrink: 0;
            padding: 0.75rem 1rem;
            background-color: var(--lte-brand-bg);
            border-top: 1px solid #2d3238;
        }

        /* Desktop Layout Offset */
        @media (min-width: 992px) {
            .app-header,
            .app-main,
            .app-footer {
                margin-left: var(--lte-sidebar-width) !important;
                width: calc(100% - var(--lte-sidebar-width)) !important;
                transition: margin-left 0.25s ease-in-out, width 0.25s ease-in-out !important;
            }
            body.sidebar-collapse .app-sidebar {
                transform: translateX(-100%) !important;
            }
            body.sidebar-collapse .app-header,
            body.sidebar-collapse .app-main,
            body.sidebar-collapse .app-footer {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }

        /* Mobile Layout */
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
                background: rgba(0, 0, 0, 0.5);
                z-index: 1035;
            }
            .app-header,
            .app-main,
            .app-footer {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }

        /* ==============================================
           POLISHED MODERN HEADER NAVBAR (REF: MINIMAL CLEAN)
           ============================================== */
        .app-header {
            height: var(--lte-header-height);
            background-color: #ffffff;
            border-bottom: 1px solid #eef2f6;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.02);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            flex-shrink: 0;
            z-index: 1030;
        }

        .header-page-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.2px;
            margin: 0;
            white-space: nowrap;
        }

        .btn-nav-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 5px 15px;
            font-size: 0.825rem;
            font-weight: 600;
            color: #334155;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            text-decoration: none;
            transition: all 0.15s ease;
            cursor: pointer;
            line-height: 1.4;
        }
        .btn-nav-pill:hover {
            background-color: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .header-greeting {
            font-size: 0.85rem;
            color: #64748b;
            margin: 0;
            white-space: nowrap;
        }
        .header-greeting strong {
            color: #0f172a;
            font-weight: 700;
        }

        /* Sleek Button Icon */
        .nav-btn-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            color: #475569;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            position: relative;
        }
        .nav-btn-icon:hover {
            background-color: #f8fafc;
            color: #0d6efd;
            border-color: #cbd5e1;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }

        /* Notif Badge */
        .notif-badge-pill {
            position: absolute;
            top: -4px;
            right: -4px;
            background-color: #ef4444;
            color: #ffffff;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 2px 5px;
            border-radius: 10px;
            border: 2px solid #ffffff;
            line-height: 1;
        }

        /* Custom Dropdowns */
        .custom-dropdown-menu {
            border-radius: 12px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04) !important;
            padding: 0.4rem !important;
        }
        .custom-dropdown-item {
            border-radius: 8px !important;
            padding: 0.5rem 0.75rem !important;
            font-size: 0.85rem !important;
            color: #334155 !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            transition: background-color 0.15s !important;
            text-decoration: none;
        }
        .custom-dropdown-item:hover {
            background-color: #f1f5f9 !important;
            color: #0d6efd !important;
        }
        .custom-dropdown-item.text-danger:hover {
            background-color: #fef2f2 !important;
            color: #dc2626 !important;
        }

        /* Main Area */
        .app-main {
            flex: 1 0 auto;
            padding-bottom: 2.5rem;
            min-height: calc(100vh - var(--lte-header-height) - 48px);
        }
        .content-header {
            padding: 1.15rem 0 0.75rem;
        }
        .content-header h1 {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0;
            color: #0f172a;
            letter-spacing: -0.3px;
        }

        /* Footer */
        .app-footer {
            flex-shrink: 0;
            height: 48px;
            background-color: #ffffff;
            border-top: 1px solid #eef2f6;
            padding: 0 1.5rem;
            font-size: 0.825rem;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* ==============================================
           CLEAN MODERN DESIGN SYSTEM (BASED ON REFERENCE)
           ============================================== */
        /* Cards */
        .card {
            background-color: #ffffff;
            border: 1px solid #eef2f6;
            border-radius: 16px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.02);
            margin-bottom: 1.25rem;
            transition: box-shadow 0.2s ease;
        }
        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #f8fafc;
            padding: 1.15rem 1.4rem;
            font-weight: 700;
            font-size: 0.95rem;
            color: #0f172a;
            border-top-left-radius: 16px !important;
            border-top-right-radius: 16px !important;
        }
        .card-body {
            padding: 1.35rem 1.4rem;
        }
        .card-footer {
            background-color: #fafbfc;
            border-top: 1px solid #f8fafc;
            padding: 1rem 1.4rem;
            border-bottom-left-radius: 16px !important;
            border-bottom-right-radius: 16px !important;
        }

        /* Form Labels & Controls */
        .form-label, .filter-label {
            font-size: 0.72rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            color: #64748b !important;
            margin-bottom: 0.35rem !important;
            display: block;
        }
        .form-control, .form-select {
            border-radius: 10px !important;
            border: 1px solid #e2e8f0 !important;
            height: 38px !important;
            font-size: 0.85rem !important;
            padding: 0.45rem 0.85rem !important;
            background-color: #ffffff !important;
            color: #1e293b !important;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12) !important;
            outline: none;
        }
        .form-control::placeholder {
            color: #94a3b8;
            font-size: 0.825rem;
        }

        /* Filter Segmented Pill Group (e.g. Harian / Bulanan / Tahunan) */
        .filter-pill-group {
            display: inline-flex;
            align-items: center;
            background: #f1f5f9;
            padding: 3px;
            border-radius: 10px;
            gap: 2px;
            height: 38px;
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .filter-pill-item {
            border: none;
            background: transparent;
            padding: 4px 14px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #64748b;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s;
            line-height: 1.5;
            white-space: nowrap;
        }
        .filter-pill-item:hover {
            color: #1e293b;
        }
        .filter-pill-item.active {
            background: #ffffff;
            color: #be123c;
            font-weight: 700;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        /* Filter Reset Button */
        .btn-filter-reset {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            height: 38px;
            padding: 0 14px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #475569;
            font-size: 0.825rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.15s;
            cursor: pointer;
        }
        .btn-filter-reset:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
        }

        /* Modern Tables */
        .table {
            margin-bottom: 0;
            font-size: 0.85rem;
            --bs-table-bg: transparent;
        }
        .table thead th {
            font-weight: 700;
            color: #64748b;
            background-color: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 0.95rem 1.15rem;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            white-space: nowrap;
        }
        .table tbody td {
            padding: 0.95rem 1.15rem;
            vertical-align: middle;
            border-bottom: 1px solid #f8fafc;
            color: #334155;
            background-color: #ffffff;
        }
        .table-hover tbody tr:hover td {
            background-color: #fbfcfe !important;
        }

        /* Buttons */
        .btn {
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.45rem 1rem;
            transition: all 0.15s ease;
        }
        .btn-sm {
            border-radius: 8px;
            font-size: 0.8rem;
            padding: 0.35rem 0.75rem;
        }
        .btn-primary {
            background-color: #2563eb;
            border-color: #2563eb;
            color: #ffffff;
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }
        .btn-outline-secondary {
            border-color: #e2e8f0;
            color: #475569;
            background-color: #ffffff;
        }
        .btn-outline-secondary:hover {
            background-color: #f8fafc;
            border-color: #cbd5e1;
            color: #1e293b;
        }

        /* Soft Badges */
        .badge-soft-success {
            background-color: #ecfdf5 !important;
            color: #047857 !important;
            border: 1px solid #a7f3d0 !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            font-size: 0.75rem !important;
            padding: 4px 10px !important;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap !important;
            line-height: 1.2 !important;
        }
        .badge-soft-danger {
            background-color: #fff1f2 !important;
            color: #be123c !important;
            border: 1px solid #fecdd3 !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            font-size: 0.75rem !important;
            padding: 4px 10px !important;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap !important;
            line-height: 1.2 !important;
        }
        .badge-soft-warning {
            background-color: #fffbeb !important;
            color: #b45309 !important;
            border: 1px solid #fde68a !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            font-size: 0.75rem !important;
            padding: 4px 10px !important;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap !important;
            line-height: 1.2 !important;
        }
        .badge-soft-primary {
            background-color: #eff6ff !important;
            color: #1d4ed8 !important;
            border: 1px solid #bfdbfe !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            font-size: 0.75rem !important;
            padding: 4px 10px !important;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap !important;
            line-height: 1.2 !important;
        }
        .badge-soft-neutral {
            background-color: #f8fafc !important;
            color: #475569 !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            font-size: 0.75rem !important;
            padding: 4px 10px !important;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap !important;
            line-height: 1.2 !important;
        }

        /* Square Action Icon Buttons matching reference: 🖨 👁 ✎ 🗑 */
        .btn-action-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #475569;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            text-decoration: none;
            transition: all 0.15s ease;
            padding: 0;
            cursor: pointer;
        }
        .btn-action-icon:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #cbd5e1;
        }
        .btn-action-icon.danger {
            background: #fff1f2;
            border-color: #fecdd3;
            color: #e11d48;
        }
        .btn-action-icon.danger:hover {
            background: #ffe4e6;
            border-color: #fda4af;
            color: #be123c;
        }
        .btn-action-icon.primary {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #2563eb;
        }
        .btn-action-icon.primary:hover {
            background: #dbeafe;
            border-color: #93c5fd;
            color: #1d4ed8;
        }

        /* Sleek Progress Bars for Analytics Cards */
        .progress-clean {
            height: 7px;
            border-radius: 9999px;
            background-color: #f1f5f9;
            overflow: hidden;
            position: relative;
            width: 100%;
        }
        .progress-clean-bar {
            height: 100%;
            border-radius: 9999px;
            transition: width 0.3s ease;
        }
        .progress-clean-bar.emerald {
            background-color: #10b981;
        }
        .progress-clean-bar.amber {
            background-color: #f59e0b;
        }
        .progress-clean-bar.blue {
            background-color: #3b82f6;
        }

        /* WhatsApp Button */
        .btn-wa {
            background-color: #25D366;
            color: #ffffff;
            border: 0;
            font-weight: 600;
            font-size: 0.8rem;
            border-radius: 8px;
        }
        .btn-wa:hover {
            background-color: #1eb956;
            color: #ffffff;
        }
    </style>
    @stack('styles')
</head>
<body>
    @php
        $isAdmin = auth()->user()->role === 'admin';
        $panelHome = $isAdmin ? route('admin.dashboard') : route('admin.cashbook.index');
        $panelRole = $isAdmin ? 'Super Admin' : 'Accounting';
    @endphp
    <div class="app-wrapper">

        <!-- ==============================================
             POLISHED MODERN NAVBAR (REF: MINIMAL CLEAN)
             ============================================== -->
        <nav class="app-header navbar navbar-expand">
            <div class="container-fluid px-0 d-flex align-items-center justify-content-between">
                
                <!-- Left Nav: Toggle & Page Title -->
                <div class="d-flex align-items-center gap-3">
                    <button class="nav-btn-icon" id="sidebarToggleBtn" type="button" title="Perluas / Ciutkan Navigasi">
                        <i class="fas fa-bars-staggered"></i>
                    </button>
                    <h1 class="header-page-title d-none d-sm-block">@yield('page-title', 'Dashboard')</h1>
                </div>

                <!-- Right Nav: Lihat Website, Notifikasi, Halo Administrator, Keluar -->
                <div class="d-flex align-items-center gap-3">
                    <a href="/" target="_blank" class="btn-nav-pill d-none d-md-inline-flex" title="Buka website publik">
                        <i class="fas fa-arrow-up-right-from-square" style="font-size: 0.72rem; opacity: 0.75;"></i>
                        <span>Lihat Website</span>
                    </a>

                    @if($isAdmin)
                    @php
                        $unreadQuotes = \App\Models\Penawaran::where('status', 'pending')->count();
                        $unreadSurat = \App\Models\SuratPerjanjian::where('status', 'pending')->count();
                    @endphp

                    <!-- Notification Dropdown -->
                    <div class="dropdown">
                        <button class="nav-btn-icon" data-bs-toggle="dropdown" aria-expanded="false" title="Pemberitahuan Sistem">
                            <i class="far fa-bell"></i>
                            @if($unreadQuotes + $unreadSurat > 0)
                                <span class="notif-badge-pill">
                                    {{ $unreadQuotes + $unreadSurat }}
                                </span>
                            @endif
                        </button>
                        <div class="dropdown-menu dropdown-menu-end custom-dropdown-menu mt-2" style="width: 270px;">
                            <div class="px-3 py-2 border-bottom">
                                <span class="text-uppercase small fw-bold text-secondary">Pemberitahuan Sistem</span>
                            </div>
                            <div class="p-1">
                                <a href="/admin/penawaran" class="custom-dropdown-item">
                                    <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.8rem;">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="fw-semibold text-truncate small">{{ $unreadQuotes }} Penawaran Masuk</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">Permintaan penawaran biaya</div>
                                    </div>
                                </a>
                                <a href="/admin/surat-perjanjian" class="custom-dropdown-item">
                                    <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.8rem;">
                                        <i class="fas fa-file-contract"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="fw-semibold text-truncate small">{{ $unreadSurat }} Berkas SPK Menunggu</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">Memerlukan verifikasi admin</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- User Greeting Text with Dropdown settings -->
                    <div class="dropdown d-none d-sm-block">
                        <a href="#" class="text-decoration-none text-dark header-greeting d-flex align-items-center gap-1" data-bs-toggle="dropdown" aria-expanded="false" role="button">
                            <span>Halo, <strong>{{ auth()->user()->name ?? 'Administrator' }}</strong></span>
                            <i class="fas fa-chevron-down text-muted" style="font-size: 0.65rem;"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end custom-dropdown-menu mt-2" style="width: 240px;">
                            <div class="p-3 border-bottom bg-light rounded-top">
                                <div class="fw-bold text-dark text-truncate small">{{ auth()->user()->name ?? 'Admin' }}</div>
                                <div class="text-muted text-truncate" style="font-size: 0.72rem;">{{ auth()->user()->email ?? '-' }}</div>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle mt-1 font-monospace" style="font-size: 0.65rem;">{{ $isAdmin ? 'ADMINISTRATOR' : 'ACCOUNTING' }}</span>
                            </div>
                            <div class="p-1">
                                @if($isAdmin)
                                <a href="/admin/settings" class="custom-dropdown-item">
                                    <i class="fas fa-sliders-h text-muted" style="width: 16px;"></i>
                                    <span>Pengaturan Website</span>
                                </a>
                                @endif
                                <a href="/" target="_blank" class="custom-dropdown-item">
                                    <i class="fas fa-globe text-muted" style="width: 16px;"></i>
                                    <span>Halaman Utama Website</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Keluar Pill Button -->
                    <form method="POST" action="/logout" class="d-inline m-0">
                        @csrf
                        <button type="submit" class="btn-nav-pill" title="Keluar dari akun">
                            <span>Keluar</span>
                        </button>
                    </form>

                </div>
            </div>
        </nav>

        <!-- ==============================================
             SIDEBAR
             ============================================== -->
        <aside class="app-sidebar" id="appSidebar">
            <!-- Brand Logo -->
            <div class="sidebar-brand">
                <a href="{{ $panelHome }}" aria-label="Indo Berkah Konstruksi - Beranda panel">
                    <img src="{{ asset('image/Logo/logo-admin.svg') }}" alt="Logo Indo Berkah Konstruksi">
                </a>
            </div>

            <!-- Mini User Profile -->
            <div class="sidebar-user">
                <div class="sidebar-user-avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name">{{ auth()->user()->name ?? 'Administrator' }}</div>
                    <small class="text-muted d-block" style="font-size: 0.72rem;">{{ $panelRole }}</small>
                </div>
            </div>

            <!-- Sidebar Navigation -->
            <div class="sidebar-wrapper">
                <ul class="nav nav-sidebar flex-column">
                    @if($isAdmin)
                    <li class="nav-item">
                        <a href="/admin" class="nav-link {{ request()->is('admin') ? 'active' : '' }}">
                            <div class="nav-left">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </div>
                        </a>
                    </li>

                    <!-- OPERASIONAL PROYEK -->
                    <li class="nav-header">Operasional Proyek</li>
                    
                    <li class="nav-item">
                        <a href="/admin/projects" class="nav-link {{ request()->is('admin/projects*') && !request()->routeIs('admin.cashbook.*') ? 'active' : '' }}">
                            <div class="nav-left">
                                <i class="fas fa-building"></i>
                                <span>Daftar Proyek</span>
                            </div>
                            @php $projCount = \App\Models\Project::count(); @endphp
                            @if($projCount > 0)
                                <span class="badge bg-secondary rounded-pill">{{ $projCount }}</span>
                            @endif
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/admin/user-files" class="nav-link {{ request()->is('admin/user-files*') ? 'active' : '' }}">
                            <div class="nav-left">
                                <i class="fas fa-folder-open"></i>
                                <span>File & Desain</span>
                            </div>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/admin/feedbacks" class="nav-link {{ request()->is('admin/feedbacks*') ? 'active' : '' }}">
                            <div class="nav-left">
                                <i class="fas fa-comment-dots"></i>
                                <span>Masukan Lapangan</span>
                            </div>
                            @php $pendingFb = \App\Models\UserFeedback::where('status', 'pending')->count(); @endphp
                            @if($pendingFb > 0)
                                <span class="badge bg-warning text-dark rounded-pill">{{ $pendingFb }}</span>
                            @endif
                        </a>
                    </li>
                    @endif

                    <!-- MANAJEMEN KEUANGAN -->
                    <li class="nav-header">Manajemen Keuangan</li>

                    <li class="nav-item">
                        <a href="{{ route('admin.cashbook.general') }}" class="nav-link {{ request()->routeIs('admin.cashbook.general') ? 'active' : '' }}">
                            <div class="nav-left">
                                <i class="fas fa-chart-pie" aria-hidden="true"></i>
                                <span>Keuangan Umum</span>
                            </div>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.cashbook.index') }}" class="nav-link {{ (request()->routeIs('admin.cashbook.index') || request()->routeIs('admin.cashbook.show')) ? 'active' : '' }}">
                            <div class="nav-left">
                                <i class="fas fa-wallet" aria-hidden="true"></i>
                                <span>Keuangan Proyek</span>
                            </div>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.cashbook.clients.index') }}" class="nav-link {{ request()->routeIs('admin.cashbook.clients.*') ? 'active' : '' }}">
                            <div class="nav-left">
                                <i class="fas fa-address-book" aria-hidden="true"></i>
                                <span>Kontak Klien</span>
                            </div>
                        </a>
                    </li>

                    @if($isAdmin)
                    <!-- PENJUALAN & KLIEN -->
                    <li class="nav-header">Penjualan & Klien</li>

                    <li class="nav-item">
                        <a href="/admin/penawaran" class="nav-link {{ request()->is('admin/penawaran*') ? 'active' : '' }}">
                            <div class="nav-left">
                                <i class="fas fa-file-invoice-dollar"></i>
                                <span>Penawaran (RFQ)</span>
                            </div>
                            @if($unreadQuotes > 0)
                                <span class="badge bg-info text-dark rounded-pill">{{ $unreadQuotes }}</span>
                            @endif
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/admin/surat-perjanjian" class="nav-link {{ request()->is('admin/surat-perjanjian*') ? 'active' : '' }}">
                            <div class="nav-left">
                                <i class="fas fa-file-contract"></i>
                                <span>Surat Perjanjian (SPK)</span>
                            </div>
                            @if($unreadSurat > 0)
                                <span class="badge bg-warning text-dark rounded-pill">{{ $unreadSurat }}</span>
                            @endif
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/admin/users" class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}">
                            <div class="nav-left">
                                <i class="fas fa-users"></i>
                                <span>Data Klien & User</span>
                            </div>
                        </a>
                    </li>

                    <!-- KONTEN PUBLIK -->
                    <li class="nav-header">Konten Website Publik</li>

                    <li class="nav-item">
                        <a href="/admin/portfolios" class="nav-link {{ request()->is('admin/portfolios*') ? 'active' : '' }}">
                            <div class="nav-left">
                                <i class="fas fa-images"></i>
                                <span>Portofolio Proyek</span>
                            </div>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/admin/berita" class="nav-link {{ request()->is('admin/berita*') ? 'active' : '' }}">
                            <div class="nav-left">
                                <i class="fas fa-newspaper"></i>
                                <span>Berita & Artikel</span>
                            </div>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/admin/reviews" class="nav-link {{ request()->is('admin/reviews*') ? 'active' : '' }}">
                            <div class="nav-left">
                                <i class="fas fa-star"></i>
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
                            <div class="nav-left">
                                <i class="fas fa-id-badge"></i>
                                <span>Tim & Rekrutmen</span>
                            </div>
                        </a>
                    </li>

                    <!-- SISTEM -->
                    <li class="nav-header">Sistem & Tools</li>

                    <li class="nav-item">
                        <a href="/admin/settings" class="nav-link {{ request()->is('admin/settings*') ? 'active' : '' }}">
                            <div class="nav-left">
                                <i class="fas fa-sliders-h"></i>
                                <span>Pengaturan Website</span>
                            </div>
                        </a>
                    </li>
                    @endif
                </ul>
            </div>

            <!-- Sidebar Footer -->
            <div class="sidebar-footer">
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Keluar (Logout)</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- ==============================================
             MAIN CONTENT WRAPPER
             ============================================== -->
        <main class="app-main">
            <!-- Content Header -->
            <div class="content-header py-3">
                <div class="container-fluid px-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h1 class="m-0 text-dark fw-bold d-sm-none" style="font-size: 1.25rem;">@yield('page-title', 'Dashboard')</h1>
                            @hasSection('page-subtitle')
                                <p class="text-muted small mb-0 mt-1">@yield('page-subtitle')</p>
                            @endif
                        </div>
                        <div class="d-flex align-items-center gap-2 ms-auto">
                            @yield('page-actions')
                            @hasSection('breadcrumb')
                                <ol class="breadcrumb mb-0 align-items-center bg-white px-3 py-1 rounded-pill border" style="font-size: 0.8rem; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                                    <li class="breadcrumb-item">
                                        <a href="{{ $panelHome }}" class="text-decoration-none text-muted">
                                            <i class="fas fa-house me-1" style="font-size: 0.75rem;"></i> Home
                                        </a>
                                    </li>
                                    @yield('breadcrumb')
                                </ol>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Body -->
            <div class="content">
                <div class="container-fluid px-3">
                    <!-- Flash Message -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show py-2 px-3 small border-0 shadow-sm mb-3" role="alert">
                            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                            <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show py-2 px-3 small border-0 shadow-sm mb-3" role="alert">
                            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                            <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="alert alert-info py-2 px-3 small mb-3" role="status">{{ session('info') }}</div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </main>

        <!-- ==============================================
             FOOTER
             ============================================== -->
        <footer class="app-footer">
            <div>
                Copyright &copy; 2026 <strong>PT Indo Berkah Konstruksi</strong>. All rights reserved.
            </div>
            <div class="d-none d-sm-inline text-muted font-monospace" style="font-size: 0.8rem;">
                AdminLTE v4.0 • Enterprise
            </div>
        </footer>

    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar Toggle Script (Responsive for Desktop Collapse & Mobile Drawer) -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const toggleBtn = document.getElementById("sidebarToggleBtn");
            if (toggleBtn) {
                toggleBtn.addEventListener("click", function (e) {
                    e.preventDefault();
                    if (window.innerWidth >= 992) {
                        document.body.classList.toggle("sidebar-collapse");
                    } else {
                        document.body.classList.toggle("sidebar-open");
                    }
                });
            }

            // Close on click outside on mobile
            document.addEventListener("click", function (e) {
                if (window.innerWidth < 992 && document.body.classList.contains("sidebar-open")) {
                    const sidebar = document.getElementById("appSidebar");
                    const toggleBtn = document.getElementById("sidebarToggleBtn");
                    if (sidebar && !sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                        document.body.classList.remove("sidebar-open");
                    }
                }
            });

            // Auto-dismiss any alert notification after 5 seconds
            const alerts = document.querySelectorAll(".alert");
            alerts.forEach(function (alert) {
                setTimeout(function () {
                    if (alert && alert.parentNode) {
                        alert.classList.remove("show");
                        setTimeout(function () {
                            if (alert.parentNode) alert.remove();
                        }, 500);
                    }
                }, 5000);
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
