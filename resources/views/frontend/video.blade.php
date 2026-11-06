<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Indo Berkah Konstruksi - Karya Visual</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    <!-- AOS Animation CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body class="bg-[#FAFAFA] text-gray-900 font-sans antialiased overflow-x-hidden">

@include('layout.header')

<!-- Hero Section -->
<section class="relative w-full pt-40 pb-20 px-6 md:px-12 bg-[#111]">
    <div class="max-w-7xl mx-auto" data-aos="fade-up" data-aos-duration="1000">
        <span class="inline-block text-[10px] md:text-xs tracking-[0.4em] uppercase text-[#C5A880] mb-4 font-medium">
            Dokumentasi Proyek
        </span>
        <h1 class="text-4xl md:text-6xl lg:text-7xl font-light text-white leading-tight tracking-wide mb-8">
            Karya <span class="font-bold text-transparent bg-clip-text bg-gradient-to-r from-[#C5A880] to-[#E3CBA8]">Visual</span>
        </h1>
        <p class="text-gray-400 text-sm md:text-base max-w-2xl font-light leading-relaxed">
            Saksikan dedikasi dan ketelitian kami dalam setiap tahap pembangunan. Video dokumentasi ini adalah bukti komitmen kami untuk mewujudkan mahakarya arsitektur yang tahan uji waktu.
        </p>
    </div>
</section>

<!-- Video Gallery Section -->
<section class="py-24 md:py-32 bg-[#FAFAFA]">
    <div class="max-w-7xl mx-auto px-6 md:px-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 lg:gap-16">
            
            @php
                function getYoutubeId($url) {
                    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match);
                    return $match[1] ?? null;
                }
            @endphp
            @forelse($videos as $index => $video)
                @php
                    $youtubeId = getYoutubeId($video->youtube_link);
                    $delay = ($index % 2 == 0) ? 100 : 300;
                @endphp
                <div class="group" data-aos="fade-up" data-aos-delay="{{ $delay }}">
                    <div class="relative w-full aspect-video bg-[#111] overflow-hidden mb-6 shadow-2xl rounded-xl">
                        @if($youtubeId)
                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $youtubeId }}?rel=0" title="{{ $video->title }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                        @else
                            <a href="{{ $video->youtube_link }}" target="_blank" class="block w-full h-full relative">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-16 h-16 rounded-full border border-white/50 flex items-center justify-center backdrop-blur-sm hover:bg-[#C5A880] hover:border-[#C5A880] transition-all duration-500">
                                        <svg class="w-6 h-6 text-white ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4l12 6-12 6z"/></svg>
                                    </div>
                                </div>
                            </a>
                        @endif
                    </div>
                    <h2 class="text-2xl font-light text-gray-900 tracking-wide mb-3 hover:text-[#C5A880] transition-colors">
                        {{ $video->title }}
                    </h2>
                </div>
            @empty
                <div class="col-span-1 md:col-span-2 text-center text-gray-500 py-10">
                    Belum ada video dokumentasi.
                </div>
            @endforelse

        </div>
    </div>
</section>

@include('layout.footer')

<!-- AOS Animation Script -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        once: true,
        offset: 50,
        duration: 800,
        easing: 'ease-out-cubic',
    });
</script>
</body>
</html>
