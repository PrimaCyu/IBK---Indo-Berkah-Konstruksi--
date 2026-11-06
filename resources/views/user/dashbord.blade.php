<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Indo Berkah Konstruksi</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 0px; }
        ::-webkit-scrollbar-thumb:hover { background: #C5A880; }
        
        #sidebar { transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        #sidebar-overlay { display: none; transition: opacity 0.4s ease; }
        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); }
            #sidebar-overlay.open { display: block; opacity: 1; }
        }

        .project-card { transition: all 0.3s ease; }
        .project-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px -15px rgba(0,0,0,0.1); }
        .progress-bar-mini { height: 4px; border-radius: 0px; background: #F3F4F6; overflow: hidden; }
        .progress-fill-mini { height: 100%; background: #C5A880; border-radius: 0px; transition: width 1s cubic-bezier(0.4, 0, 0.2, 1); }
        
        #create-project-modal { display: none; }
        #create-project-modal.open { display: flex; }
    </style>
</head>
<body class="bg-[#FAFAFA] text-gray-900 font-sans antialiased min-h-screen">

<!-- OVERLAY MOBILE -->
<div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/40 z-40 backdrop-blur-sm opacity-0"></div>

<!-- SIDEBAR -->
<aside id="sidebar" class="w-[280px] bg-white border-r border-gray-100 flex fixed flex-col h-full z-50">
    <!-- Logo -->
    <div class="p-8 border-b border-gray-100 flex items-center gap-4">
        <a href="/" class="w-10 h-10 bg-[#111111] flex items-center justify-center hover:bg-[#C5A880] transition-colors">
            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
        </a>
        <div>
            <div class="text-gray-900 font-light text-lg tracking-widest uppercase">IBK</div>
            <div class="text-[#C5A880] text-[9px] tracking-[0.3em] uppercase font-bold mt-1">Konstruksi</div>
        </div>
    </div>

    <!-- User Info -->
    <div class="p-8 pb-4 border-b border-gray-50">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gray-50 flex items-center justify-center text-gray-900 font-light text-xl border border-gray-200">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-gray-900 font-medium text-sm truncate">{{ auth()->user()->name }}</div>
                <div class="text-gray-500 text-xs truncate font-light mt-0.5">{{ auth()->user()->email }}</div>
            </div>
        </div>
    </div>

    <!-- Nav -->
    <nav class="flex-1 px-6 py-6 space-y-1 overflow-y-auto">
        <div class="text-gray-400 text-[10px] uppercase tracking-[0.2em] px-4 py-3 font-bold">Menu</div>

        <a href="/dashbord" class="flex items-center gap-4 px-4 py-3 text-sm font-medium transition-colors bg-[#FAFAFA] text-[#C5A880] border-l-2 border-[#C5A880]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>
    </nav>

    <!-- Logout -->
    <div class="p-6 border-t border-gray-100">
        <form method="POST" action="/logout">
            @csrf
            <button class="w-full flex items-center justify-center gap-3 bg-[#111111] hover:bg-[#C5A880] text-white px-4 py-4 text-[10px] tracking-widest uppercase font-bold transition-colors">
                Keluar
            </button>
        </form>
    </div>
</aside>

<!-- MAIN CONTENT -->
<div class="md:ml-[280px] flex flex-col min-h-screen relative">
    
    <!-- TOPBAR -->
    <header class="sticky top-0 z-30 bg-[#FAFAFA]/90 backdrop-blur-xl border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-900">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>

        <div class="hidden md:block">
            <h1 class="text-gray-900 font-light text-xl tracking-wide">Client Portal</h1>
        </div>

        <div class="flex items-center gap-4">
            <a href="/" class="w-10 h-10 bg-white border border-gray-200 flex items-center justify-center text-gray-900 hover:bg-[#111111] hover:text-white transition-colors" title="Kembali ke Web">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </a>
        </div>
    </header>

    <main class="flex-1 p-6 md:p-10 space-y-10 max-w-7xl w-full mx-auto">
        
        <!-- WELCOME CARD -->
        <div class="bg-white p-10 md:p-14 border border-gray-100 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05)] relative overflow-hidden flex flex-col md:flex-row justify-between items-center gap-10">
            <div class="relative z-10 w-full md:w-2/3">
                <p class="text-[#C5A880] text-[10px] font-bold uppercase tracking-[0.3em] mb-4">Selamat Datang</p>
                <h2 class="text-4xl md:text-5xl font-light mb-4 text-gray-900 leading-tight">Halo, <br/><span class="font-bold">{{ auth()->user()->name }}</span></h2>
                <p class="text-gray-500 font-light text-sm max-w-md leading-relaxed">Kelola semua proyek konstruksi Anda di sini. Tambahkan proyek baru, pantau progres, dan kirimkan masukan langsung ke tim kami.</p>
            </div>
            
            <div class="w-32 h-32 md:w-48 md:h-48 bg-[#FAFAFA] flex items-center justify-center relative z-10 border border-gray-100">
                <div class="text-[#111111] font-light text-5xl md:text-7xl">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </div>
            
            <div class="absolute -top-32 -right-32 w-[500px] h-[500px] bg-[#FAFAFA] rounded-full blur-[80px] pointer-events-none"></div>
        </div>

        <!-- STATS GRID -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @php
                $totalProjects = $projects->count();
                $activeProjects = $projects->where('status', 'aktif')->count();
                $completedProjects = $projects->where('status', 'selesai')->count();
            @endphp

            <div class="bg-white p-8 border border-gray-100 shadow-[0_10px_30px_-15px_rgba(0,0,0,0.05)]">
                <div class="text-[#C5A880] mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <div class="text-3xl font-light text-gray-900 mb-2">{{ $totalProjects }}</div>
                <div class="text-gray-400 text-[10px] font-bold uppercase tracking-[0.2em]">Total<br>Proyek</div>
            </div>

            <div class="bg-white p-8 border border-gray-100 shadow-[0_10px_30px_-15px_rgba(0,0,0,0.05)]">
                <div class="text-green-500 mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div class="text-3xl font-light text-gray-900 mb-2">{{ $activeProjects }}</div>
                <div class="text-gray-400 text-[10px] font-bold uppercase tracking-[0.2em]">Proyek<br>Aktif</div>
            </div>

            <div class="bg-white p-8 border border-gray-100 shadow-[0_10px_30px_-15px_rgba(0,0,0,0.05)]">
                <div class="text-blue-500 mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="text-3xl font-light text-gray-900 mb-2">{{ $completedProjects }}</div>
                <div class="text-gray-400 text-[10px] font-bold uppercase tracking-[0.2em]">Proyek<br>Selesai</div>
            </div>

            <div class="bg-[#111111] p-8 border border-[#111111] shadow-[0_10px_30px_-15px_rgba(0,0,0,0.2)]">
                <div class="text-[#C5A880] mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div class="text-xl font-light text-white mb-2 mt-4">{{ auth()->user()->created_at->format('M Y') }}</div>
                <div class="text-gray-500 text-[10px] font-bold uppercase tracking-[0.2em]">Member<br>Sejak</div>
            </div>
        </div>

        <!-- PROJECT LIST SECTION -->
        <div>
            <div class="flex items-center justify-between mb-8">
                <div>
                    <span class="block text-[10px] tracking-[0.3em] uppercase text-[#C5A880] mb-1 font-medium">Daftar Proyek</span>
                    <h3 class="font-light text-gray-900 text-2xl">Proyek Konstruksi Anda</h3>
                </div>
                <button onclick="openCreateModal()" class="inline-flex items-center gap-3 bg-[#111111] hover:bg-[#C5A880] text-white px-6 py-4 text-[10px] tracking-widest uppercase font-bold transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Proyek
                </button>
            </div>

            @if(session('project_success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-100 flex items-center gap-4">
                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    <p class="text-green-800 text-sm font-medium tracking-wide">{{ session('project_success') }}</p>
                </div>
            @endif

            @if($projects->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($projects as $project)
                        <a href="{{ route('projects.show', $project) }}" class="project-card bg-white border border-gray-100 overflow-hidden block group">
                            <!-- Project Image / Placeholder -->
                            <div class="h-48 bg-gradient-to-br from-[#111111] to-[#333333] relative overflow-hidden">
                                @if($project->main_image)
                                    <img src="{{ asset('storage/' . $project->main_image) }}" alt="{{ $project->nama_proyek }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-16 h-16 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                @endif
                                <!-- Status Badge -->
                                <div class="absolute top-4 right-4">
                                    <span class="text-[9px] font-bold uppercase tracking-widest px-3 py-1.5
                                        @if($project->status === 'aktif') bg-green-500 text-white
                                        @elseif($project->status === 'selesai') bg-blue-500 text-white
                                        @else bg-red-500 text-white @endif">
                                        {{ ucfirst($project->status) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <h4 class="text-lg font-bold text-gray-900 mb-1 group-hover:text-[#C5A880] transition-colors">{{ $project->nama_proyek }}</h4>
                                @if($project->alamat_proyek)
                                    <div class="flex items-center gap-2 text-gray-500 text-xs font-light mb-4">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span class="truncate">{{ $project->alamat_proyek }}</span>
                                    </div>
                                @endif
                                @if($project->kategori)
                                    <span class="inline-block bg-[#FAFAFA] border border-gray-100 text-gray-600 text-[10px] font-bold uppercase tracking-widest px-3 py-1.5 mb-4">{{ $project->kategori }}</span>
                                @endif

                                <!-- Progress -->
                                <div class="mt-2">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-[10px] font-bold tracking-[0.2em] uppercase text-gray-400">Progres</span>
                                        <span class="text-sm font-bold text-[#C5A880]">{{ $project->progress_percentage }}%</span>
                                    </div>
                                    <div class="progress-bar-mini">
                                        <div class="progress-fill-mini" style="width: {{ $project->progress_percentage }}%"></div>
                                    </div>
                                </div>

                                <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between">
                                    <span class="text-[9px] text-gray-400 font-bold tracking-widest uppercase">{{ $project->created_at->format('d M Y') }}</span>
                                    <span class="inline-flex items-center gap-2 text-[10px] font-bold tracking-widest uppercase text-[#C5A880] group-hover:translate-x-1 transition-transform">
                                        Detail
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="bg-white border-2 border-dashed border-gray-200 p-16 text-center">
                    <div class="w-20 h-20 bg-[#FAFAFA] flex items-center justify-center mx-auto mb-6 border border-gray-100">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h4 class="text-gray-900 font-bold text-lg mb-2">Belum Ada Proyek</h4>
                    <p class="text-gray-500 text-sm font-light max-w-md mx-auto mb-8">Anda belum memiliki proyek konstruksi. Tambahkan proyek pertama Anda untuk mulai mengelola pembangunan.</p>
                    <button onclick="openCreateModal()" class="inline-flex items-center gap-3 bg-[#111111] hover:bg-[#C5A880] text-white px-8 py-4 text-[10px] tracking-widest uppercase font-bold transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Proyek Pertama
                    </button>
                </div>
            @endif
        </div>

        <div class="pb-20"></div>
    </main>
</div>

<!-- CREATE PROJECT MODAL -->
<div id="create-project-modal" onclick="if(event.target===this)closeCreateModal()" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[100] items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg border border-gray-200 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between">
            <div>
                <span class="block text-[10px] tracking-[0.3em] uppercase text-[#C5A880] mb-1 font-medium">Proyek Baru</span>
                <h3 class="font-light text-gray-900 text-xl">Tambah Proyek Konstruksi</h3>
            </div>
            <button onclick="closeCreateModal()" class="w-10 h-10 bg-[#FAFAFA] flex items-center justify-center text-gray-500 hover:bg-[#111111] hover:text-white transition-colors border border-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        
        <form action="{{ route('projects.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            <div>
                <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-gray-500 mb-3">Nama Proyek *</label>
                <input type="text" name="nama_proyek" placeholder="Contoh: Pembangunan Rumah Tinggal Jl. Merdeka" required
                    class="w-full bg-[#FAFAFA] border-b-2 border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#C5A880] transition-colors">
            </div>

            <div>
                <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-gray-500 mb-3">Alamat Proyek</label>
                <input type="text" name="alamat_proyek" placeholder="Contoh: Jl. Merdeka No. 10, Jakarta Selatan"
                    class="w-full bg-[#FAFAFA] border-b-2 border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#C5A880] transition-colors">
            </div>

            <div>
                <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-gray-500 mb-3">Kategori</label>
                <select name="kategori" class="w-full bg-[#FAFAFA] border-b-2 border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#C5A880] transition-colors">
                    <option value="">— Pilih Kategori —</option>
                    <option value="Pembangunan Rumah">Pembangunan Rumah</option>
                    <option value="Gedung Komersial">Gedung Komersial</option>
                    <option value="Renovasi Bangunan">Renovasi Bangunan</option>
                    <option value="Konsultasi Konstruksi">Konsultasi Konstruksi</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-gray-500 mb-3">Deskripsi Singkat</label>
                <textarea name="deskripsi" rows="3" placeholder="Jelaskan secara singkat proyek ini..."
                    class="w-full bg-[#FAFAFA] border-b-2 border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#C5A880] transition-colors resize-none"></textarea>
            </div>

            <button type="submit"
                class="w-full inline-flex items-center justify-center gap-3 text-[11px] font-bold tracking-[0.2em] uppercase bg-[#111111] text-white px-8 py-4 hover:bg-[#C5A880] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Proyek Baru
            </button>
        </form>
    </div>
</div>

<!-- FLOAT BUTTON -->
<a href="/penawaran" class="fixed bottom-8 right-8 z-40 bg-[#111111] text-white px-6 py-4 flex items-center gap-3 shadow-2xl hover:bg-[#C5A880] hover:-translate-y-1 transition-all group">
    <svg class="w-5 h-5 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    <span class="tracking-widest font-bold uppercase text-[10px]">Penawaran Baru</span>
</a>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('sidebar-overlay').classList.toggle('open');
    }
    function closeSidebarMobile() {
        if (window.innerWidth < 768) {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebar-overlay').classList.remove('open');
        }
    }
    function openCreateModal() {
        document.getElementById('create-project-modal').classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeCreateModal() {
        document.getElementById('create-project-modal').classList.remove('open');
        document.body.style.overflow = '';
    }
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeCreateModal();
    });
</script>

</body>
</html>