<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Surat Perjanjian - Indo Berkah</title>
    <meta name="description" content="Upload surat perjanjian kerja sama dengan Indo Berkah Konstruksi secara mudah dan aman.">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-white text-gray-900 font-sans antialiased">
    @include('layout.header')

    <main class="pt-32 pb-24 md:pt-40 md:pb-32 px-6 md:px-12 min-h-screen bg-gray-50">
        <div class="max-w-3xl mx-auto">

            {{-- Header Section --}}
            <div class="text-center mb-12 md:mb-16">
                <p class="uppercase tracking-[0.3em] text-[#C5A880] text-xs md:text-sm mb-4 font-medium" data-aos="fade-down">
                    Dokumen Resmi
                </p>
                <h1 class="text-4xl md:text-5xl font-light text-gray-900 mb-6 tracking-wide" data-aos="fade-up" data-aos-delay="100">
                    Upload Surat <span class="font-bold text-[#C5A880]">Perjanjian</span>
                </h1>
                <div class="w-16 h-[1px] bg-[#C5A880] mx-auto mb-8"></div>
                <p class="text-gray-500 leading-relaxed font-light text-sm md:text-base" data-aos="fade-up" data-aos-delay="200">
                    Unggah dokumen surat perjanjian Anda dalam format PDF, DOC, atau DOCX. Maksimal ukuran file <strong>2 MB</strong>. Dokumen akan ditinjau oleh tim manajemen kami.
                </p>
            </div>

            {{-- Success Alert --}}
            @if(session('success'))
            <div class="mb-8 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-center font-medium" data-aos="fade-up">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
            @endif

            {{-- Error Global --}}
            @if($errors->any())
            <div class="mb-8 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm" data-aos="fade-up">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Form --}}
            <div class="bg-white shadow-xl border border-gray-100 rounded-xl p-8 md:p-12" data-aos="fade-up" data-aos-delay="300">
                <form action="{{ route('surat-perjanjian.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    {{-- Nama --}}
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" id="nama" required value="{{ old('nama') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#C5A880] focus:border-transparent outline-none transition-all bg-gray-50"
                            placeholder="Masukkan nama lengkap Anda">
                        @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#C5A880] focus:border-transparent outline-none transition-all bg-gray-50"
                                placeholder="email@contoh.com">
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Telepon --}}
                        <div>
                            <label for="telepon" class="block text-sm font-medium text-gray-700 mb-2">No. Telepon / WhatsApp</label>
                            <input type="tel" name="telepon" id="telepon" value="{{ old('telepon') }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#C5A880] focus:border-transparent outline-none transition-all bg-gray-50"
                                placeholder="081234567890">
                            @error('telepon') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Keterangan --}}
                    <div>
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan / Catatan</label>
                        <textarea name="keterangan" id="keterangan" rows="3"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#C5A880] focus:border-transparent outline-none transition-all bg-gray-50"
                            placeholder="Tuliskan keterangan terkait surat perjanjian ini (opsional)">{{ old('keterangan') }}</textarea>
                        @error('keterangan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Upload File --}}
                    <div>
                        <label for="file_surat" class="block text-sm font-medium text-gray-700 mb-2">
                            File Surat Perjanjian <span class="text-red-500">*</span>
                        </label>

                        {{-- Drop Zone --}}
                        <div id="drop-zone"
                            class="relative border-2 border-dashed border-gray-200 rounded-xl p-8 text-center hover:border-[#C5A880] transition-colors duration-300 cursor-pointer bg-gray-50"
                            onclick="document.getElementById('file_surat').click()">

                            <div id="drop-icon" class="flex flex-col items-center gap-3">
                                <div class="w-14 h-14 rounded-full bg-[#C5A880]/10 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-[#C5A880]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Klik untuk memilih file</p>
                                    <p class="text-xs text-gray-400 mt-1">atau drag & drop file di sini</p>
                                </div>
                                <p class="text-xs text-gray-400">PDF, DOC, DOCX — Maksimal <span class="font-semibold text-[#C5A880]">2 MB</span></p>
                            </div>

                            {{-- Preview nama file --}}
                            <div id="file-preview" class="hidden flex items-center justify-center gap-3 py-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#C5A880]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span id="file-name" class="text-sm font-medium text-gray-700"></span>
                                <button type="button" onclick="clearFile(event)" class="text-red-400 hover:text-red-600 text-xs underline ml-2">Hapus</button>
                            </div>

                            <input type="file" name="file_surat" id="file_surat" accept=".pdf,.doc,.docx" class="hidden" onchange="handleFileSelect(this)">
                        </div>

                        @error('file_surat') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                    </div>

                    {{-- Submit --}}
                    <div class="pt-2">
                        <button type="submit" id="submit-btn"
                            class="w-full bg-[#111] hover:bg-[#C5A880] text-white font-medium py-4 rounded-lg tracking-widest uppercase transition-colors duration-300 flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Kirim Surat Perjanjian
                        </button>
                    </div>
                </form>
            </div>

            {{-- Info box --}}
            <div class="mt-8 p-6 bg-amber-50 border border-amber-100 rounded-xl text-sm text-amber-700" data-aos="fade-up" data-aos-delay="400">
                <p class="font-medium mb-2">📋 Informasi Penting:</p>
                <ul class="space-y-1 font-light list-disc list-inside">
                    <li>Pastikan dokumen sudah ditandatangani sebelum diunggah</li>
                    <li>Format yang diterima: PDF, DOC, DOCX</li>
                    <li>Ukuran maksimal file: 2 MB</li>
                    <li>Tim kami akan menghubungi Anda dalam 1–3 hari kerja</li>
                </ul>
            </div>

        </div>
    </main>

    @include('layout.footer')

    <script>
    function handleFileSelect(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const maxSize = 2 * 1024 * 1024; // 2MB

            if (file.size > maxSize) {
                alert('Ukuran file melebihi 2 MB. Silakan pilih file yang lebih kecil.');
                input.value = '';
                return;
            }

            document.getElementById('drop-icon').classList.add('hidden');
            document.getElementById('file-preview').classList.remove('hidden');
            document.getElementById('file-name').textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
            document.getElementById('drop-zone').classList.add('border-[#C5A880]', 'bg-[#C5A880]/5');
        }
    }

    function clearFile(event) {
        event.stopPropagation();
        document.getElementById('file_surat').value = '';
        document.getElementById('drop-icon').classList.remove('hidden');
        document.getElementById('file-preview').classList.add('hidden');
        document.getElementById('drop-zone').classList.remove('border-[#C5A880]', 'bg-[#C5A880]/5');
    }

    // Drag & Drop
    const dropZone = document.getElementById('drop-zone');
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-[#C5A880]', 'bg-[#C5A880]/5');
    });
    dropZone.addEventListener('dragleave', () => {
        if (!document.getElementById('file_surat').files.length) {
            dropZone.classList.remove('border-[#C5A880]', 'bg-[#C5A880]/5');
        }
    });
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        const dt = e.dataTransfer;
        const fileInput = document.getElementById('file_surat');
        fileInput.files = dt.files;
        handleFileSelect(fileInput);
    });

    document.addEventListener('DOMContentLoaded', () => {
        if (typeof AOS !== 'undefined') AOS.init({ duration: 1000, once: true, offset: 50 });
    });
    </script>
</body>
</html>
