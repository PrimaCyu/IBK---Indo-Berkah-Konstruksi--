<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login - Indo Berkah Konstruksi</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-[#FAFAFA] text-gray-900 font-sans antialiased flex flex-col pt-24">
    @include('layout.header')

    <div class="flex-1 flex items-center justify-center px-6 py-12 md:py-20">
        <form action="/login" method="POST" data-aos="fade-up"
            class="w-full max-w-md bg-white p-8 md:p-14 border border-gray-100 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05)]">

            @csrf

            @if(session('error'))
                <div class="mb-8 bg-red-50/50 border border-red-200 text-red-700 px-6 py-4 flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="text-sm tracking-wide">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Header -->
            <div class="mb-10 pb-4 border-b border-gray-100 text-center">
                <span class="block text-[10px] tracking-[0.3em] uppercase text-[#C5A880] mb-2 font-medium">
                    Selamat Datang
                </span>
                <h1 class="text-2xl md:text-3xl font-light text-gray-900 tracking-wide">
                    Login Akun
                </h1>
            </div>

            <!-- Email -->
            <div class="mb-8">
                <label class="block text-xs font-bold tracking-[0.1em] uppercase text-gray-500 mb-3">Email *</label>
                <input type="email" name="email" placeholder="Masukkan email Anda" required
                    class="w-full bg-[#FAFAFA] border-b-2 border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#C5A880] transition-colors">
            </div>

            <!-- Password -->
            <div class="mb-10">
                <label class="block text-xs font-bold tracking-[0.1em] uppercase text-gray-500 mb-3">Password *</label>
                <input type="password" name="password" placeholder="Masukkan password Anda" required
                    class="w-full bg-[#FAFAFA] border-b-2 border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#C5A880] transition-colors">
            </div>

            <!-- Button -->
            <button type="submit"
                class="w-full inline-flex items-center justify-center gap-4 text-xs font-medium tracking-[0.15em] uppercase border border-transparent bg-[#111111] text-white px-8 py-4 hover:bg-[#C5A880] transition-all duration-500 hover:shadow-xl group">
                Masuk Sekarang
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </button>

            <!-- Register link -->
            <p class="text-center text-xs text-gray-500 mt-8 tracking-wide">
                Belum memiliki akun?
                <a href="/register" class="text-[#C5A880] font-medium hover:text-[#A68A60] transition-colors">
                    Daftar di sini
                </a>
            </p>

        </form>
    </div>

    @include('layout.footer')

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ once: true, offset: 50, duration: 800, easing: 'ease-out-cubic' });
    </script>
</body>
</html>