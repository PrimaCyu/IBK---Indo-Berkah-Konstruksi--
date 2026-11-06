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
<section class="relative w-full pt-32 pb-16 md:pt-40 md:pb-24 bg-[#3b2d2a] text-center">
    <div class="max-w-4xl mx-auto px-6 relative z-20" data-aos="fade-up" data-aos-duration="1200">
        <span class="block text-xs md:text-sm tracking-[0.3em] uppercase text-[#C5A880] mb-4 font-medium">
            Kabar Terbaru
        </span>
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-medium text-white leading-tight tracking-wide mb-6">
            News  & <span class="font-light italic lowercase text-gray-300">Event</span>
        </h1>
        <p class="text-gray-300 text-sm md:text-base font-light max-w-2xl mx-auto">
            Temukan informasi terkini seputar dunia konstruksi, tips arsitektur, dan update proyek dari Indo Berkah Konstruksi.
        </p>
    </div>
</section>

<!-- =======================
     KONTEN BERITA
======================== -->
<section class="py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-6 md:px-12">
        
        <!-- Filter Kategori (Opsional jika ada kategori) -->
        @if($kategoris->count() > 0)
        <div class="flex flex-wrap justify-center gap-4 mb-16" data-aos="fade-up">
            <a href="{{ route('berita.index') }}" class="px-6 py-2 text-xs font-medium tracking-widest uppercase transition-colors duration-300 border {{ request('kategori') == '' ? 'bg-[#C5A880] text-white border-[#C5A880]' : 'bg-transparent text-gray-600 border-gray-300 hover:border-[#C5A880] hover:text-[#C5A880]' }}">
                Semua
            </a>
            @foreach($kategoris as $kat)
            <a href="{{ route('berita.index', ['kategori' => $kat->id]) }}" class="px-6 py-2 text-xs font-medium tracking-widest uppercase transition-colors duration-300 border {{ request('kategori') == $kat->id ? 'bg-[#C5A880] text-white border-[#C5A880]' : 'bg-transparent text-gray-600 border-gray-300 hover:border-[#C5A880] hover:text-[#C5A880]' }}">
                {{ $kat->nama }}
            </a>
            @endforeach
        </div>
        @endif

        @if($beritas->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            @foreach($beritas as $index => $berita)
            <a href="{{ route('berita.show', $berita->slug) }}" class="group block cursor-pointer bg-white border border-gray-100 hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] transition-all duration-500 flex flex-col h-full" data-aos="fade-up" data-aos-delay="{{ ($index % 3) * 150 }}">
                <!-- Thumbnail -->
                <div class="overflow-hidden w-full aspect-[4/3] relative bg-gray-100">
                    @if($berita->image)
                        <img src="{{ asset('storage/' . $berita->image) }}" alt="{{ $berita->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                    <!-- Kategori Badge -->
                    @if($berita->kategori)
                    <div class="absolute top-4 left-4 bg-[#C5A880] text-white text-[10px] font-bold tracking-widest uppercase px-3 py-1">
                        {{ $berita->kategori->nama }}
                    </div>
                    @endif
                </div>
                
                <!-- Konten -->
                <div class="p-8 flex flex-col flex-grow">
                    <span class="block text-xs text-gray-400 mb-3 font-light">{{ $berita->created_at->format('d M Y') }}</span>
                    <h3 class="text-xl font-bold text-gray-900 leading-tight mb-4 group-hover:text-[#C5A880] transition-colors line-clamp-2">
                        {{ $berita->title }}
                    </h3>
                    <div class="text-gray-500 text-sm font-light line-clamp-3 mb-6 flex-grow">
                        {!! Str::limit(strip_tags($berita->content), 120) !!}
                    </div>
                    <div class="mt-auto inline-flex items-center gap-2 text-xs font-bold tracking-[0.2em] uppercase text-gray-900 group-hover:text-[#C5A880] transition-colors">
                        Baca Selengkapnya
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-16 flex justify-center">
            {{ $beritas->links() }}
        </div>
        
        @else
        <div class="text-center py-20">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15M9 11l3 3m0 0l3-3m-3 3V8"/></svg>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Berita</h3>
            <p class="text-gray-500 font-light">Berita atau informasi terbaru akan segera hadir di sini.</p>
        </div>
        @endif

    </div>
</section>

@include('layout.footer') 

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ once: true, offset: 50, duration: 800, easing: 'ease-out-cubic' });
</script>
</body>
</html>
