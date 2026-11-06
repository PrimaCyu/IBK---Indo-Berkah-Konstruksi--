<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Berita & Informasi - Indo Berkah Konstruksi</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body class="bg-[#FAFAFA] text-gray-900 font-sans antialiased overflow-x-hidden">

@include('layout.header')

<!-- =======================
     HERO SECTION BERITA
======================== -->
<section class="relative w-full pt-32 pb-20 md:pt-40 md:pb-28 bg-[#3b2d2a] text-center overflow-hidden">
    <!-- Decorative background glow & pattern -->
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#C5A880_1px,transparent_1px)] [background-size:24px_24px] pointer-events-none"></div>
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-[#C5A880]/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-[#C5A880]/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-4xl mx-auto px-6 relative z-20" data-aos="fade-up" data-aos-duration="1000">
        <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-medium text-white leading-tight tracking-wide mb-6 uppercase">
            News, Event & <span class="font-light italic lowercase text-gray-300">Inspirasi</span>
        </h1>

        <p class="text-gray-200 text-sm md:text-base font-light max-w-2xl mx-auto leading-relaxed mb-8 opacity-90">
            Jelajahi berita konstruksi terkini, tips arsitektur premium, dan dokumentasi kemajuan proyek mahakarya Indo Berkah Konstruksi.
        </p>

        <!-- Live Statistics Counter Chips -->
        <div class="flex flex-wrap items-center justify-center gap-3 md:gap-6 pt-2 pb-6 border-t border-white/10 max-w-2xl mx-auto">
            <div class="flex items-center gap-2 px-3 py-1.5 rounded bg-black/30 border border-white/5 text-gray-300 text-xs">
                <svg class="w-4 h-4 text-[#C5A880]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15M9 11l3 3m0 0l3-3m-3 3V8"/>
                </svg>
                <span><strong class="text-white font-semibold">{{ $totalArticles }}</strong> Artikel</span>
            </div>

            <div class="flex items-center gap-2 px-3 py-1.5 rounded bg-black/30 border border-white/5 text-gray-300 text-xs">
                <svg class="w-4 h-4 text-[#C5A880]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <span><strong class="text-white font-semibold">{{ number_format($totalViews) }}</strong> Total Views</span>
            </div>

            <div class="flex items-center gap-2 px-3 py-1.5 rounded bg-black/30 border border-white/5 text-gray-300 text-xs">
                <svg class="w-4 h-4 text-[#C5A880]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                <span><strong class="text-white font-semibold">{{ $kategoris->count() }}</strong> Kategori</span>
            </div>
        </div>

        <!-- Search Bar -->
        <form action="{{ route('berita.index') }}" method="GET" class="max-w-lg mx-auto relative mt-4">
            @if(request('kategori'))
                <input type="hidden" name="kategori" value="{{ request('kategori') }}">
            @endif
            <div class="relative flex items-center">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari topik atau judul berita..."
                    class="w-full bg-[#111111]/80 backdrop-blur-md border border-[#C5A880]/40 text-gray-100 placeholder-gray-400 text-xs md:text-sm rounded-full pl-5 pr-28 py-3.5 focus:outline-none focus:border-[#C5A880] focus:ring-1 focus:ring-[#C5A880] transition-all">
                <button type="submit"
                    class="absolute right-1.5 top-1.5 bottom-1.5 px-4 bg-[#C5A880] hover:bg-[#b0936b] text-[#111111] text-xs font-semibold tracking-wider uppercase rounded-full flex items-center gap-1.5 transition-colors cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Cari</span>
                </button>
            </div>
        </form>
    </div>
</section>

<!-- =======================
     KONTEN BERITA
======================== -->
<section class="py-14 md:py-20 bg-[#FAFAFA]">
    <div class="max-w-7xl mx-auto px-6 md:px-12">

        <!-- TRENDING / POPULER STRIP (Tampil saat tidak sedang search) -->
        @if(!request('search') && !request('kategori') && $popularBeritas->count() >= 2)
        <div class="mb-14 p-6 md:p-8 bg-gradient-to-r from-[#1c1817] via-[#241e1c] to-[#1c1817] border border-[#C5A880]/20 rounded-xl shadow-lg" data-aos="fade-up">
            <div class="flex items-center justify-between mb-6 border-b border-white/10 pb-4">
                <div class="flex items-center gap-2">
                    <span class="flex h-2.5 w-2.5 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#C5A880] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#C5A880]"></span>
                    </span>
                    <h3 class="text-sm md:text-base font-bold tracking-wider text-white uppercase">
                        Sedang Ramai Dibaca
                    </h3>
                </div>
                <span class="text-[11px] text-[#C5A880] font-medium tracking-widest uppercase">Trending News</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($popularBeritas as $rank => $pop)
                <a href="{{ route('berita.show', $pop->slug) }}" class="group flex gap-4 items-center bg-white/5 hover:bg-white/10 border border-white/5 hover:border-[#C5A880]/40 p-3.5 rounded-lg transition-all duration-300">
                    <div class="relative w-20 h-20 shrink-0 overflow-hidden rounded bg-gray-800">
                        @if($pop->image)
                            <img src="{{ asset('storage/' . $pop->image) }}" alt="{{ $pop->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-500">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15M9 11l3 3m0 0l3-3m-3 3V8"/></svg>
                            </div>
                        @endif
                        <span class="absolute top-1 left-1 w-5 h-5 bg-[#C5A880] text-[#111] text-[10px] font-extrabold flex items-center justify-center rounded">
                            {{ $rank + 1 }}
                        </span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="inline-flex items-center gap-1 text-[10px] text-[#C5A880] font-semibold tracking-wider uppercase mb-1">
                            <svg class="w-3 h-3 text-[#C5A880]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            {{ number_format($pop->views) }} views
                        </span>
                        <h4 class="text-xs md:text-sm font-semibold text-gray-200 group-hover:text-[#C5A880] transition-colors line-clamp-2 leading-snug">
                            {{ $pop->title }}
                        </h4>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Filter Kategori Tabs -->
        @if($kategoris->count() > 0)
        <div class="flex flex-wrap items-center justify-center gap-2 md:gap-3 mb-12" data-aos="fade-up">
            <a href="{{ route('berita.index', request('search') ? ['search' => request('search')] : []) }}"
               class="px-5 py-2 text-xs font-semibold tracking-wider uppercase transition-all duration-300 rounded-full border {{ request('kategori') == '' ? 'bg-[#C5A880] text-[#111] border-[#C5A880] shadow-md shadow-[#C5A880]/20' : 'bg-white text-gray-600 border-gray-200 hover:border-[#C5A880] hover:text-[#C5A880]' }}">
                Semua <span class="ml-1 opacity-75 text-[10px]">({{ $totalArticles }})</span>
            </a>
            @foreach($kategoris as $kat)
            <a href="{{ route('berita.index', array_merge(request('search') ? ['search' => request('search')] : [], ['kategori' => $kat->id])) }}"
               class="px-5 py-2 text-xs font-semibold tracking-wider uppercase transition-all duration-300 rounded-full border {{ request('kategori') == $kat->id ? 'bg-[#C5A880] text-[#111] border-[#C5A880] shadow-md shadow-[#C5A880]/20' : 'bg-white text-gray-600 border-gray-200 hover:border-[#C5A880] hover:text-[#C5A880]' }}">
                {{ $kat->nama }} <span class="ml-1 opacity-75 text-[10px]">({{ $kat->beritas_count }})</span>
            </a>
            @endforeach
        </div>
        @endif

        <!-- Search Notice jika ada pencarian -->
        @if(request('search'))
        <div class="mb-8 flex items-center justify-between bg-amber-50 border border-amber-200 px-5 py-3 rounded-lg text-xs md:text-sm text-amber-900">
            <span>Menampilkan hasil pencarian untuk: <strong>"{{ request('search') }}"</strong> ({{ $beritas->total() }} berita ditemukan)</span>
            <a href="{{ route('berita.index', request('kategori') ? ['kategori' => request('kategori')] : []) }}" class="text-xs font-bold text-[#C5A880] hover:underline uppercase tracking-wider ml-4">Hapus Filter</a>
        </div>
        @endif

        @if($beritas->count() > 0)
        <!-- Grid Artikel -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-10">
            @foreach($beritas as $index => $berita)
            @php
                $readTime = max(1, (int) ceil(str_word_count(strip_tags($berita->content)) / 180));
            @endphp
            <article class="group bg-white border border-gray-100 rounded-xl overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.04)] hover:shadow-[0_20px_40px_-15px_rgba(197,168,128,0.2)] hover:border-[#C5A880]/40 transition-all duration-500 flex flex-col h-full" data-aos="fade-up" data-aos-delay="{{ ($index % 3) * 120 }}">
                <!-- Thumbnail Container -->
                <div class="overflow-hidden w-full aspect-[16/10] relative bg-gray-100">
                    <a href="{{ route('berita.show', $berita->slug) }}" class="block w-full h-full">
                        @if($berita->image)
                            <img src="{{ asset('storage/' . $berita->image) }}" alt="{{ $berita->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                    </a>

                    <!-- Top Badges: Kategori (kiri) & Views Count (kanan) -->
                    <div class="absolute top-3 inset-x-3 flex items-center justify-between pointer-events-none">
                        @if($berita->kategori)
                        <span class="bg-[#C5A880] text-[#111111] text-[10px] font-bold tracking-widest uppercase px-3 py-1 rounded shadow-md pointer-events-auto">
                            {{ $berita->kategori->nama }}
                        </span>
                        @else
                        <span></span>
                        @endif

                        <!-- Visitor / Views Count Badge -->
                        <span class="inline-flex items-center gap-1.5 bg-black/75 backdrop-blur-md text-white text-[10px] font-semibold px-2.5 py-1 rounded-full border border-white/10 shadow-md pointer-events-auto" title="{{ $berita->views }} pembaca telah melihat artikel ini">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-[#C5A880]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span class="tracking-wide">{{ number_format($berita->views) }}</span>
                            <span class="text-gray-300 font-light">views</span>
                        </span>
                    </div>
                </div>

                <!-- Konten Kartu -->
                <div class="p-6 md:p-7 flex flex-col flex-grow">
                    <!-- Tanggal & Waktu Baca -->
                    <div class="flex items-center gap-3 text-xs text-gray-400 mb-3 font-light">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $berita->created_at->format('d M Y') }}
                        </span>
                        <span>•</span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $readTime }} min baca
                        </span>
                    </div>

                    <!-- Judul -->
                    <h3 class="text-lg md:text-xl font-bold text-gray-900 leading-snug mb-3 group-hover:text-[#C5A880] transition-colors line-clamp-2">
                        <a href="{{ route('berita.show', $berita->slug) }}">
                            {{ $berita->title }}
                        </a>
                    </h3>

                    <!-- Ringkasan Excerpt -->
                    <p class="text-gray-500 text-xs md:text-sm font-light leading-relaxed line-clamp-3 mb-6 flex-grow">
                        {{ Str::limit(strip_tags($berita->content), 130) }}
                    </p>

                    <!-- Card Footer -->
                    <div class="pt-4 border-t border-gray-100 mt-auto flex items-center justify-between">
                        <span class="text-[11px] font-medium text-gray-400 uppercase tracking-wider">
                            Indo Berkah
                        </span>

                        <a href="{{ route('berita.show', $berita->slug) }}" class="inline-flex items-center gap-1.5 text-xs font-bold tracking-wider uppercase text-gray-900 group-hover:text-[#C5A880] transition-all">
                            <span>Baca</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-14 flex justify-center">
            {{ $beritas->links() }}
        </div>

        @else
        <!-- Empty State -->
        <div class="text-center py-20 bg-white border border-gray-200 rounded-xl p-8 max-w-lg mx-auto shadow-sm">
            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15M9 11l3 3m0 0l3-3m-3 3V8"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Tidak Ada Berita Ditemukan</h3>
            <p class="text-gray-500 text-xs md:text-sm font-light mb-6">
                @if(request('search'))
                    Tidak ditemukan berita yang cocok dengan kata kunci "{{ request('search') }}".
                @else
                    Belum ada berita yang tersedia dalam kategori ini.
                @endif
            </p>
            <a href="{{ route('berita.index') }}" class="inline-flex items-center px-6 py-2.5 bg-[#C5A880] hover:bg-[#b0936b] text-[#111111] text-xs font-semibold tracking-widest uppercase rounded transition-colors">
                Lihat Semua Berita
            </a>
        </div>
        @endif

    </div>
</section>

@include('layout.footer')

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ once: true, offset: 40, duration: 800, easing: 'ease-out-cubic' });
</script>
</body>
</html>
