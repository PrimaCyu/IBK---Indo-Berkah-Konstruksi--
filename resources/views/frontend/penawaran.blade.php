<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dapatkan Penawaran - Indo Berkah Konstruksi</title>
    
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    <!-- AOS Animation CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.css" rel="stylesheet">
    
    <style>
        /* Custom File Input Styles */
        input[type="file"]::file-selector-button {
            border: 1px solid #C5A880;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            background-color: transparent;
            color: #C5A880;
            transition: all 0.3s ease;
            cursor: pointer;
            margin-right: 1rem;
        }
        input[type="file"]::file-selector-button:hover {
            background-color: #C5A880;
            color: white;
        }
    </style>
</head>
<body class="bg-[#FAFAFA] text-gray-900 font-sans antialiased overflow-x-hidden">

@include('layout.header')

<!-- Page Hero -->
<section class="w-full bg-[#111111] pt-32 pb-20 md:pt-40 md:pb-28 relative">
    <div class="max-w-7xl mx-auto px-6 md:px-12 relative z-10 text-center" data-aos="fade-up">
        <span class="block text-[10px] tracking-[0.3em] uppercase text-[#C5A880] mb-4 font-medium">
            Mulai Berkonsultasi
        </span>
        <h1 class="text-3xl md:text-5xl lg:text-6xl font-light text-white tracking-wide mb-6">
            Rancang <span class="font-bold text-[#C5A880]">Masa Depan</span>
        </h1>
        <p class="text-gray-400 max-w-xl mx-auto font-light text-sm leading-relaxed">
            Sampaikan visi proyek Anda kepada kami. Tim ahli Indo Berkah Konstruksi siap mewujudkan ekspektasi Anda dengan presisi tingkat tinggi.
        </p>
    </div>
</section>

<!-- Form Section -->
<section class="py-20 md:py-28 relative">
    <div class="max-w-4xl mx-auto px-6 md:px-12">

        @if(session('penawaran_success'))
        <div class="mb-10 bg-green-50/50 border border-green-200 text-green-700 px-6 py-4 flex items-center justify-between" data-aos="fade-down">
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span class="text-sm font-medium tracking-wide">Terima kasih! Permintaan penawaran Anda berhasil dikirim. Tim kami akan segera menghubungi Anda.</span>
            </div>
        </div>
        @endif

        @if ($errors->any())
        <div class="mb-10 bg-red-50/50 border border-red-200 text-red-700 px-6 py-4" data-aos="fade-down">
            <div class="flex items-center gap-3 mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span class="font-bold text-sm tracking-wide">Mohon periksa kembali form Anda:</span>
            </div>
            <ul class="list-disc pl-9 text-xs font-light">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @php
            $layanan = request('layanan');
            $defaultDeskripsi = '';
            if ($layanan) {
                $layananName = ucwords(str_replace('-', ' ', $layanan));
                $defaultDeskripsi = "Saya tertarik dengan layanan $layananName. Tolong berikan penawaran dan informasi lebih lanjut mengenai hal ini.";
            }
        @endphp

        <form action="{{ route('penawaran.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-8 md:p-14 border border-gray-100 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05)]" data-aos="fade-up" data-aos-delay="100">
            @csrf

            <h2 class="text-2xl font-light text-gray-900 mb-10 pb-4 border-b border-gray-100">Informasi Proyek & Kontak</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Nama -->
                <div>
                    <label for="nama" class="block text-xs font-bold tracking-[0.1em] uppercase text-gray-500 mb-3">Nama Lengkap *</label>
                    <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required class="w-full bg-[#FAFAFA] border-b-2 border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#C5A880] transition-colors" placeholder="Masukkan nama Anda">
                </div>
                
                <!-- Telepon -->
                <div>
                    <label for="telepon" class="block text-xs font-bold tracking-[0.1em] uppercase text-gray-500 mb-3">No. Telepon / Whatsapp *</label>
                    <input type="tel" id="telepon" name="telepon" value="{{ old('telepon') }}" required class="w-full bg-[#FAFAFA] border-b-2 border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#C5A880] transition-colors" placeholder="Contoh: 08123456789">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-bold tracking-[0.1em] uppercase text-gray-500 mb-3">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full bg-[#FAFAFA] border-b-2 border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#C5A880] transition-colors" placeholder="email@contoh.com">
                </div>

                <!-- Budget -->
                <div>
                    <label for="budget" class="block text-xs font-bold tracking-[0.1em] uppercase text-gray-500 mb-3">Estimasi Budget *</label>
                    <select id="budget" name="budget"  class="w-full bg-[#FAFAFA] border-b-2 border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#C5A880] transition-colors appearance-none">
                        <option value="" disabled selected>Pilih kisaran budget</option>
                        <option value="< Rp 500 Juta" {{ old('budget') == '< Rp 500 Juta' ? 'selected' : '' }}>< Rp 500 Juta</option>
                        <option value="Rp 500 Juta - Rp 1 Miliar" {{ old('budget') == 'Rp 500 Juta - Rp 1 Miliar' ? 'selected' : '' }}>Rp 500 Juta - Rp 1 Miliar</option>
                        <option value="Rp 1 Miliar - Rp 5 Miliar" {{ old('budget') == 'Rp 1 Miliar - Rp 5 Miliar' ? 'selected' : '' }}>Rp 1 Miliar - Rp 5 Miliar</option>
                        <option value="> Rp 5 Miliar" {{ old('budget') == '> Rp 5 Miliar' ? 'selected' : '' }}>> Rp 5 Miliar</option>
                    </select>
                </div>
            </div>

            <!-- Alamat -->
            <div class="mb-8">
                <label for="alamat" class="block text-xs font-bold tracking-[0.1em] uppercase text-gray-500 mb-3">Alamat / Lokasi Proyek</label>
                <textarea id="alamat" name="alamat" rows="2" class="w-full bg-[#FAFAFA] border-b-2 border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#C5A880] transition-colors resize-none" placeholder="Masukkan lokasi rencana pembangunan">{{ old('alamat') }}</textarea>
            </div>

            <!-- Deskripsi Proyek -->
            <div class="mb-8">
                <label for="deskripsi" class="block text-xs font-bold tracking-[0.1em] uppercase text-gray-500 mb-3">Deskripsi Kebutuhan *</label>
                <textarea id="deskripsi" name="deskripsi" rows="5" required class="w-full bg-[#FAFAFA] border-b-2 border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#C5A880] transition-colors resize-none" placeholder="Jelaskan secara singkat jenis bangunan, desain yang diinginkan, atau detail lainnya">{{ old('deskripsi', $defaultDeskripsi) }}</textarea>
            </div>

            <!-- File Upload -->
            <div class="mb-12">
                <label for="foto" class="block text-xs font-bold tracking-[0.1em] uppercase text-gray-500 mb-3">Lampiran Foto/Desain (Opsional)</label>
                <p class="text-xs text-gray-400 font-light mb-4">Format: JPG, PNG, WEBP. Maks: 5MB. Unggah sketsa atau referensi visual Anda jika ada.</p>
                <input type="file" id="foto" name="foto" accept="image/jpeg, image/png, image/webp" class="w-full text-sm text-gray-500 bg-[#FAFAFA] p-3 border-b-2 border-gray-200 focus:outline-none">
            </div>

            <!-- Submit Button -->
            <div class="text-right mt-12 pt-8 border-t border-gray-100">
                <button type="submit" class="inline-flex items-center justify-center gap-4 text-xs font-medium tracking-[0.15em] uppercase border border-transparent bg-[#C5A880] text-white px-8 py-4 hover:bg-[#A68A60] transition-all duration-500 hover:shadow-xl w-full md:w-auto group">
                    Kirim Permintaan
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </button>
            </div>

        </form>
    </div>
</section>

@include('layout.footer') 

<!-- AOS JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<!-- Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.js"></script>

<script>
    AOS.init({
        once: true,
        offset: 50,
        duration: 800,
        easing: 'ease-out-cubic',
    });

    $(document).ready(function() {
        $('#alamat').summernote({
            placeholder: 'Masukkan lokasi rencana pembangunan secara detail...',
            tabsize: 2,
            height: 120,
            toolbar: [
                ['font', ['bold', 'underline', 'clear']],
                ['para', ['ul', 'ol']],
                ['insert', ['link']]
            ]
        });

        $('#deskripsi').summernote({
            placeholder: 'Jelaskan secara singkat jenis bangunan, desain yang diinginkan, atau detail lainnya...',
            tabsize: 2,
            height: 250,
            toolbar: [
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['fullscreen']]
            ]
        });
    });
</script>

</body>
</html>
