<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $service['title'] }} - Indo Berkah Konstruksi</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- AOS Animation CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body class="bg-[#FAFAFA] text-gray-900 font-sans antialiased overflow-x-hidden">

@include('layout.header')

<!-- =======================
     HERO SECTION LAYANAN
======================== -->
<section class="relative w-full pt-32 pb-16 md:pt-40 md:pb-24 bg-[#3b2d2a] text-center overflow-hidden">
    <!-- Background element -->
    <div class="absolute inset-0 flex items-center justify-center opacity-5">
        <img src="{{ asset($service['icon']) }}" alt="Background" class="w-[500px] h-[500px] object-contain grayscale invert">
    </div>
    
    <div class="max-w-4xl mx-auto px-6 relative z-20" data-aos="fade-up" data-aos-duration="1200">
        <span class="block text-[10px] md:text-xs tracking-[0.4em] uppercase text-[#C5A880] mb-4 font-bold">
            Detail Layanan
        </span>
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-medium text-white leading-tight tracking-wide mb-6">
            {{ $service['title'] }}
        </h1>
        <p class="text-gray-300 text-sm md:text-base font-light max-w-2xl mx-auto leading-relaxed">
            {{ $service['description'] }}
        </p>
    </div>
</section>

<!-- =======================
     TATA CARA PEMESANAN / STEPS
======================== -->
<section class="py-24 md:py-32 bg-white relative">
    <div class="max-w-5xl mx-auto px-6 md:px-12">
        <div class="text-center max-w-3xl mx-auto mb-16 md:mb-24" data-aos="fade-up">
            <span class="block text-[10px] tracking-[0.3em] uppercase text-[#C5A880] mb-4 font-medium">
                Langkah Mudah
            </span>
            <h2 class="text-3xl md:text-5xl font-light text-gray-900 tracking-wide">
                Tata Cara <span class="font-bold">Pemesanan</span>
            </h2>
        </div>

        <div class="relative mt-12 md:mt-0">
            <!-- Vertical Line for Desktop -->
            <div class="hidden md:block absolute left-1/2 top-0 bottom-0 w-[2px] bg-gray-100 -translate-x-1/2"></div>
            
            <div class="space-y-16 md:space-y-0 relative">
                @foreach($service['steps'] as $index => $step)
                <div class="relative flex flex-col md:flex-row items-center justify-between group" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                    
                    <!-- Left Side (Empty on odd, content on even in mobile it's different) -->
                    <div class="w-full md:w-5/12 {{ $index % 2 == 0 ? 'md:text-right md:pr-12' : 'md:order-3 md:pl-12' }}">
                        <div class="bg-white p-8 rounded-xl shadow-[0_10px_30px_rgba(0,0,0,0.03)] border border-gray-100 group-hover:border-[#C5A880]/30 group-hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] transition-all duration-500 relative">
                            <!-- Mobile Number Badge -->
                            <div class="md:hidden absolute -top-5 -left-5 w-10 h-10 rounded-full bg-[#C5A880] text-white flex items-center justify-center font-bold text-sm shadow-lg border-4 border-white">
                                0{{ $index + 1 }}
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-900 mb-3 tracking-wide group-hover:text-[#C5A880] transition-colors mt-2 md:mt-0">
                                {{ $step['title'] }}
                            </h3>
                            <p class="text-gray-500 font-light text-sm leading-loose">
                                {{ $step['desc'] }}
                            </p>
                        </div>
                    </div>

                    <!-- Center Connector (Circle) -->
                    <div class="hidden md:flex absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-14 h-14 rounded-full bg-white border-4 border-gray-100 items-center justify-center z-10 group-hover:border-[#C5A880] group-hover:bg-[#C5A880] transition-all duration-500 {{ $index % 2 == 0 ? 'md:order-2' : 'md:order-2' }}">
                        <span class="text-gray-400 font-bold text-sm group-hover:text-white transition-colors duration-500">0{{ $index + 1 }}</span>
                    </div>
                    
                    <!-- Right Side Empty Placeholder for alignment -->
                    <div class="hidden md:block w-5/12 {{ $index % 2 == 0 ? 'md:order-3' : 'md:order-1' }}"></div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</section>

<!-- =======================
     CALL TO ACTION (AJUKAN KOLABORASI)
======================== -->
<section class="py-24 bg-[#111111] text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-t from-[#111] via-[#1a1514] to-[#111]"></div>
    
    <!-- Premium Accent Elements -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-[#C5A880]/5 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>

    <div class="max-w-4xl mx-auto px-6 text-center relative z-10" data-aos="zoom-in" data-aos-duration="1000">
        <span class="block text-[10px] tracking-[0.3em] uppercase text-[#C5A880] mb-4 font-medium">
            Mulai Sekarang
        </span>
        <h2 class="text-3xl md:text-5xl font-light tracking-wide mb-6">
            Siap Mewujudkan <span class="font-bold text-white">Proyek Anda?</span>
        </h2>
        <p class="text-gray-400 font-light mb-12 text-sm md:text-base max-w-xl mx-auto leading-relaxed">
            Mulai langkah pertama untuk membangun struktur premium impian Anda bersama tim ahli Indo Berkah Konstruksi.
        </p>
        <a href="{{ route('penawaran.index') }}" class="inline-flex items-center justify-center gap-4 text-xs font-bold tracking-[0.2em] uppercase bg-[#C5A880] text-[#111111] px-10 py-5 hover:bg-white hover:text-[#111111] transition-all duration-500 shadow-[0_10px_30px_rgba(197,168,128,0.3)] hover:shadow-[0_10px_40px_rgba(255,255,255,0.4)]">
            Minta Penawaran
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </a>
    </div>
</section>

@include('layout.footer')

<!-- AOS JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        AOS.init({
            once: true,
            offset: 50,
            duration: 800,
            easing: 'ease-out-cubic',
        });
    });
</script>

</body>
</html>
