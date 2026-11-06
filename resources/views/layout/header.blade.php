@php
    $isHome = request()->is('/');
    $navBg = $isHome ? 'bg-transparent' : 'bg-[#111111] shadow-lg';
    $navPadding = $isHome ? 'py-8' : 'py-5';
@endphp
<nav id="mainNavbar" class="fixed top-0 left-0 w-full px-6 md:px-12 {{ $navPadding }} flex items-center justify-between z-50 {{ $navBg }} transition-all duration-300">

    <!-- Logo -->
    <div class="flex items-center gap-4 shrink-0">
        <a href="/" class="w-10 h-10 rounded-full bg-cover bg-center block border border-[#C5A880]/30 hover:border-[#C5A880] transition-colors duration-500"
        style="background-image: url('{{ asset('image/Logo/Logo PT. Indo Berkah.png') }}')">
        </a>
    </div>

    <!-- Menu desktop -->
    <ul id="menu" class="hidden md:flex items-center gap-8 text-[11px] lg:text-xs font-medium tracking-[0.15em] uppercase text-gray-100">
        <li>
            <a href="{{  route('tentang-kami') }}" class="hover:text-[#C5A880] transition-colors duration-300">tentang kami</a>
        </li>
        <li>
            <a href="{{  url('our-team') }}" class="hover:text-[#C5A880] transition-colors duration-300">Our Team</a>
        </li>
        <li>
            <a href="{{ url('/') }}#program" class="hover:text-[#C5A880] transition-colors duration-300">Layanan</a>
        </li>
        <li>
            <a href="{{ route('portofolio') }}" class="hover:text-[#C5A880] transition-colors duration-300">Portofolio</a>
        </li>
        <li>
            <a href="/penawaran" class="hover:text-[#C5A880] transition-colors duration-300">Ajukan Kolaborasi</a>
        </li>
        <li>
            <a href="{{ route('partner-client') }}" class="hover:text-[#C5A880] transition-colors duration-300">Partner</a>
        </li>
        
        <li>
            <a href="{{ route('berita.index') }}" class="hover:text-[#C5A880] transition-colors duration-300">News & Event</a>
        </li>
    </ul>

 

    <!-- Action (Desktop) -->
    <div class="hidden md:flex items-center gap-4 shrink-0">
        @auth
            @if(Auth::user()->role === 'admin')
                <a href="/admin" class="text-xs font-medium tracking-[0.15em] uppercase border border-[#C5A880]/50 text-[#C5A880] px-4 py-2 hover:bg-[#C5A880] hover:text-white transition-all duration-300">
                    Admin Panel
                </a>
            @else
                <a href="/dashbord" class="text-xs font-medium tracking-[0.15em] uppercase border border-[#C5A880]/50 text-[#C5A880] px-4 py-2 hover:bg-[#C5A880] hover:text-white transition-all duration-300">
                    Dashboard
                </a>
            @endif
            <form action="/logout" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-xs font-medium tracking-[0.15em] uppercase text-gray-400 hover:text-red-400 transition-colors duration-300">
                    Keluar
                </button>
            </form>
        @else
            <a href="/login" class="text-xs font-medium tracking-[0.15em] uppercase text-gray-400 hover:text-[#C5A880] transition-colors duration-300">Masuk</a>
            <a href="/register" class="text-xs font-medium tracking-[0.15em] uppercase border border-[#C5A880]/50 text-[#C5A880] px-6 py-2.5 hover:bg-[#C5A880] hover:text-white transition-all duration-500">Daftar</a>
        @endauth
    </div>

    <!-- Mobile Menu Button -->
    <button id="mobileMenuBtn" class="w-8 h-8 flex flex-col justify-center items-end gap-1.5 md:hidden text-[#C5A880]">
        <span class="block w-6 h-[1px] bg-[#C5A880] transition-all duration-300" id="bar1"></span>
        <span class="block w-4 h-[1px] bg-[#C5A880] transition-all duration-300" id="bar2"></span>
        <span class="block w-6 h-[1px] bg-[#C5A880] transition-all duration-300" id="bar3"></span>
    </button>
</nav>



<!-- Mobile Dropdown Menu -->
<div id="mobileMenu"
    class="fixed top-[72px] left-0 w-full bg-[#111111]/95 backdrop-blur-xl border-b border-white/5 z-40 overflow-hidden max-h-0 transition-all duration-500 ease-in-out md:hidden">
    <ul class="flex flex-col text-xs font-light tracking-[0.2em] uppercase text-gray-300 px-8 py-8 gap-6">
        <li><a href="{{ route('tentang-kami') }}" class="block hover:text-[#C5A880] transition-colors">Tentang Kami</a></li>
        <li><a href="{{ url('our-team') }}" class="block hover:text-[#C5A880] transition-colors">Our Team</a></li>
        <li><a href="{{ url('/') }}#program" class="block hover:text-[#C5A880] transition-colors">Layanan</a></li>
        <li><a href="{{ route('portofolio') }}" class="block hover:text-[#C5A880] transition-colors">Portofolio</a></li>
        <li><a href="/penawaran" class="block hover:text-[#C5A880] transition-colors">Ajukan Kolaborasi</a></li>
        <li><a href="{{ route('partner-client') }}" class="block hover:text-[#C5A880] transition-colors">Partner</a></li>
        <li><a href="{{ route('berita.index') }}" class="block hover:text-[#C5A880] transition-colors">News & Event</a></li>
        
        <li class="flex flex-col gap-4 pt-6 border-t border-white/10 mt-2">
            @auth
                @if(Auth::user()->role === 'admin')
                    <a href="/admin" class="text-center py-3.5 bg-[#C5A880] text-[#111] font-medium hover:bg-[#A68A60] transition-colors tracking-widest">Admin Panel</a>
                @else
                    <a href="/dashbord" class="text-center py-3.5 bg-[#C5A880] text-[#111] font-medium hover:bg-[#A68A60] transition-colors tracking-widest">Dashboard Saya</a>
                @endif
                <form action="/logout" method="POST" class="w-full">
                    @csrf
                    <button type="submit" class="w-full text-center py-3.5 border border-red-500/40 text-red-400 hover:bg-red-500/10 transition-colors tracking-widest uppercase text-xs">Keluar / Logout</button>
                </form>
            @else
                <a href="/login" class="text-center py-3.5 border border-gray-700 hover:border-[#C5A880] hover:text-[#C5A880] transition-colors tracking-widest">Masuk</a>
                <a href="/register" class="text-center py-3.5 bg-[#C5A880] text-[#111] font-medium hover:bg-[#A68A60] transition-colors tracking-widest">Daftar</a>
            @endauth
        </li>
    </ul>
</div>

<script>
    const btn = document.getElementById('mobileMenuBtn');
    const menu = document.getElementById('mobileMenu');
    const bar1 = document.getElementById('bar1');
    const bar2 = document.getElementById('bar2');
    const bar3 = document.getElementById('bar3');
    let open = false;

    btn.addEventListener('click', () => {
        open = !open;
        menu.style.maxHeight = open ? menu.scrollHeight + 'px' : '0';
        
        // Luxury hamburger animation (transform into sleek X)
        bar1.style.transform = open ? 'rotate(45deg) translate(5px, 4.5px)' : '';
        bar2.style.opacity = open ? '0' : '1';
        bar3.style.transform = open ? 'rotate(-45deg) translate(5px, -4.5px)' : '';
        bar2.style.width = open ? '24px' : '16px';
    });

    const mainNavbar = document.getElementById('mainNavbar');
    const isHomePage = {{ request()->is('/') ? 'true' : 'false' }};

    if (isHomePage) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                mainNavbar.classList.remove('bg-transparent', 'py-8');
                mainNavbar.classList.add('bg-[#111111]', 'py-5', 'shadow-lg');
            } else {
                mainNavbar.classList.add('bg-transparent', 'py-8');
                mainNavbar.classList.remove('bg-[#111111]', 'py-5', 'shadow-lg');
            }
        });
    }
</script>

<!-- Hidden Google Translate Element -->
<div id="google_translate_element" style="position:absolute;left:-9999px;visibility:hidden;"></div>

<!-- Custom Floating Language Switcher -->
<div id="lang-switcher" style="position:fixed;bottom:24px;right:24px;z-index:9999;">
    <button onclick="toggleLangMenu()" title="Ganti Bahasa"
        style="width:48px;height:48px;border-radius:50%;background:#111;border:1px solid rgba(197,168,128,0.4);color:#C5A880;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 4px 20px rgba(0,0,0,0.4);transition:all 0.3s;">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
        </svg>
    </button>
    <div id="lang-menu" style="display:none;position:absolute;bottom:58px;right:0;background:#111;border:1px solid rgba(255,255,255,0.08);border-radius:12px;overflow:hidden;min-width:160px;box-shadow:0 8px 32px rgba(0,0,0,0.5);">
        <div style="padding:8px 0;">
            <button onclick="changeLang('id')" class="lang-btn" style="display:flex;align-items:center;gap:10px;width:100%;padding:10px 16px;background:none;border:none;color:#d1d5db;font-size:12px;font-weight:500;letter-spacing:0.1em;text-transform:uppercase;cursor:pointer;transition:all 0.2s;text-align:left;">🇮🇩 Indonesia</button>
            <button onclick="changeLang('en')" class="lang-btn" style="display:flex;align-items:center;gap:10px;width:100%;padding:10px 16px;background:none;border:none;color:#d1d5db;font-size:12px;font-weight:500;letter-spacing:0.1em;text-transform:uppercase;cursor:pointer;transition:all 0.2s;text-align:left;">🇺🇸 English</button>
            <button onclick="changeLang('zh-CN')" class="lang-btn" style="display:flex;align-items:center;gap:10px;width:100%;padding:10px 16px;background:none;border:none;color:#d1d5db;font-size:12px;font-weight:500;letter-spacing:0.1em;text-transform:uppercase;cursor:pointer;transition:all 0.2s;text-align:left;">🇨🇳 中文</button>
            <button onclick="changeLang('ar')" class="lang-btn" style="display:flex;align-items:center;gap:10px;width:100%;padding:10px 16px;background:none;border:none;color:#d1d5db;font-size:12px;font-weight:500;letter-spacing:0.1em;text-transform:uppercase;cursor:pointer;transition:all 0.2s;text-align:left;">🇸🇦 العربية</button>
            <button onclick="changeLang('ko')" class="lang-btn" style="display:flex;align-items:center;gap:10px;width:100%;padding:10px 16px;background:none;border:none;color:#d1d5db;font-size:12px;font-weight:500;letter-spacing:0.1em;text-transform:uppercase;cursor:pointer;transition:all 0.2s;text-align:left;">🇰🇷 한국어</button>
            <button onclick="changeLang('ja')" class="lang-btn" style="display:flex;align-items:center;gap:10px;width:100%;padding:10px 16px;background:none;border:none;color:#d1d5db;font-size:12px;font-weight:500;letter-spacing:0.1em;text-transform:uppercase;cursor:pointer;transition:all 0.2s;text-align:left;">🇯🇵 日本語</button>
        </div>
    </div>
</div>

<style>
.lang-btn:hover { background: rgba(197,168,128,0.1) !important; color: #C5A880 !important; }
body > .skiptranslate { display: none !important; }
body { top: 0 !important; }
#lang-switcher button:hover { border-color: #C5A880 !important; box-shadow: 0 4px 24px rgba(197,168,128,0.2) !important; }
</style>

<script type="text/javascript">
function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: 'id',
        includedLanguages: 'en,id,zh-CN,ar,ko,ja'
    }, 'google_translate_element');
}

function toggleLangMenu() {
    const menu = document.getElementById('lang-menu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

function changeLang(lang) {
    // Sembunyikan menu dropdown
    document.getElementById('lang-menu').style.display = 'none';

    if (lang === 'id') {
        // Hapus cookie terjemahan untuk kembali ke bahasa asli (Indonesia)
        document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; domain=" + window.location.hostname + "; path=/;";
    } else {
        // Set cookie terjemahan ke bahasa tujuan
        document.cookie = "googtrans=/id/" + lang + "; path=/;";
        document.cookie = "googtrans=/id/" + lang + "; domain=" + window.location.hostname + "; path=/;";
    }
    
    // Refresh halaman agar script Google Translate membaca cookie baru
    window.location.reload();
}

// Tutup dropdown saat klik di luar
document.addEventListener('click', function(e) {
    const switcher = document.getElementById('lang-switcher');
    if (switcher && !switcher.contains(e.target)) {
        document.getElementById('lang-menu').style.display = 'none';
    }
});
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
