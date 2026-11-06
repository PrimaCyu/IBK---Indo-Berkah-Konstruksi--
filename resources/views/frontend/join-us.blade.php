<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Join Us - Indo Berkah</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- Summernote CSS & JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
</head>
<body class="bg-white text-gray-900 font-sans antialiased">
    @include('layout.header')
    
    <main class="pt-32 pb-24 md:pt-40 md:pb-32 px-6 md:px-12 min-h-screen bg-gray-50">
        <div class="max-w-3xl mx-auto">
            <!-- Header Section -->
            <div class="text-center mb-12 md:mb-16">
                <p class="uppercase tracking-[0.3em] text-[#C5A880] text-xs md:text-sm mb-4 font-medium" data-aos="fade-down">
                    Karir & Kolaborasi
                </p>
                <h1 class="text-4xl md:text-5xl font-light text-gray-900 mb-6 tracking-wide" data-aos="fade-up" data-aos-delay="100">
                    Bergabung Bersama <span class="font-bold text-[#C5A880]">Kami</span>
                </h1>
                <div class="w-16 h-[1px] bg-[#C5A880] mx-auto mb-8"></div>
                <p class="text-gray-500 leading-relaxed font-light text-sm md:text-base" data-aos="fade-up" data-aos-delay="200">
                    Isi formulir di bawah ini untuk mengajukan diri sebagai bagian dari tim kami. Profil Anda akan ditinjau oleh manajemen sebelum ditampilkan.
                </p>
            </div>

            <!-- Success Alert -->
            @if(session('success'))
            <div class="mb-8 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-center font-medium" data-aos="fade-up">
                {{ session('success') }}
            </div>
            @endif

            <!-- Form -->
            <div class="bg-white shadow-xl border border-gray-100 rounded-xl p-8 md:p-12" data-aos="fade-up" data-aos-delay="300">
                <form action="{{ route('join-us.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="nama" id="nama" value="{{ old('nama') }}" required placeholder="Contoh: Budi Santoso, S.T." class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#C5A880] focus:border-transparent outline-none transition-all bg-gray-50">
                        <p class="text-gray-400 text-xs mt-1">Nama lengkap hanya dapat diketahui oleh pihak admin/manajemen.</p>
                        @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="nama_pendek" class="block text-sm font-medium text-gray-700 mb-2">Nama Pendek / Panggilan *</label>
                        <input type="text" name="nama_pendek" id="nama_pendek" value="{{ old('nama_pendek') }}" required placeholder="Contoh: Budi" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#C5A880] focus:border-transparent outline-none transition-all bg-gray-50">
                        <p class="text-[#C5A880] text-xs mt-1">Nama ini yang akan ditampilkan di profil website publik (Our Team).</p>
                        @error('nama_pendek') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" id="email" required class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#C5A880] focus:border-transparent outline-none transition-all bg-gray-50">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="telepon" class="block text-sm font-medium text-gray-700 mb-2">No. Telepon / WhatsApp</label>
                        <input type="tel" name="telepon" id="telepon" required placeholder="Contoh: 081234567890" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#C5A880] focus:border-transparent outline-none transition-all bg-gray-50">
                        @error('telepon') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">Alamat </label>
                        <textarea name="alamat" id="alamat" rows="2" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#C5A880] focus:border-transparent outline-none transition-all bg-gray-50"></textarea>
                        @error('alamat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="posisi" class="block text-sm font-medium text-gray-700 mb-2">Posisi yang Dilamar</label>
                        <select name="posisi" id="posisi" required class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#C5A880] focus:border-transparent outline-none transition-all bg-gray-50">
                            <option value="">Pilih Posisi</option>
                            <option value="Arsitek">Arsitek</option>
                            <option value="Pekerja Konstruksi">Pekerja Konstruksi</option>
                            <option value="Konsultan">Konsultan</option>
                            <option value="Kepala Tukang">Kepala Tukang</option>
                            <option value="Pengawas Lapangan">Pengawas Lapangan</option>
                            <option value="Administrator">Administrator</option>
                            <option value="Pengawas Pemeriksa">Pengawas Pemeriksa</option>
                            <option value="Tukang">Tukang</option>
                            <option value="Asisten Tukang">Asisten Tukang</option>
                        </select>
                        @error('posisi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="profil_singkat" class="block text-sm font-medium text-gray-700 mb-2">Profil Singkat</label>
                        <textarea name="profil_singkat" id="profil_singkat" required></textarea>
                        @error('profil_singkat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="foto" class="block text-sm font-medium text-gray-700 mb-2">Foto Profil (Opsional)</label>
                        <input type="file" name="foto" id="foto" accept="image/*" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#C5A880] focus:border-transparent outline-none transition-all bg-gray-50 text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#C5A880]/10 file:text-[#C5A880] hover:file:bg-[#C5A880]/20">
                        @error('foto') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        <p class="text-xs text-gray-400 mt-2">Format: JPG, PNG. Maksimal ukuran 2MB.</p>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-[#111] hover:bg-[#C5A880] text-white font-medium py-4 rounded-lg tracking-widest uppercase transition-colors duration-300">
                            Kirim Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    @include('layout.footer')
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof AOS !== 'undefined') {
                AOS.init({ duration: 1000, once: true, offset: 50 });
            }
        });

        $(document).ready(function() {
            $('#profil_singkat').summernote({
                placeholder: 'Ceritakan keahlian, pengalaman, dan latar belakang Anda...',
                height: 180,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link']],
                    ['misc', ['fullscreen', 'codeview']]
                ],
                callbacks: {
                    onInit: function() {
                        $('.note-editor').addClass('border-gray-200 border rounded-lg overflow-hidden');
                        $('.note-toolbar').addClass('bg-gray-50 border-b border-gray-200');
                        $('.note-statusbar').hide();
                    }
                }
            });
        });
    </script>
</body>
</html>
