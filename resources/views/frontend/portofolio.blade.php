<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Portofolio</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100">
    @include('layout.header')

<section class="bg-gray-100 pt-24 md:pt-28 pb-16 px-4">
    <div class="max-w-7xl mx-auto">

        <!-- Filter — scrollable horizontal di mobile -->
        @php
            $categories = $portfolios->pluck('kategori')->filter()->unique();
        @endphp
        <div class="overflow-x-auto pb-2 mb-8 md:mb-10 -mx-4 px-4">
            <ul class="inline-flex overflow-hidden border border-gray-300 rounded-full bg-white whitespace-nowrap text-sm">
                <li>
                    <button onclick="filterPortfolio('all', this)" class="filter-btn px-4 md:px-5 py-2.5 md:py-3 text-amber-500 bg-amber-50 transition duration-300">
                        Semua
                    </button>
                </li>
                @foreach($categories as $cat)
                <li>
                    <button onclick="filterPortfolio('{{ $cat }}', this)" class="filter-btn px-4 md:px-5 py-2.5 md:py-3 hover:text-amber-400 text-gray-500 transition duration-300">
                        {{ $cat }}
                    </button>
                </li>
                @endforeach
            </ul>
        </div>

        <!-- Grid: 1 kolom mobile, 2 tablet, 4 desktop -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 md:gap-8">

            @forelse($portfolios as $portfolio)
            <div class="portfolio-item group relative overflow-hidden rounded-3xl shadow-lg cursor-pointer" data-category="{{ $portfolio->kategori }}" onclick="openPortfolioModal({{ $portfolio->id }})">
                <img
                    src="{{ $portfolio->main_image ? asset('storage/' . $portfolio->main_image) : asset('image/Logo/logofix.png') }}"
                    alt="{{ $portfolio->program }}"
                    class="w-full aspect-[4/5] md:aspect-auto md:h-[400px] object-cover group-hover:scale-105 transition-transform duration-700"
                >
                <!-- Label at the bottom -->
                <div class="absolute bottom-4 left-4 right-4 bg-[#FAFAFA] rounded-2xl p-4 flex items-center justify-between shadow-lg">
                    <div class="pr-2">
                        <span class="block text-[10px] tracking-[0.2em] font-bold text-[#C5A880] uppercase mb-1">{{ $portfolio->kategori }}</span>
                        <h3 class="text-gray-900 font-bold text-base md:text-lg line-clamp-1">{{ $portfolio->program }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-[#9c7f5f] text-white flex items-center justify-center flex-shrink-0 group-hover:bg-[#111111] transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-20 text-center">
                <p class="text-gray-500 font-light">Belum ada portofolio yang diunggah.</p>
            </div>
            @endforelse

        </div>

    </div>
</section>

@include('layout.footer') 
<div id="portfolio-modal" class="fixed inset-0 z-[100] hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
    <div class="bg-white rounded-3xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col shadow-2xl relative">
        <button onclick="closePortfolioModal()" class="absolute top-4 right-4 z-10 w-10 h-10 bg-black/50 text-white rounded-full flex items-center justify-center hover:bg-black transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="p-6 md:p-8 border-b border-gray-100 flex-shrink-0 mt-4 md:mt-0">
            <span id="modal-category" class="block text-[10px] tracking-[0.2em] font-bold text-[#C5A880] uppercase mb-1"></span>
            <h2 id="modal-title" class="text-2xl md:text-3xl font-bold text-gray-900 mb-2"></h2>


            <p class="text-sm text-gray-500 flex items-center gap-2 mt-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Waktu Pengerjaan: <span id="modal-date" class="font-medium text-gray-900"></span>
            </p>
        </div>
        <div class="p-6 md:p-8 overflow-y-auto bg-[#FAFAFA]">
            <div id="modal-description-container" class="mb-8 hidden">
                <h4 class="text-[10px] tracking-[0.2em] font-bold text-gray-400 uppercase mb-4">Deskripsi Proyek</h4>
                <div id="modal-description" class="text-gray-600 prose max-w-none text-sm md:text-base bg-white p-6 rounded-2xl shadow-sm border border-gray-100"></div>
            </div>
            <h4 class="text-[10px] tracking-[0.2em] font-bold text-gray-400 uppercase mb-4">Galeri Proyek</h4>
            <div id="modal-gallery" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <!-- Gallery images will be injected here -->
            </div>
        </div>
    </div>
</div>

<script>
    const portfolios = @json($portfolios);

    function openPortfolioModal(id) {
        const p = portfolios.find(item => item.id === id);
        if(!p) return;

        document.getElementById('modal-category').innerText = p.kategori || '-';
        document.getElementById('modal-title').innerText = p.program || '-';
        
        const descEl = document.getElementById('modal-description');
        const descContainerEl = document.getElementById('modal-description-container');
        if (p.deskripsi) {
            descEl.innerHTML = p.deskripsi;
            descContainerEl.classList.remove('hidden');
        } else {
            descEl.innerHTML = '';
            descContainerEl.classList.add('hidden');
        }
        
        let dateStr = '-';
        if(p.waktu_pengerjaan) {
            const d = new Date(p.waktu_pengerjaan);
            dateStr = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        }
        document.getElementById('modal-date').innerText = dateStr;

        const galleryContainer = document.getElementById('modal-gallery');
        galleryContainer.innerHTML = '';
        
        let images = [];
        if(p.main_image) images.push(p.main_image);
        if(p.gallery && Array.isArray(p.gallery)) {
            images = images.concat(p.gallery);
        }

        if(images.length > 0) {
            images.forEach(img => {
                const imgEl = document.createElement('img');
                imgEl.src = '/storage/' + img;
                imgEl.className = 'w-full aspect-square object-cover rounded-xl shadow-sm border border-gray-200 hover:scale-105 transition-transform duration-300';
                galleryContainer.appendChild(imgEl);
            });
        } else {
            galleryContainer.innerHTML = '<p class="text-sm text-gray-500 col-span-full">Tidak ada foto galeri.</p>';
        }

        document.getElementById('portfolio-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closePortfolioModal() {
        document.getElementById('portfolio-modal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function filterPortfolio(category, btnEl) {
        // Update button styles
        const buttons = document.querySelectorAll('.filter-btn');
        buttons.forEach(btn => {
            btn.classList.remove('text-amber-500', 'bg-amber-50');
            btn.classList.add('text-gray-500');
        });
        btnEl.classList.remove('text-gray-500');
        btnEl.classList.add('text-amber-500', 'bg-amber-50');

        // Filter items
        const items = document.querySelectorAll('.portfolio-item');
        items.forEach(item => {
            if (category === 'all' || item.getAttribute('data-category') === category) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }
</script>
</body>
</html>