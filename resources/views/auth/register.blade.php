<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register - Indo Berkah Konstruksi</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-[#FAFAFA] text-gray-900 font-sans antialiased flex flex-col pt-24">
    @include('layout.header')

    <div class="flex-1 flex items-center justify-center px-6 py-12 md:py-20">
        <form action="/register" method="POST" data-aos="fade-up"
            class="w-full max-w-md bg-white p-8 md:p-14 border border-gray-100 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05)]">

            @csrf

            @if($errors->any())
                <div class="mb-8 bg-red-50/50 border border-red-200 text-red-700 px-6 py-4 flex items-start gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <ul class="list-disc pl-4 text-xs font-light tracking-wide space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Header -->
            <div class="mb-10 pb-4 border-b border-gray-100 text-center">
                <span class="block text-[10px] tracking-[0.3em] uppercase text-[#C5A880] mb-2 font-medium">
                    Bergabung Bersama Kami
                </span>
                <h1 class="text-2xl md:text-3xl font-light text-gray-900 tracking-wide">
                    Buat Akun Baru
                </h1>
            </div>

            <!-- Nama -->
            <div class="mb-6">
                <label class="block text-xs font-bold tracking-[0.1em] uppercase text-gray-500 mb-3">Nama Lengkap *</label>
                <input type="text" name="name" placeholder="Masukkan nama Anda" value="{{ old('name') }}" required
                    class="w-full bg-[#FAFAFA] border-b-2 border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#C5A880] transition-colors">
            </div>

            <!-- Email -->
            <div class="mb-6">
                <label class="block text-xs font-bold tracking-[0.1em] uppercase text-gray-500 mb-3">Email *</label>
                <input type="email" name="email" placeholder="Masukkan email aktif" value="{{ old('email') }}" required
                    class="w-full bg-[#FAFAFA] border-b-2 border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#C5A880] transition-colors">
            </div>

            <!-- Password -->
            <div class="mb-8">
                <label class="block text-xs font-bold tracking-[0.1em] uppercase text-gray-500 mb-3">Password *</label>
                <input type="password" name="password" placeholder="Buat password" required
                    class="w-full bg-[#FAFAFA] border-b-2 border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#C5A880] transition-colors">
            </div>

            <!-- CAPTCHA UI (Placeholder) -->
            <div class="mb-10">
                <div class="flex items-center justify-between p-4 bg-[#FAFAFA] border border-gray-200 shadow-sm transition-all hover:border-[#C5A880]">
                    <div class="flex items-center gap-4">
                        <div class="relative flex items-center justify-center">
                            <input type="checkbox" id="captcha" name="captcha" required
                                class="peer w-6 h-6 appearance-none border-2 border-gray-300 bg-white checked:bg-[#C5A880] checked:border-[#C5A880] cursor-pointer transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="absolute w-4 h-4 text-white opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <label for="captcha" class="text-sm font-medium text-gray-700 cursor-pointer select-none">
                            Saya bukan robot
                        </label>
                    </div>
                    <div class="flex flex-col items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#C5A880] mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span class="text-[8px] tracking-[0.2em] uppercase text-gray-400 font-bold">Secure</span>
                    </div>
                </div>
            </div>

            <!-- Button -->
            <button type="submit"
                class="w-full inline-flex items-center justify-center gap-4 text-xs font-medium tracking-[0.15em] uppercase border border-transparent bg-[#111111] text-white px-8 py-4 hover:bg-[#C5A880] transition-all duration-500 hover:shadow-xl group">
                Daftar Sekarang
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </button>

            <!-- Login link -->
            <p class="text-center text-xs text-gray-500 mt-8 tracking-wide">
                Sudah memiliki akun?
                <a href="/login" class="text-[#C5A880] font-medium hover:text-[#A68A60] transition-colors">
                    Login di sini
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