<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Indo Berkah Konstruksi - Mahakarya Arsitektur & Konstruksi Premium</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Indo Berkah Konstruksi melayani jasa pembangunan rumah, gedung komersial, infrastruktur, dan renovasi dengan kualitas premium dan ketahanan struktur terbaik.">
    <meta name="keywords" content="kontraktor bangunan, jasa konstruksi, bangun rumah, renovasi gedung, indo berkah konstruksi, arsitektur mewah">
    <meta name="author" content="Indo Berkah Konstruksi">
    <meta name="robots" content="index, follow">

    <!-- Open Graph (Untuk share di WhatsApp, Facebook, dll) -->
    <meta property="og:title" content="Indo Berkah Konstruksi - Layanan Konstruksi Premium">
    <meta property="og:description" content="Kami memadukan estetika modern dengan ketahanan struktur. Temukan layanan pembangunan rumah, gedung komersial, dan renovasi terbaik.">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ asset('image/Logo/Logo PT. Indo Berkah.png') }}">
    
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
 
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <!-- AOS Animation CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        /* Custom Swiper Navigation Color */
        .swiper-button-next, .swiper-button-prev {
            color: #C5A880 !important;
        }
        .swiper-pagination-bullet-active {
            background: #C5A880 !important;
        }
    </style>
</head>
<body class="bg-[#FAFAFA] text-gray-900 font-sans antialiased overflow-x-hidden">

@include('layout.header')    



<!-- =======================
     HERO CAROUSEL SECTION
======================== -->
<section id="hero" class="relative w-full h-screen min-h-[600px] md:min-h-[700px] flex flex-col md:flex-row overflow-hidden bg-[#3b2d2a]">
    
    @php
        $heroImg = !empty($settings['hero_image']) ? (str_starts_with($settings['hero_image'], 'image/') ? asset($settings['hero_image']) : asset('storage/' . $settings['hero_image'])) : asset('image/ElementProgram/rumah2.jpg');
    @endphp

    <!-- Mobile Background Overlay (Hanya tampil di layar kecil) -->
    <div class="absolute inset-0 z-0 block md:hidden">
        <img src="{{ $heroImg }}" alt="Main Project Mobile" class="w-full h-full object-cover brightness-[0.4]">
        <div class="absolute inset-0 bg-gradient-to-t from-[#3b2d2a] via-[#3b2d2a]/80 to-transparent"></div>
    </div>

    <!-- Left Side: Main Vertical Image (45%) -->
    <div class="relative w-[45%] h-full z-10 hidden md:block">
        <img src="{{ $heroImg }}" alt="Main Project" class="w-full h-full object-cover bg-gray-200 brightness-50">
    </div>

    <!-- Right Side: Dark Brown Content Area (55%) -->
    <div class="relative w-full md:w-[55%] h-full flex flex-col justify-center px-6 md:px-16 lg:px-24 z-20 pt-24 md:pt-0">
        
        <!-- Text Content -->
        <div class="max-w-xl relative z-30 mt-12 lg:mt-24" data-aos="fade-up" data-aos-duration="1500">
            <span class="block text-[10px] md:text-xs tracking-[0.4em] uppercase text-[#C5A880] mb-4 font-bold">
                Indo Berkah Konstruksi
            </span>
            <h1 class="text-[2.5rem] leading-[1.1] sm:text-5xl md:text-6xl lg:text-[4.5rem] font-medium text-white mb-4 md:mb-6 tracking-wide uppercase">
                {{ $settings['hero_title_line1'] ?? 'Menjaga Kualitas' }} <br>
                <span class="italic font-light tracking-normal lowercase text-gray-300">{{ $settings['hero_title_line2'] ?? 'mewujud' }}</span> <br>
                {{ $settings['hero_title_line3'] ?? 'Berkah.' }}
            </h1>
            <p class="text-gray-100 text-xs sm:text-sm md:text-base font-light leading-relaxed max-w-sm mb-10 opacity-90 md:opacity-100">
                {{ $settings['hero_subtitle'] ?? 'Membangun lingkungan masa depan yang berdampak. Kami memadukan estetika modern dengan ketahanan struktur tak tertandingi.' }}
            </p>
        </div>

        <!-- Overlapping Image 1 (Center) -->
        <div class="absolute top-[35%] -left-[15%] w-[320px] h-[400px] z-20 shadow-[0_30px_60px_rgba(0,0,0,0.4)] hidden lg:block" data-aos="fade-left" data-aos-duration="1500">
            <img src="{{ $heroImg }}" alt="Overlap 1" class="w-full h-full object-cover bg-gray-300 brightness-50">
        </div>

        <!-- Overlapping Image 2 (Bottom Right) -->
        <div class="absolute -bottom-10 right-[10%] w-[250px] h-[300px] z-20 shadow-[0_30px_60px_rgba(0,0,0,0.4)] hidden lg:block" data-aos="fade-up" data-aos-duration="1500">
            <img src="{{ $heroImg }}" alt="Overlap 2" class="w-full h-full object-cover bg-gray-400 brightness-50">
        </div>

        <!-- Bottom Left Text -->
        <div class="absolute bottom-10 md:bottom-16 left-6 md:left-16 z-30">
            <h3 class="text-white text-base md:text-xl tracking-widest font-medium uppercase mb-2">
                {{ $settings['hero_badge_title'] ?? 'Fokus pada Kualitas' }}
            </h3>
        </div>
    </div>
</section>


<!-- =======================
     ABOUT / TENTANG KAMI 
======================== -->
<section id="about" class="py-24 md:py-32 bg-white relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-6 md:px-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            
            @php
                $aboutImg = !empty($settings['about_image']) ? (str_starts_with($settings['about_image'], 'image/') ? asset($settings['about_image']) : asset('storage/' . $settings['about_image'])) : asset('image/ElementProgram/foto9.jpg');
            @endphp

            <!-- Left: Text -->
            <div data-aos="fade-right" data-aos-duration="1000">
                <span class="block text-[10px] tracking-[0.3em] uppercase text-[#ffb856] mb-4 font-medium">
                    {{ $settings['about_label'] ?? ' BestBuild Indo Berkah Konstruksi' }}
                </span>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-light text-gray-900 mb-8 leading-tight tracking-wide">
                    {{ $settings['about_title_line1'] ?? 'Pelopor Kualitas' }} <br>
                    <span class="font-bold">{{ $settings['about_title_line2'] ?? 'dan Keunggulan' }}</span><br>
                    {{ $settings['about_title_line3'] ?? 'dalam Setiap Proyek' }}
                </h2>
                <p class="text-gray-500 font-light leading-loose text-sm md:text-base mb-8">
                    {{ $settings['about_description'] ?? 'INDO BERKAH KONSTRUKSI adalah perusahaan jasa konstruksi yang menyediakan layanan pembangunan rumah, gedung, infrastruktur, renovasi, serta konstruksi besi dan baja.' }}
                </p>
                <a href="/tentang-kami" class="inline-flex items-center gap-4 text-xs font-bold tracking-[0.2em] uppercase text-gray-900 hover:text-[#C5A880] transition-colors duration-300">
                    Pelajari Lebih Lanjut
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>
            </div>
            
            <!-- Right: Image Composition -->
            <div class="relative h-[400px] md:h-[500px]" data-aos="fade-left" data-aos-duration="1200" data-aos-delay="200">
                <!-- Back Box Accent -->
                <div class="absolute top-10 right-0 w-3/4 h-full bg-[#FAFAFA] border border-gray-200"></div>
                <!-- Main Image -->
                <div class="absolute top-0 left-0 w-4/5 h-4/5 shadow-2xl overflow-hidden">
                    <img src="{{ $aboutImg }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-1000 brightness-70" alt="Konstruksi">
                </div>
                <!-- Floating Info Card -->
                <div class="absolute bottom-0 right-10 bg-[#111] text-white p-6 md:p-8 shadow-2xl w-2/3 border-t-2 border-[#C5A880]">
                    <div class="text-3xl md:text-4xl font-light text-[#C5A880] mb-2">{{ $settings['about_experience'] ?? '10+' }}</div>
                    <div class="text-xs tracking-widest uppercase font-light text-gray-400">{{ $settings['about_experience_text'] ?? 'Tahun Pengalaman Membangun Kepercayaan' }}</div>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- =======================
     PROGRAM KONSTRUKSI 
======================== -->
<section id="program" class="py-24 md:py-32 bg-[#FAFAFA]">
    <div class="max-w-7xl mx-auto px-6 md:px-12">
        
        <div class="text-center max-w-3xl mx-auto mb-16 md:mb-24" data-aos="fade-up">
            <span class="block text-[10px] tracking-[0.3em] uppercase text-[#C5A880] mb-4 font-medium">
                {{ $settings['program_label'] ?? 'Keahlian Kami' }}
            </span>
            <h2 class="text-3xl md:text-5xl font-light text-gray-900 tracking-wide">
                {{ $settings['program_title'] ?? 'Layanan' }} <span class="font-bold">{{ $settings['program_title_bold'] ?? 'Indo Berkah Konstruksi' }}</span>
            </h2>
        </div>

        @php
            $defaultLayanan = [
                1 => [
                    'title' => 'Pembangunan Rumah',
                    'description' => 'Kami menyediakan layanan pembangunan rumah dari tahap perencanaan hingga selesai dengan desain mewah dan material premium.',
                    'image' => 'image/ElementProgram/building-construction-industry-18-svgrepo-com.svg',
                    'slug' => 'pembangunan-rumah'
                ],
                2 => [
                    'title' => 'Gedung Komersial',
                    'description' => 'Melayani pembangunan gedung komersial dengan standar internasional, fokus pada efisiensi serta ketahanan struktur.',
                    'image' => 'image/ElementProgram/building-construction-industry-5-svgrepo-com.svg',
                    'slug' => 'gedung-komersial'
                ],
                3 => [
                    'title' => 'Renovasi Bangunan',
                    'description' => 'Solusi renovasi cerdas untuk meningkatkan nilai estetika, fungsi, dan kenyamanan properti eksklusif Anda.',
                    'image' => 'image/ElementProgram/building-concrete-construction-svgrepo-com.svg',
                    'slug' => 'renovasi-bangunan'
                ],
                4 => [
                    'title' => 'Konsultasi Ahli',
                    'description' => 'Konsultasi perencanaan desain dan manajemen konstruksi mendalam untuk memastikan mahakarya Anda terwujud sempurna.',
                    'image' => 'image/ElementProgram/blueprint-building-construction-svgrepo-com.svg',
                    'slug' => 'konsultasi-ahli'
                ]
            ];

            $layananCards = [];
            for ($i = 1; $i <= 4; $i++) {
                $imgVal = $settings["layanan_{$i}_image"] ?? '';
                $imgSrc = !empty($imgVal)
                    ? (str_starts_with($imgVal, 'image/') ? asset($imgVal) : asset('storage/' . $imgVal))
                    : asset($defaultLayanan[$i]['image']);
                
                $layananCards[] = [
                    'title'       => $settings["layanan_{$i}_title"] ?? $defaultLayanan[$i]['title'],
                    'description' => $settings["layanan_{$i}_description"] ?? $defaultLayanan[$i]['description'],
                    'image'       => $imgSrc,
                    'slug'        => $settings["layanan_{$i}_slug"] ?? $defaultLayanan[$i]['slug'],
                ];
            }
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($layananCards as $ci => $card)
            <a href="{{ $card['slug'] ? route('layanan.show', $card['slug']) : '#' }}" class="group block bg-white p-10 border border-gray-100 hover:border-[#C5A880] hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] transition-all duration-500" data-aos="fade-up" data-aos-delay="{{ ($ci + 1) * 100 }}">
                <div class="w-16 h-16 mb-8 text-[#C5A880] bg-transparent flex items-center justify-center group-hover:-translate-y-2 transition-transform duration-500">
                    <img src="{{ $card['image'] }}" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity" alt="{{ $card['title'] }}">
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-4 tracking-wide group-hover:text-[#C5A880] transition-colors">
                    {{ $card['title'] }}
                </h3>
                <p class="text-gray-500 text-sm font-light leading-loose">
                    {{ $card['description'] }}
                </p>
            </a>
            @endforeach

        </div>
    </div>
</section>


<!-- =======================
     FEATURED PORTFOLIO 
======================== -->
<section id="portofolio" class="py-24 md:py-32 bg-white border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-6 md:px-12">

        <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6" data-aos="fade-up">
            <div>
                <span class="block text-[10px] tracking-[0.3em] uppercase text-[#C5A880] mb-4 font-medium">
                    Koleksi Pilihan
                </span>
                <h2 class="text-3xl md:text-5xl font-light text-gray-900 tracking-wide">
                    Mahakarya <span class="font-bold">Terbaru</span>
                </h2>
            </div>
            <a href="{{ route('portofolio') }}" class="inline-flex items-center gap-3 text-xs font-medium tracking-[0.2em] uppercase border-b border-[#C5A880] text-gray-900 pb-1 hover:text-[#C5A880] transition-colors">
                Lihat Semua Proyek
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#C5A880]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>

        <!-- Horizontal scroll / Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">

            @foreach($portofolios as $index => $p)
            <div class="group cursor-pointer" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 150 }}">
                <div class="overflow-hidden bg-black mb-6 shadow-lg relative">
                    <img
                        src="{{ $p->main_image ? asset('storage/' . $p->main_image) : asset('image/Logo/logofix.png') }}"
                        alt="{{ $p->program }}"
                        class="w-full aspect-[4/3] object-cover group-hover:scale-105 group-hover:opacity-80 transition-all duration-700"
                    >
                </div>
                <span class="block text-[10px] tracking-[0.2em] uppercase text-[#C5A880] mb-2 font-medium">
                    {{ $p->kategori }}
                </span>
                <h3 class="text-xl md:text-2xl font-light text-gray-900 tracking-wide group-hover:text-[#C5A880] transition-colors">
                    {{ $p->program }}
                </h3>
            </div>
            @endforeach

        </div>
    </div>
</section>

<!-- =======================
     TESTIMONIALS / REVIEWS 
======================== -->
<section id="reviews" class="py-24 md:py-32 bg-[#111111] text-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-6 md:px-12">
        <div class="text-center max-w-3xl mx-auto mb-16 md:mb-24" data-aos="fade-up">
            <span class="block text-[10px] tracking-[0.3em] uppercase text-[#C5A880] mb-4 font-medium">
                Review Klien
            </span>
            <h2 class="text-3xl md:text-5xl font-light tracking-wide">
                Kepercayaan <span class="font-bold">Mereka</span>
            </h2>
        </div>

        @if($reviews->count() > 0)
        <!-- Swiper -->
        <div data-aos="fade-up" data-aos-delay="200">

            <!-- Autoplay Progress Bar -->
            <div class="relative mb-6 h-0.5 bg-white/10 max-w-xs mx-auto rounded-full overflow-hidden">
                <div id="review-progress" class="h-full bg-[#C5A880] rounded-full" style="width: 0%; transition: width linear;"></div>
            </div>

            <!-- Slide Counter -->
            <div class="text-center mb-8">
                <span id="review-counter" class="text-[10px] tracking-[0.3em] uppercase text-gray-500 font-medium">
                    <span id="review-current">1</span> / <span id="review-total">{{ $reviews->count() }}</span>
                </span>
            </div>

            <div class="swiper reviewSwiper">
                <div class="swiper-wrapper">
                    @foreach($reviews as $review)
                    <div class="swiper-slide">
                        <div class="bg-[#1a1a1a] p-10 md:p-14 border border-white/10 h-full flex flex-col justify-between hover:border-[#C5A880]/50 transition-colors duration-500">
                            <div>
                                @if($review->image)
                                    <img src="{{ Storage::url($review->image) }}" alt="Hasil Pekerjaan" class="w-full h-48 object-cover mb-8 border border-white/10 rounded">
                                @endif
                                <!-- Quote Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-[#C5A880] mb-8 opacity-50" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                                </svg>
                                <p class="text-gray-300 font-light leading-relaxed text-sm md:text-base italic mb-10 line-clamp-4">
                                    "{{ $review->message }}"
                                </p>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#C5A880] to-[#9c7f5f] flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                    {{ strtoupper(substr($review->user->name ?? 'User', 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="text-white font-medium tracking-wider">{{ $review->user->name ?? 'Anonim' }}</h4>
                                    <span class="text-[10px] text-gray-500 tracking-widest uppercase">{{ $review->created_at->format('d M Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <!-- Navigation -->
                <div class="flex justify-center items-center gap-8 mt-12">
                    <button class="swiper-button-prev-custom group w-12 h-12 border border-white/20 rounded-full hover:bg-[#C5A880] hover:border-[#C5A880] transition-all duration-300 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white group-hover:text-[#111] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <div class="swiper-pagination-custom flex items-center gap-2"></div>
                    <button class="swiper-button-next-custom group w-12 h-12 border border-white/20 rounded-full hover:bg-[#C5A880] hover:border-[#C5A880] transition-all duration-300 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white group-hover:text-[#111] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-10">
            <p class="text-gray-500 font-light">Belum ada review klien saat ini.</p>
        </div>
        @endif

        <!-- Form Tambah Review -->
        <div class="mt-20 max-w-3xl mx-auto bg-[#1a1a1a] border border-white/10 p-8 md:p-12" data-aos="fade-up" id="tambah-review">
            <div class="mb-8 text-center">
                <span class="block text-[10px] tracking-[0.3em] uppercase text-[#C5A880] mb-2 font-medium">Pengalaman Anda</span>
                <h3 class="font-light text-white text-2xl md:text-3xl">Bagikan Testimonial</h3>
                <p class="text-xs text-gray-400 mt-2">Ulasan dan masukan Anda sangat berarti untuk peningkatan layanan kami.</p>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-900/20 border-l-2 border-green-500 text-green-400 text-sm font-medium tracking-wide">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-900/20 border-l-2 border-red-500 text-red-400 text-sm font-medium tracking-wide">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @auth
                <div class="mb-6 flex items-center justify-between pb-4 border-b border-white/10">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-[#C5A880] to-[#9c7f5f] flex items-center justify-center text-white font-bold text-sm shadow">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Menulis review sebagai:</p>
                            <p class="text-sm text-white font-medium">{{ Auth::user()->name }}</p>
                        </div>
                    </div>
                    <span class="text-[10px] uppercase tracking-widest text-[#C5A880] border border-[#C5A880]/30 px-2.5 py-1 rounded">Terverifikasi</span>
                </div>
            @endauth

            <form action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                @guest
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-gray-400 mb-3">Nama Lengkap *</label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama Anda..." required
                                class="w-full bg-[#111] border-b-2 border-white/10 px-4 py-3 text-sm text-white focus:outline-none focus:border-[#C5A880] transition-colors placeholder-gray-600">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-gray-400 mb-3">Email / WhatsApp (Opsional)</label>
                            <input type="text" name="email" value="{{ old('email') }}" placeholder="Email atau nomor WhatsApp..."
                                class="w-full bg-[#111] border-b-2 border-white/10 px-4 py-3 text-sm text-white focus:outline-none focus:border-[#C5A880] transition-colors placeholder-gray-600">
                        </div>
                    </div>
                @endguest

                <div>
                    <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-gray-400 mb-3">Pesan Review *</label>
                    <textarea name="message" rows="3" placeholder="Tuliskan kesan dan saran Anda mengenai hasil pengerjaan proyek..." required
                        class="w-full bg-[#111] border-b-2 border-white/10 px-4 py-3 text-sm text-white focus:outline-none focus:border-[#C5A880] transition-colors resize-none placeholder-gray-600">{{ old('message') }}</textarea>
                </div>
                    
                <div>
                    <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-gray-400 mb-3">Foto Hasil Pengerjaan (Opsional)</label>
                    <input type="file" name="image" accept="image/*"
                        class="w-full text-sm text-gray-400 file:mr-4 file:py-2.5 file:px-6 file:border-0 file:text-[10px] file:font-bold file:uppercase file:tracking-widest file:bg-[#C5A880] file:text-[#111] hover:file:bg-white hover:file:text-[#111] file:transition-colors file:cursor-pointer bg-[#111] border-b-2 border-white/10 focus:border-[#C5A880] transition-colors">
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-3 text-[11px] font-bold tracking-[0.2em] uppercase border border-[#C5A880] bg-[#C5A880] text-[#111111] px-8 py-4 hover:bg-transparent hover:text-[#C5A880] transition-all duration-300">
                        Kirim Testimonial
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>




@include('layout.footer') 

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<!-- AOS JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
    // Initialize AOS
    AOS.init({
        once: true, // Animasi hanya berjalan sekali saat di-scroll
        offset: 50,
        duration: 800,
        easing: 'ease-out-cubic',
    });

    // Initialize Review Swiper
    const REVIEW_AUTOPLAY_DELAY = 5000;
    const progressBar = document.getElementById('review-progress');
    const currentEl = document.getElementById('review-current');
    let progressInterval = null;
    let progressStart = null;

    function startProgress() {
        if (!progressBar) return;
        clearInterval(progressInterval);
        progressStart = Date.now();
        progressBar.style.transition = 'none';
        progressBar.style.width = '0%';
        // Force reflow
        progressBar.offsetWidth;
        progressBar.style.transition = 'width ' + REVIEW_AUTOPLAY_DELAY + 'ms linear';
        progressBar.style.width = '100%';
    }

    var reviewSwiper = new Swiper(".reviewSwiper", {
        slidesPerView: 1,
        spaceBetween: 32,
        loop: true,
        loopAdditionalSlides: 2,
        speed: 800,
        autoplay: {
            delay: REVIEW_AUTOPLAY_DELAY,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
            waitForTransition: false,
        },
        pagination: {
            el: ".swiper-pagination-custom",
            clickable: true,
            renderBullet: function (index, className) {
                return '<span class="' + className + ' inline-block w-2 h-2 rounded-full bg-white/20 cursor-pointer transition-all duration-300"></span>';
            },
        },
        navigation: {
            nextEl: ".swiper-button-next-custom",
            prevEl: ".swiper-button-prev-custom",
        },
        breakpoints: {
            768: { slidesPerView: 2 },
            1024: { slidesPerView: 3 }
        },
        on: {
            init: function () {
                startProgress();
                if (currentEl) currentEl.textContent = this.realIndex + 1;
                // Pastikan autoplay benar-benar berjalan
                var sw = this;
                setTimeout(function() {
                    if (sw.autoplay && !sw.autoplay.running) {
                        sw.autoplay.start();
                    }
                }, 100);
            },
            slideChange: function () {
                startProgress();
                if (currentEl) currentEl.textContent = this.realIndex + 1;
            },
            autoplayPause: function () {
                if (progressBar) {
                    const elapsed = Date.now() - progressStart;
                    const pct = Math.min((elapsed / REVIEW_AUTOPLAY_DELAY) * 100, 100);
                    progressBar.style.transition = 'none';
                    progressBar.style.width = pct + '%';
                }
            },
            autoplayResume: function () {
                startProgress();
            },
        }
    });

    // Pastikan autoplay mulai setelah halaman selesai load
    window.addEventListener('load', function() {
        if (reviewSwiper && reviewSwiper.autoplay) {
            reviewSwiper.autoplay.start();
            startProgress();
        }
    });

    // Style active pagination bullet
    const style = document.createElement('style');
    style.textContent = '.swiper-pagination-custom .swiper-pagination-bullet-active { background-color: #C5A880 !important; width: 1.5rem !important; border-radius: 9999px !important; }';
    document.head.appendChild(style);
</script>

</body>
</html>