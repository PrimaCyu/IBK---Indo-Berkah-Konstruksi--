<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Galeri Karya & Dokumentasi | Indo Berkah Konstruksi</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- AOS Animation CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
        .split-card {
            transition: all 0.7s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .split-card:hover {
            flex-grow: 1.2;
        }
        .split-card:hover .bg-img {
            transform: scale(1.08);
            filter: brightness(0.65);
        }
        .left-card:hover .arrow-icon {
            transform: translate(-6px, -6px);
            background-color: #C5A880;
            border-color: #C5A880;
            color: #111;
        }
        .right-card:hover .arrow-icon {
            transform: translate(6px, -6px);
            background-color: #C5A880;
            border-color: #C5A880;
            color: #111;
        }
    </style>
</head>
<body class="bg-[#111111] text-gray-100 font-sans antialiased overflow-x-hidden min-h-screen flex flex-col justify-between">

@include('layout.header')

<!-- Main Split Container -->
<main class="flex-1 flex flex-col md:flex-row w-full h-[100vh] min-h-[600px] overflow-hidden pt-[72px]">
    
    <!-- Left Column: Portofolio -->
    <a href="{{ route('portofolio.list') }}" class="split-card left-card group relative flex-1 flex items-center justify-center p-8 md:p-16 overflow-hidden border-b md:border-b-0 md:border-r border-white/5 cursor-pointer">
        <!-- Background Image -->
        <div class="bg-img absolute inset-0 bg-cover bg-center bg-no-repeat transition-all duration-1000 ease-out filter brightness-[0.4]"
             style="background-image: url('{{ asset('image/Portofolio/porto2.jpg') }}')"></div>
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-[#111111]/90 via-[#111111]/30 to-transparent"></div>
        
        <!-- Content -->
        <div class="relative z-10 text-center max-w-md" data-aos="fade-right" data-aos-duration="1000">
            <span class="inline-block text-[10px] md:text-xs tracking-[0.4em] uppercase text-[#C5A880] mb-4 font-semibold">
                Galeri Desain & Konstruksi
            </span>
            <h2 class="text-4xl md:text-5xl lg:text-6xl font-light text-white leading-tight tracking-wide mb-6 group-hover:text-[#C5A880] transition-colors duration-500">
                Porto<span class="font-bold text-transparent bg-clip-text bg-gradient-to-r from-white to-gray-300">folio</span>
            </h2>
            <p class="text-gray-300 text-sm md:text-base font-light leading-relaxed mb-8 opacity-80 group-hover:opacity-100 transition-opacity">
                Saksikan mahakarya arsitektur, hunian mewah, gedung komersial, dan renovasi estetik yang telah kami selesaikan dengan presisi tinggi dan kepuasan maksimal.
            </p>
            <!-- Arrow pointing LEFT ← -->
            <div class="arrow-icon inline-flex w-14 h-14 rounded-full border border-white/30 items-center justify-center text-white transition-all duration-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </div>
        </div>
    </a>

    <!-- Right Column: Video -->
    <a href="{{ route('video') }}" class="split-card right-card group relative flex-1 flex items-center justify-center p-8 md:p-16 overflow-hidden cursor-pointer">
        <!-- Background Image -->
        <div class="bg-img absolute inset-0 bg-cover bg-center bg-no-repeat transition-all duration-1000 ease-out filter brightness-[0.4]"
             style="background-image: url('{{ asset('image/Portofolio/porto3.jpg') }}')"></div>
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-[#111111]/90 via-[#111111]/30 to-transparent"></div>
        
        <!-- Content -->
        <div class="relative z-10 text-center max-w-md" data-aos="fade-left" data-aos-duration="1000">
            <span class="inline-block text-[10px] md:text-xs tracking-[0.4em] uppercase text-[#C5A880] mb-4 font-semibold">
                Dokumentasi Lapangan
            </span>
            <h2 class="text-4xl md:text-5xl lg:text-6xl font-light text-white leading-tight tracking-wide mb-6 group-hover:text-[#C5A880] transition-colors duration-500">
                Karya <span class="font-bold text-transparent bg-clip-text bg-gradient-to-r from-[#C5A880] to-[#E3CBA8]">Visual</span>
            </h2>
            <p class="text-gray-300 text-sm md:text-base font-light leading-relaxed mb-8 opacity-80 group-hover:opacity-100 transition-opacity">
                Tonton komitmen, kerja keras, dan dedikasi tim konstruksi kami langsung di lapangan dari awal peletakan batu pertama hingga penyelesaian akhir.
            </p>
            <!-- Arrow pointing RIGHT → -->
            <div class="arrow-icon inline-flex w-14 h-14 rounded-full border border-white/30 items-center justify-center text-white transition-all duration-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </div>
        </div>
    </a>

</main>

@include('layout.footer')

<!-- AOS Animation Script -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init();
</script>

</body>
</html>
