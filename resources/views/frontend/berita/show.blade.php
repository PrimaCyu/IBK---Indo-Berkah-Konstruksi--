<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $berita->title }} - Indo Berkah Konstruksi</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        /* Typography for RichText Content */
        .prose p { margin-bottom: 1.25em; line-height: 1.8; font-weight: 300; }
        .prose h1, .prose h2, .prose h3, .prose h4 { color: #111; font-weight: 700; margin-top: 2em; margin-bottom: 1em; }
        .prose h2 { font-size: 1.875rem; }
        .prose h3 { font-size: 1.5rem; }
        .prose ul { list-style-type: disc; padding-left: 1.5em; margin-bottom: 1.25em; }
        .prose ol { list-style-type: decimal; padding-left: 1.5em; margin-bottom: 1.25em; }
        .prose a { color: #C5A880; text-decoration: underline; text-underline-offset: 4px; }
        .prose blockquote { border-left: 4px solid #C5A880; padding-left: 1em; font-style: italic; color: #555; }
        .prose img { max-width: 100%; height: auto; margin: 2em auto; border-radius: 4px; }
    </style>
</head>
<body class="bg-[#FAFAFA] text-gray-900 font-sans antialiased overflow-x-hidden">

@include('layout.header')    

<main class="pt-32 pb-16 md:pt-40 md:pb-24">
    <article class="max-w-4xl mx-auto px-6 md:px-12 bg-white p-8 md:p-12 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05)] border border-gray-100" data-aos="fade-up">
        
        <!-- Header Artikel -->
        <header class="text-center mb-10">
            @if($berita->kategori)
            <span class="inline-block text-[10px] tracking-[0.3em] uppercase text-[#C5A880] mb-4 font-bold border border-[#C5A880] px-3 py-1">
                {{ $berita->kategori->nama }}
            </span>
            @endif
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-light text-gray-900 leading-tight mb-6 tracking-wide">
                {{ $berita->title }}
            </h1>
            <div class="flex items-center justify-center gap-4 text-xs text-gray-500 tracking-wider">
                <span>{{ $berita->created_at->format('d M Y') }}</span>
                <span>•</span>
                <span>Indo Berkah Konstruksi</span>
                <span>•</span>
                <span class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    {{ $berita->views }}x Dilihat
                </span>
            </div>
        </header>

        <!-- Thumbnail -->
        @if($berita->image)
        <div class="w-full mb-12 overflow-hidden bg-gray-100">
            <img src="{{ asset('storage/' . $berita->image) }}" alt="{{ $berita->title }}" class="w-full h-auto max-h-[500px] object-cover">
        </div>
        @endif

        <!-- Konten Berita -->
        <div class="prose max-w-none text-gray-600">
            {!! $berita->content !!}
        </div>
        
        <!-- Share section -->
        <div class="mt-12 pt-8 border-t border-gray-100">
            <h4 class="text-sm font-bold text-gray-900 mb-4 tracking-widest uppercase text-center">Bagikan Berita Ini</h4>
            <div class="flex justify-center gap-4">
                <!-- WhatsApp -->
                <a href="https://api.whatsapp.com/send?text={{ urlencode($berita->title . ' ' . route('berita.show', $berita->slug)) }}" target="_blank" class="w-10 h-10 rounded-full bg-[#25D366] text-white flex items-center justify-center hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </a>
                
                <!-- Facebook -->
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('berita.show', $berita->slug)) }}" target="_blank" class="w-10 h-10 rounded-full bg-[#1877F2] text-white flex items-center justify-center hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>

                <!-- Twitter / X -->
                <a href="https://twitter.com/intent/tweet?text={{ urlencode($berita->title) }}&url={{ urlencode(route('berita.show', $berita->slug)) }}" target="_blank" class="w-10 h-10 rounded-full bg-black text-white flex items-center justify-center hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </a>
                
                <!-- Native Share for Mobile (handles IG, Tiktok, dll via sistem share bawaan) -->
                <button onclick="shareBerita()" class="w-10 h-10 rounded-full bg-gray-500 text-white flex items-center justify-center hover:scale-110 transition-transform" title="Bagikan ke Aplikasi Lainnya">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                </button>
            </div>
            
            <script>
            function shareBerita() {
                if (navigator.share) {
                    navigator.share({
                        title: '{{ $berita->title }}',
                        text: 'Baca berita menarik ini di Indo Berkah Konstruksi: {{ $berita->title }}',
                        url: '{{ route('berita.show', $berita->slug) }}',
                    })
                    .then(() => console.log('Successful share'))
                    .catch((error) => console.log('Error sharing', error));
                } else {
                    navigator.clipboard.writeText('{{ route('berita.show', $berita->slug) }}').then(() => {
                        alert('Link berita telah disalin ke clipboard! Silakan paste di Instagram, TikTok, atau aplikasi lainnya.');
                    });
                }
            }
            </script>
        </div>
        
        <div class="mt-8 pt-8 border-t border-gray-100 flex justify-between items-center">
            <a href="{{ route('berita.index') }}" class="inline-flex items-center gap-3 text-xs font-bold tracking-[0.2em] uppercase text-gray-900 hover:text-[#C5A880] transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                Kembali ke Berita
            </a>
        </div>
    </article>
</main>

<!-- Berita Terkait -->
@if($relatedBeritas->count() > 0)
<section class="py-16 bg-[#FAFAFA] border-t border-gray-200">
    <div class="max-w-7xl mx-auto px-6 md:px-12">
        <h3 class="text-2xl font-light text-gray-900 mb-10 text-center tracking-wide">Berita Terkait</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($relatedBeritas as $index => $related)
            <a href="{{ route('berita.show', $related->slug) }}" class="group block cursor-pointer bg-white border border-gray-100 hover:shadow-lg transition-all duration-300" data-aos="fade-up" data-aos-delay="{{ $index * 150 }}">
                <div class="overflow-hidden w-full aspect-[4/3] bg-gray-100">
                    @if($related->image)
                        <img src="{{ asset('storage/' . $related->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    @endif
                </div>
                <div class="p-6">
                    <span class="block text-xs text-[#C5A880] font-bold tracking-widest uppercase mb-2">{{ $related->kategori->nama ?? 'Berita' }}</span>
                    <h4 class="text-lg font-bold text-gray-900 leading-tight group-hover:text-[#C5A880] transition-colors line-clamp-2">
                        {{ $related->title }}
                    </h4>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@include('layout.footer') 

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ once: true, offset: 50, duration: 800, easing: 'ease-out-cubic' });
</script>
</body>
</html>
