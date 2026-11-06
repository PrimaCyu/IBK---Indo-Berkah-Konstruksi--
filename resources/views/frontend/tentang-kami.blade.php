<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tentang Kami</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-white">
    @include('layout.header')
    
<section class="bg-white pt-28 md:pt-40 pb-16 md:pb-20 px-4 md:px-6">
    <div class="max-w-7xl mx-auto">

        <!-- Header -->
        <div class="text-center mb-12 md:mb-20">
            <p class="uppercase tracking-[3px] md:tracking-[4px] text-gray-500 text-xs md:text-sm mb-3 md:mb-4">
                Tentang Kami
            </p>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-4 md:mb-6 leading-snug">
                Membangun Masa Depan Dengan
                Kualitas dan Kepercayaan
            </h1>
            <p class="max-w-3xl mx-auto text-gray-600 leading-relaxed text-sm md:text-base">
                Indo Berkah Konstruksi adalah perusahaan konstruksi yang berdedikasi untuk memberikan solusi konstruksi yang inovatif dan berkualitas. Dengan pengalaman yang luas dalam industri ini, Indo Berkah Konstruksi telah berhasil membangun reputasi yang solid sebagai mitra terpercaya dalam proyek-proyek konstruksi yang beragam. Perusahaan kami memiliki tim yang terampil dan berkomitmen tinggi, yang terdiri dari arsitek, insinyur, dan pekerja terampil lainnya yang bekerja sama untuk memberikan hasil yang luar biasa.
            </p>
        </div>

        <!-- Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 md:gap-16 items-center mb-16 md:mb-24">

            <!-- Image -->
            <div>
                <img
                    src="https://images.unsplash.com/photo-1504307651254-35680f356dfd"
                    alt="Construction"
                    class="rounded-2xl shadow-lg w-full h-[280px] sm:h-[380px] md:h-[450px] lg:h-[550px] object-cover"
                >
            </div>

            <!-- Text -->
            <div>
                <h2 class="text-2xl md:text-3xl font-bold mb-4 md:mb-6">
                    Solusi Konstruksi Profesional
                </h2>
                <p class="text-gray-600 leading-relaxed mb-4 md:mb-6 text-sm md:text-base">
                    Dengan pengalaman dalam berbagai proyek pembangunan,
                    kami menghadirkan layanan konstruksi yang mengutamakan
                    kualitas, keamanan, dan efisiensi pada setiap tahap pekerjaan.
                </p>
                <p class="text-gray-600 leading-relaxed mb-8 md:mb-10 text-sm md:text-base">
                    Kami percaya bahwa setiap proyek adalah investasi jangka
                    panjang. Oleh karena itu, setiap detail dirancang dan
                    dikerjakan dengan standar terbaik untuk menghasilkan
                    bangunan yang kokoh dan bernilai tinggi.
                </p>

                <div class="grid grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <h3 class="text-3xl md:text-4xl font-bold">100+</h3>
                        <p class="text-gray-500 text-sm md:text-base">Proyek Selesai</p>
                    </div>
                    <div>
                        <h3 class="text-3xl md:text-4xl font-bold">10+</h3>
                        <p class="text-gray-500 text-sm md:text-base">Tahun Pengalaman</p>
                    </div>
                    <div>
                        <h3 class="text-3xl md:text-4xl font-bold">50+</h3>
                        <p class="text-gray-500 text-sm md:text-base">Klien Puas</p>
                    </div>
                    <div>
                        <h3 class="text-3xl md:text-4xl font-bold">24/7</h3>
                        <p class="text-gray-500 text-sm md:text-base">Konsultasi</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Values -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 md:gap-8">

            <div class="border border-gray-200 rounded-2xl p-6 md:p-8">
                <h3 class="text-lg md:text-xl font-bold mb-3 md:mb-4">Integrity</h3>
                <p class="text-gray-600 text-sm md:text-base">
                    Menjalankan setiap proyek dengan kejujuran,
                    transparansi, dan tanggung jawab.
                </p>
            </div>

            <div class="border border-gray-200 rounded-2xl p-6 md:p-8">
                <h3 class="text-lg md:text-xl font-bold mb-3 md:mb-4">Responsibility</h3>
                <p class="text-gray-600 text-sm md:text-base">
                    Mengutamakan hasil konstruksi yang kuat,
                    presisi, dan sesuai standar terbaik.
                </p>
            </div>

            <div class="border border-gray-200 rounded-2xl p-6 md:p-8">
                <h3 class="text-lg md:text-xl font-bold mb-3 md:mb-4">Quality</h3>
                <p class="text-gray-600 text-sm md:text-base">
                    Menggunakan metode dan teknologi modern
                    untuk meningkatkan efisiensi pekerjaan.
                </p>
            </div>

        </div>

    </div>
</section>

@include('layout.footer') 
</body>
</html>