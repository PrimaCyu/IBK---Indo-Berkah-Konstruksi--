<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $project->nama_proyek }} | Indo Berkah Konstruksi</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
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
        
        .progress-bar { height: 6px; border-radius: 0px; background: #F3F4F6; overflow: hidden; }
        .progress-fill { height: 100%; background: #C5A880; border-radius: 0px; transition: width 1s cubic-bezier(0.4, 0, 0.2, 1); }
        
        #lightbox { display: none; }
        #lightbox.open { display: flex; }
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

        <a href="/dashbord" class="flex items-center gap-4 px-4 py-3 text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors border-l-2 border-transparent">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>

        <div class="text-gray-400 text-[10px] uppercase tracking-[0.2em] px-4 py-3 font-bold mt-4">Proyek Ini</div>

        <a href="#upload-section" class="flex items-center gap-4 px-4 py-3 text-sm font-medium transition-colors bg-[#FAFAFA] text-[#C5A880] border-l-2 border-[#C5A880]" onclick="closeSidebarMobile()">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
            Upload Desain
        </a>

        <a href="#surat-section" class="flex items-center gap-4 px-4 py-3 text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors border-l-2 border-transparent" onclick="closeSidebarMobile()">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Surat Perjanjian
        </a>

        <a href="#progress-section" class="flex items-center gap-4 px-4 py-3 text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors border-l-2 border-transparent" onclick="closeSidebarMobile()">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Progres
        </a>

        <a href="#review-section" class="flex items-center gap-4 px-4 py-3 text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors border-l-2 border-transparent" onclick="closeSidebarMobile()">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            Review
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

        <div class="hidden md:flex items-center gap-3">
            <a href="/dashbord" class="text-gray-400 hover:text-gray-900 transition-colors text-sm font-light">Dashboard</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-900 text-sm font-medium truncate max-w-[300px]">{{ $project->nama_proyek }}</span>
        </div>

        <div class="flex items-center gap-4">
            <div class="hidden sm:flex items-center gap-2 bg-white px-4 py-2 text-[10px] uppercase tracking-widest font-bold border border-gray-200
                @if($project->status === 'aktif') text-green-600
                @elseif($project->status === 'selesai') text-blue-600
                @else text-red-600 @endif">
                <span class="w-2 h-2 animate-pulse
                    @if($project->status === 'aktif') bg-green-500
                    @elseif($project->status === 'selesai') bg-blue-500
                    @else bg-red-500 @endif"></span>
                {{ ucfirst($project->status) }}
            </div>
            <a href="/dashbord" class="w-10 h-10 bg-white border border-gray-200 flex items-center justify-center text-gray-900 hover:bg-[#111111] hover:text-white transition-colors" title="Kembali ke Dashboard">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
        </div>
    </header>

    <main class="flex-1 p-6 md:p-10 space-y-10 max-w-7xl w-full mx-auto">
        
        <!-- PROJECT INFO CARD -->
        <div class="bg-white p-10 md:p-14 border border-gray-100 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05)] relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-[#C5A880] text-[10px] font-bold uppercase tracking-[0.3em] mb-4">Detail Proyek</p>
                <h2 class="text-3xl md:text-4xl font-light mb-4 text-gray-900 leading-tight"><span class="font-bold">{{ $project->nama_proyek }}</span></h2>
                
                <div class="flex flex-wrap gap-6 mt-4">
                    @if($project->alamat_proyek)
                        <div class="flex items-center gap-2 text-gray-500 text-sm font-light">
                            <svg class="w-4 h-4 text-[#C5A880]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $project->alamat_proyek }}
                        </div>
                    @endif
                    @if($project->kategori)
                        <div class="flex items-center gap-2 text-gray-500 text-sm font-light">
                            <svg class="w-4 h-4 text-[#C5A880]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            {{ $project->kategori }}
                        </div>
                    @endif
                </div>
                
                @if($project->deskripsi)
                    <p class="text-gray-500 font-light text-sm max-w-2xl leading-relaxed mt-4">{{ $project->deskripsi }}</p>
                @endif
            </div>
            <div class="absolute -top-32 -right-32 w-[500px] h-[500px] bg-[#FAFAFA] rounded-full blur-[80px] pointer-events-none"></div>
        </div>

        <!-- STATS GRID -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="bg-white p-8 border border-gray-100 shadow-[0_10px_30px_-15px_rgba(0,0,0,0.05)]">
                <div class="text-[#C5A880] mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div class="text-3xl font-light text-gray-900 mb-2">{{ $designFiles->count() }}</div>
                <div class="text-gray-400 text-[10px] font-bold uppercase tracking-[0.2em]">Desain<br>Diunggah</div>
            </div>

            <div class="bg-white p-8 border border-gray-100 shadow-[0_10px_30px_-15px_rgba(0,0,0,0.05)]">
                <div class="text-[#111111] mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <div class="text-3xl font-light text-gray-900 mb-2">{{ $progressFiles->count() }}</div>
                <div class="text-gray-400 text-[10px] font-bold uppercase tracking-[0.2em]">Foto<br>Progres</div>
            </div>

            <div class="bg-white p-8 border border-gray-100 shadow-[0_10px_30px_-15px_rgba(0,0,0,0.05)]">
                <div class="text-[#C5A880] mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="text-3xl font-light text-gray-900 mb-2">{{ $reviews->count() }}</div>
                <div class="text-gray-400 text-[10px] font-bold uppercase tracking-[0.2em]">Review<br>Dikirim</div>
            </div>

            <div class="bg-[#111111] p-8 border border-[#111111] shadow-[0_10px_30px_-15px_rgba(0,0,0,0.2)]">
                <div class="text-[#C5A880] mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div class="text-3xl font-light text-white mb-2">{{ $project->progress_percentage }}%</div>
                <div class="text-gray-500 text-[10px] font-bold uppercase tracking-[0.2em]">Total<br>Progres</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <!-- COL 1 -->
            <div class="space-y-10">
                <!-- UPLOAD DESAIN -->
                <div id="upload-section" class="bg-white border border-gray-100 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05)]">
                    <div class="px-8 py-6 border-b border-gray-100">
                        <span class="block text-[10px] tracking-[0.3em] uppercase text-[#C5A880] mb-1 font-medium">Unggah Dokumen</span>
                        <h3 class="font-light text-gray-900 text-2xl">Desain & File</h3>
                    </div>

                    <div class="p-8">
                        <form action="{{ route('user-files.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <input type="hidden" name="project_id" value="{{ $project->id }}">
                            <label class="block p-10 text-center relative border-2 border-dashed border-gray-200 bg-[#FAFAFA] hover:border-[#C5A880] hover:bg-white transition-colors cursor-pointer" for="file-input">
                                <input type="file" id="file-input" name="file" accept="image/*,.pdf" class="hidden" onchange="updateFileName(this)">
                                <div id="upload-icon">
                                    <div class="w-12 h-12 bg-white flex items-center justify-center mx-auto mb-4 border border-gray-100 shadow-sm">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                    </div>
                                    <p class="text-gray-900 font-medium text-sm">Klik atau seret file ke sini</p>
                                    <p class="text-gray-400 text-xs mt-2 font-light">Max. 10MB (JPG, PNG, PDF)</p>
                                </div>
                                <div id="file-name" class="hidden text-[#C5A880] font-bold text-sm absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 whitespace-nowrap bg-white px-4 py-2 border border-gray-100 shadow-sm"></div>
                            </label>

                            <div>
                                <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-gray-500 mb-3">Judul Desain *</label>
                                <input type="text" name="title" placeholder="Contoh: Denah Lantai 1..." required
                                    class="w-full bg-[#FAFAFA] border-b-2 border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#C5A880] transition-colors">
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-gray-500 mb-3">Keterangan</label>
                                <textarea name="description" rows="2" placeholder="Catatan khusus..."
                                    class="w-full bg-[#FAFAFA] border-b-2 border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#C5A880] transition-colors resize-none"></textarea>
                            </div>

                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-3 text-[11px] font-bold tracking-[0.2em] uppercase bg-[#111111] text-white px-8 py-4 hover:bg-[#C5A880] transition-colors">
                                Unggah Dokumen
                            </button>
                        </form>

                        @if(session('upload_success'))
                            <div class="mt-6 p-4 bg-green-50 border border-green-100 flex items-center gap-4">
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <p class="text-green-800 text-sm font-medium tracking-wide">{{ session('upload_success') }}</p>
                            </div>
                        @endif

                        @if($designFiles->count() > 0)
                            <div class="mt-10 pt-8 border-t border-gray-100">
                                <h4 class="text-[10px] font-bold tracking-[0.2em] uppercase text-gray-400 mb-4">Riwayat Unggahan</h4>
                                <div class="space-y-3">
                                    @foreach($designFiles as $file)
                                        <div class="flex items-center gap-4 p-4 bg-[#FAFAFA] border border-gray-100 hover:border-[#C5A880] transition-colors group">
                                            <div class="w-10 h-10 bg-white flex items-center justify-center flex-shrink-0 border border-gray-100">
                                                @if(in_array(pathinfo($file->file_path, PATHINFO_EXTENSION), ['jpg','jpeg','png','webp']))
                                                    <svg class="w-4 h-4 text-[#C5A880]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"/></svg>
                                                @else
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">{{ $file->title }}</p>
                                                <p class="text-[10px] text-gray-400 uppercase tracking-[0.1em] mt-1">{{ $file->created_at->format('d M Y') }}</p>
                                            </div>
                                            <a href="{{ Storage::url($file->file_path) }}" target="_blank"
                                               class="w-8 h-8 bg-white flex items-center justify-center text-gray-900 hover:bg-[#111111] hover:text-white transition-colors border border-gray-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- UPLOAD SURAT PERJANJIAN -->
                <div id="surat-section" class="bg-white border border-gray-100 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05)]">
                    <div class="px-8 py-6 border-b border-gray-100">
                        <span class="block text-[10px] tracking-[0.3em] uppercase text-[#C5A880] mb-1 font-medium">Unggah Dokumen Resmi</span>
                        <h3 class="font-light text-gray-900 text-2xl">Surat Perjanjian</h3>
                    </div>

                    <div class="p-8">
                        <form action="{{ route('surat-perjanjian.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <input type="hidden" name="nama" value="{{ auth()->user()->name }}">
                            <input type="hidden" name="email" value="{{ auth()->user()->email }}">

                            <label class="block p-10 text-center relative border-2 border-dashed border-gray-200 bg-[#FAFAFA] hover:border-[#C5A880] hover:bg-white transition-colors cursor-pointer" for="surat-file-input">
                                <input type="file" id="surat-file-input" name="file_surat" accept=".pdf,.doc,.docx" class="hidden" required onchange="updateSuratFileName(this)">
                                <div id="surat-upload-icon">
                                    <div class="w-12 h-12 bg-white flex items-center justify-center mx-auto mb-4 border border-gray-100 shadow-sm">
                                        <svg class="w-5 h-5 text-[#C5A880]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <p class="text-gray-900 font-medium text-sm">Klik atau seret file Surat Perjanjian ke sini</p>
                                    <p class="text-gray-400 text-xs mt-2 font-light">Maksimal 2MB (Format: PDF, DOC, DOCX)</p>
                                </div>
                                <div id="surat-file-name" class="hidden text-[#C5A880] font-bold text-sm absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 whitespace-nowrap bg-white px-4 py-2 border border-gray-100 shadow-sm"></div>
                            </label>

                            <div>
                                <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-gray-500 mb-3">No. Telepon / WhatsApp</label>
                                <input type="tel" name="telepon" placeholder="Contoh: 081234567890" value="{{ auth()->user()->phone ?? '' }}"
                                    class="w-full bg-[#FAFAFA] border-b-2 border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#C5A880] transition-colors">
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-gray-500 mb-3">Keterangan / Catatan Tambahan</label>
                                <textarea name="keterangan" rows="2" placeholder="Catatan khusus terkait surat perjanjian (opsional)..."
                                    class="w-full bg-[#FAFAFA] border-b-2 border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#C5A880] transition-colors resize-none"></textarea>
                            </div>

                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-3 text-[11px] font-bold tracking-[0.2em] uppercase bg-[#111111] text-white px-8 py-4 hover:bg-[#C5A880] transition-colors">
                                Kirim Surat Perjanjian
                            </button>
                        </form>

                        @if(session('success'))
                            <div class="mt-6 p-4 bg-green-50 border border-green-100 flex items-center gap-4">
                                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <p class="text-green-800 text-sm font-medium tracking-wide">{{ session('success') }}</p>
                            </div>
                        @endif

                        @if(isset($suratPerjanjians) && $suratPerjanjians->count() > 0)
                            <div class="mt-10 pt-8 border-t border-gray-100">
                                <h4 class="text-[10px] font-bold tracking-[0.2em] uppercase text-gray-400 mb-4">Riwayat Surat Perjanjian</h4>
                                <div class="space-y-3">
                                    @foreach($suratPerjanjians as $surat)
                                        <div class="flex items-center gap-4 p-4 bg-[#FAFAFA] border border-gray-100 hover:border-[#C5A880] transition-colors group">
                                            <div class="w-10 h-10 bg-white flex items-center justify-center flex-shrink-0 border border-gray-100">
                                                <svg class="w-4 h-4 text-[#C5A880]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">{{ basename($surat->file_surat) }}</p>
                                                <p class="text-[10px] text-gray-400 uppercase tracking-[0.1em] mt-1">{{ $surat->created_at->format('d M Y, H:i') }}</p>
                                            </div>
                                            <a href="{{ Storage::url($surat->file_surat) }}" target="_blank"
                                               class="w-8 h-8 bg-white flex items-center justify-center text-gray-900 hover:bg-[#111111] hover:text-white transition-colors border border-gray-200" title="Lihat File">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- COL 2 -->
            <div class="space-y-10 flex flex-col h-full">
                <!-- PROGRES PEMBANGUNAN -->
                <div id="progress-section" class="bg-white border border-gray-100 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05)] flex-1">
                    <div class="px-8 py-6 border-b border-gray-100">
                        <span class="block text-[10px] tracking-[0.3em] uppercase text-[#C5A880] mb-1 font-medium">Laporan Lapangan</span>
                        <h3 class="font-light text-gray-900 text-2xl">Progres Proyek</h3>
                    </div>

                    <div class="p-8">
                        @php $pct = $project->progress_percentage ?? 0; @endphp

                        <div class="mb-10">
                            <div class="flex justify-between items-end mb-4">
                                <span class="text-[10px] font-bold tracking-[0.2em] uppercase text-gray-500">Total Penyelesaian</span>
                                <span class="text-3xl font-light text-[#C5A880] leading-none">{{ $pct }}%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>

                        <!-- Foto Progres -->
                        <div>
                            <h4 class="text-[10px] font-bold tracking-[0.2em] uppercase text-gray-400 mb-4">Galeri Lapangan</h4>
                            @if($progressFiles->count() > 0)
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    @foreach($progressFiles as $photo)
                                        <div class="bg-[#FAFAFA] border border-gray-100 p-3">
                                            <div class="relative group cursor-pointer mb-3 border border-gray-200" onclick="openLightbox('{{ Storage::url($photo->file_path) }}')">
                                                <img src="{{ Storage::url($photo->file_path) }}" alt="{{ $photo->title }}" class="w-full h-40 object-cover hover:opacity-90 transition-opacity">
                                                <div class="absolute inset-0 bg-[#111111]/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm">
                                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                                </div>
                                            </div>
                                            <h5 class="text-sm font-bold text-gray-900 mb-1 truncate" title="{{ $photo->title }}">{{ $photo->title }}</h5>
                                            <p class="text-xs text-gray-500 font-light leading-relaxed line-clamp-2" title="{{ $photo->description }}">{{ $photo->description ?? 'Tidak ada keterangan tambahan.' }}</p>
                                            <p class="text-[9px] text-gray-400 mt-2 font-bold tracking-widest uppercase">{{ $photo->created_at->format('d M Y') }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-10 bg-[#FAFAFA] border border-gray-200 border-dashed">
                                    <svg class="w-8 h-8 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
                                    <p class="text-gray-900 text-sm font-bold">Belum ada foto</p>
                                    <p class="text-gray-500 text-xs mt-1 font-light max-w-[200px] mx-auto">Dokumentasi akan segera diunggah.</p>
                                </div>
                            @endif
                        </div>

                        <!-- MASUKAN & CATATAN LAPANGAN (SUMMERNOTE) -->
                        <div class="mt-10 pt-8 border-t border-gray-100">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Catatan & Masukan Lapangan</h4>
                                    <p class="text-xs text-gray-500 font-light mt-0.5">Kirimkan masukan, catatan, atau tanggapan proyek langsung ke admin.</p>
                                </div>
                                <span class="bg-[#C5A880]/10 text-[#C5A880] text-[10px] font-bold tracking-widest uppercase px-3 py-1 border border-[#C5A880]/30">Rich Text</span>
                            </div>

                            @if(session('feedback_success'))
                                <div class="mb-6 p-4 bg-green-50 border border-green-100 flex items-center gap-4">
                                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    <p class="text-green-800 text-sm font-medium tracking-wide">{{ session('feedback_success') }}</p>
                                </div>
                            @endif

                            <form action="{{ route('user-feedback.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="project_id" value="{{ $project->id }}">
                                <div>
                                    <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-gray-500 mb-2">Judul / Subjek Catatan (Opsional)</label>
                                    <input type="text" name="title" placeholder="Contoh: Penyesuaian Cat Dinding Lantai 2"
                                        class="w-full bg-[#FAFAFA] border-b-2 border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-[#C5A880] transition-colors">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-gray-500 mb-2">Masukan & Catatan Lapangan *</label>
                                    <textarea name="content" id="masukan-summernote" required></textarea>
                                </div>

                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-3 text-[11px] font-bold tracking-[0.2em] uppercase bg-[#111111] text-white px-8 py-3.5 hover:bg-[#C5A880] transition-colors">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                    Kirim Masukan ke Admin
                                </button>
                            </form>

                            <!-- Riwayat Masukan User -->
                            @if($feedbacks->count() > 0)
                                <div class="mt-8 pt-6 border-t border-gray-100">
                                    <h5 class="text-[10px] font-bold tracking-[0.2em] uppercase text-gray-400 mb-4">Riwayat Catatan & Masukan Anda</h5>
                                    <div class="space-y-4">
                                        @foreach($feedbacks as $fb)
                                            <div class="bg-[#FAFAFA] border border-gray-100 p-4 space-y-2">
                                                <div class="flex items-center justify-between">
                                                    <h6 class="text-sm font-bold text-gray-900">{{ $fb->title }}</h6>
                                                    <span class="text-[9px] font-bold uppercase tracking-widest px-2.5 py-1 border 
                                                        @if($fb->status === 'selesai') bg-green-50 text-green-700 border-green-200
                                                        @elseif($fb->status === 'dibaca' || $fb->status === 'diproses') bg-blue-50 text-blue-700 border-blue-200
                                                        @else bg-amber-50 text-amber-700 border-amber-200 @endif">
                                                        {{ strtoupper($fb->status) }}
                                                    </span>
                                                </div>
                                                <div class="text-xs text-gray-700 font-light leading-relaxed prose max-w-none">
                                                    {!! $fb->content !!}
                                                </div>
                                                @if($fb->admin_reply)
                                                    <div class="mt-3 p-3 bg-white border-l-2 border-[#C5A880] text-xs">
                                                        <span class="block text-[9px] font-bold uppercase tracking-widest text-[#C5A880] mb-1">Tanggapan Admin:</span>
                                                        <div class="text-gray-600 font-light prose max-w-none">{!! $fb->admin_reply !!}</div>
                                                    </div>
                                                @endif
                                                <p class="text-[9px] text-gray-400 font-bold tracking-widest uppercase pt-1">{{ $fb->created_at->format('d M Y, H:i') }} WIB</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- REVIEW -->
                <div id="review-section" class="bg-white border border-gray-100 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05)]">
                    <div class="px-8 py-6 border-b border-gray-100">
                        <span class="block text-[10px] tracking-[0.3em] uppercase text-[#C5A880] mb-1 font-medium">Pengalaman Anda</span>
                        <h3 class="font-light text-gray-900 text-2xl">Testimonial</h3>
                    </div>

                    <div class="p-8">
                        <form action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <input type="hidden" name="project_id" value="{{ $project->id }}">
                            <textarea name="message" rows="3" placeholder="Tuliskan kesan dan saran Anda..." required
                                class="w-full bg-[#FAFAFA] border-b-2 border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#C5A880] transition-colors resize-none"></textarea>
                                
                            <div>
                                <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-gray-500 mb-3">Foto Hasil Pengerjaan (Opsional)</label>
                                <input type="file" name="image" accept="image/*"
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-xs file:font-bold file:uppercase file:tracking-widest file:bg-[#111111] file:text-white hover:file:bg-[#C5A880] file:transition-colors file:cursor-pointer bg-[#FAFAFA] border-b-2 border-gray-200">
                            </div>

                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-3 text-[11px] font-bold tracking-[0.2em] uppercase border border-[#111111] text-[#111111] px-8 py-4 hover:bg-[#111111] hover:text-white transition-colors">
                                Kirim Review
                            </button>
                        </form>

                        @if(session('success'))
                            <div class="mt-6 p-4 bg-[#FAFAFA] border-l-2 border-[#C5A880] text-gray-900 text-sm font-medium tracking-wide">
                                {{ session('success') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="pb-20"></div>
    </main>
</div>

<!-- LIGHTBOX -->
<div id="lightbox" onclick="closeLightbox()" class="fixed inset-0 bg-[#FAFAFA]/95 backdrop-blur-sm z-[100] items-center justify-center p-4">
    <img id="lightbox-img" src="" alt="Preview" class="max-w-full max-h-full border border-gray-200 shadow-2xl bg-white p-2">
    <button onclick="closeLightbox()" class="absolute top-6 right-6 w-12 h-12 bg-white flex items-center justify-center text-gray-900 hover:bg-[#111111] hover:text-white transition-colors border border-gray-200">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>

<!-- FLOAT BUTTON -->
<a href="/penawaran" class="fixed bottom-8 right-8 z-40 bg-[#111111] text-white px-6 py-4 flex items-center gap-3 shadow-2xl hover:bg-[#C5A880] hover:-translate-y-1 transition-all group">
    <svg class="w-5 h-5 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    <span class="tracking-widest font-bold uppercase text-[10px]">Penawaran Baru</span>
</a>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
    $(document).ready(function() {
        $('#masukan-summernote').summernote({
            placeholder: 'Tuliskan catatan, masukan, atau kendala lapangan di sini...',
            height: 150,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['misc', ['fullscreen', 'codeview']]
            ],
            callbacks: {
                onInit: function() {
                    $('.note-editor').addClass('border-gray-200 border-2 rounded-none');
                    $('.note-toolbar').addClass('bg-[#FAFAFA] border-b-2 border-gray-200');
                    $('.note-statusbar').hide();
                }
            }
        });
    });

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
    function updateFileName(input) {
        const nameEl = document.getElementById('file-name');
        const iconEl = document.getElementById('upload-icon');
        if (input.files.length) {
            nameEl.textContent = input.files[0].name;
            nameEl.classList.remove('hidden');
            iconEl.classList.add('opacity-30', 'scale-95');
        } else {
            nameEl.classList.add('hidden');
            iconEl.classList.remove('opacity-30', 'scale-95');
        }
    }
    function updateSuratFileName(input) {
        const nameEl = document.getElementById('surat-file-name');
        const iconEl = document.getElementById('surat-upload-icon');
        if (input.files.length) {
            const file = input.files[0];
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file melebihi 2 MB. Silakan pilih file yang lebih kecil.');
                input.value = '';
                nameEl.classList.add('hidden');
                iconEl.classList.remove('opacity-30', 'scale-95');
                return;
            }
            nameEl.textContent = file.name;
            nameEl.classList.remove('hidden');
            iconEl.classList.add('opacity-30', 'scale-95');
        } else {
            nameEl.classList.add('hidden');
            iconEl.classList.remove('opacity-30', 'scale-95');
        }
    }
    function openLightbox(src) {
        document.getElementById('lightbox-img').src = src;
        document.getElementById('lightbox').classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeLightbox() {
        document.getElementById('lightbox').classList.remove('open');
        document.body.style.overflow = '';
    }
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeLightbox();
    });
</script>

</body>
</html>
