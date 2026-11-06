@php
    $isHome = request()->is('/');
    $navBg = $isHome ? 'bg-transparent' : 'bg-[#111111] shadow-lg';
    $navPadding = $isHome ? 'py-8' : 'py-5';
    $languages = [
        'id' => ['flag' => '🇮🇩', 'code' => 'ID', 'name' => 'Indonesia'],
        'en' => ['flag' => '🇺🇸', 'code' => 'EN', 'name' => 'English'],
        'zh-CN' => ['flag' => '🇨🇳', 'code' => 'CN', 'name' => '中文'],
        'ar' => ['flag' => '🇸🇦', 'code' => 'AR', 'name' => 'العربية'],
        'ko' => ['flag' => '🇰🇷', 'code' => 'KO', 'name' => '한국어'],
        'ja' => ['flag' => '🇯🇵', 'code' => 'JA', 'name' => '日本語'],
    ];
@endphp
<nav id="mainNavbar" class="fixed top-0 left-0 w-full px-6 md:px-8 xl:px-12 {{ $navPadding }} flex items-center justify-between gap-4 z-50 {{ $navBg }} transition-all duration-300">
    <!-- Logo -->
    <div class="flex items-center gap-4 shrink-0">
        <a href="/" class="w-10 h-10 rounded-full bg-cover bg-center block border border-[#C5A880]/30 hover:border-[#C5A880] transition-colors duration-500"
        style="background-image: url('{{ asset('image/Logo/Logo PT. Indo Berkah.png') }}')">
        </a>
    </div>

    <!-- Menu desktop -->
    <ul id="menu" class="hidden lg:flex items-center gap-4 xl:gap-6 text-[11px] xl:text-xs font-medium tracking-[0.12em] xl:tracking-[0.15em] uppercase text-gray-100">
        <li><a href="{{ route('tentang-kami') }}" class="hover:text-[#C5A880] transition-colors duration-300">tentang kami</a></li>
        <li><a href="{{ url('our-team') }}" class="hover:text-[#C5A880] transition-colors duration-300">Our Team</a></li>
        <li><a href="{{ url('/') }}#program" class="hover:text-[#C5A880] transition-colors duration-300">Layanan</a></li>
        <li><a href="{{ route('portofolio') }}" class="hover:text-[#C5A880] transition-colors duration-300">Portofolio</a></li>
        <li><a href="/penawaran" class="hover:text-[#C5A880] transition-colors duration-300">Ajukan Kolaborasi</a></li>
        <li><a href="{{ route('partner-client') }}" class="hover:text-[#C5A880] transition-colors duration-300">Partner</a></li>
        <li><a href="{{ route('berita.index') }}" class="hover:text-[#C5A880] transition-colors duration-300">News & Event</a></li>
    </ul>

    <div class="flex items-center gap-3 shrink-0">
        <!-- Action (Desktop) -->
        <div class="hidden lg:flex items-center gap-3 xl:gap-4">
            @auth
                @if(Auth::user()->role === 'admin')
                    <a href="/admin" class="text-xs font-medium tracking-[0.12em] xl:tracking-[0.15em] uppercase border border-[#C5A880]/50 text-[#C5A880] px-3.5 xl:px-4 py-2 hover:bg-[#C5A880] hover:text-white transition-all duration-300">Admin Panel</a>
                @else
                    <a href="/dashbord" class="text-xs font-medium tracking-[0.12em] xl:tracking-[0.15em] uppercase border border-[#C5A880]/50 text-[#C5A880] px-3.5 xl:px-4 py-2 hover:bg-[#C5A880] hover:text-white transition-all duration-300">Dashboard</a>
                @endif
                <form action="/logout" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-xs font-medium tracking-[0.12em] xl:tracking-[0.15em] uppercase text-gray-400 hover:text-red-400 transition-colors duration-300">Keluar</button>
                </form>
            @else
                <a href="/login" class="text-xs font-medium tracking-[0.12em] xl:tracking-[0.15em] uppercase text-gray-400 hover:text-[#C5A880] transition-colors duration-300">Masuk</a>
                <a href="/register" class="text-xs font-medium tracking-[0.12em] xl:tracking-[0.15em] uppercase border border-[#C5A880]/50 text-[#C5A880] px-4 xl:px-6 py-2 xl:py-2.5 hover:bg-[#C5A880] hover:text-white transition-all duration-500">Daftar</a>
            @endauth
        </div>

        <details id="lang-switcher" class="relative notranslate" translate="no">
            <summary aria-label="Pilih bahasa, Indonesia" class="flex min-h-10 items-center gap-2 cursor-pointer list-none border border-[#C5A880]/50 bg-[#111111]/80 px-3 text-xs font-medium text-gray-100 hover:border-[#C5A880] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#C5A880]">
                <span id="activeLangFlag" aria-hidden="true">🇮🇩</span>
                <span id="activeLangCode">ID</span>
                <svg aria-hidden="true" class="w-3 h-3 text-[#C5A880]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </summary>
            <div class="absolute right-0 top-full mt-2 min-w-24 border border-[#C5A880]/30 bg-[#111111] py-1 shadow-lg">
                @foreach($languages as $lang => $language)
                    <button type="button" data-lang="{{ $lang }}" data-name="{{ $language['name'] }}" aria-label="{{ $language['name'] }}" aria-current="{{ $lang === 'id' ? 'true' : 'false' }}" class="flex min-h-10 w-full items-center gap-2 px-4 text-left text-xs text-gray-100 hover:bg-[#C5A880]/10 hover:text-[#C5A880] focus-visible:outline focus-visible:outline-2 focus-visible:outline-inset focus-visible:outline-[#C5A880]">
                        <span aria-hidden="true">{{ $language['flag'] }}</span>
                        <span>{{ $language['code'] }}</span>
                    </button>
                @endforeach
            </div>
        </details>

        <!-- Mobile Menu Button -->
        <button type="button" id="mobileMenuBtn" aria-label="Buka menu" aria-controls="mobileMenu" aria-expanded="false" class="w-10 h-10 flex flex-col justify-center items-end gap-1.5 lg:hidden text-[#C5A880] focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#C5A880]">
            <span class="block w-6 h-[1px] bg-[#C5A880] transition-all duration-300" id="bar1"></span>
            <span class="block w-4 h-[1px] bg-[#C5A880] transition-all duration-300" id="bar2"></span>
            <span class="block w-6 h-[1px] bg-[#C5A880] transition-all duration-300" id="bar3"></span>
        </button>
    </div>

    <!-- Mobile Dropdown Menu -->
    <div id="mobileMenu" hidden class="absolute top-full left-0 w-full max-h-[calc(100dvh-6.5rem)] overflow-y-auto bg-[#111111]/95 backdrop-blur-xl border-b border-white/5 lg:hidden">
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
</nav>

<script>
    const btn = document.getElementById('mobileMenuBtn');
    const menu = document.getElementById('mobileMenu');
    const bar1 = document.getElementById('bar1');
    const bar2 = document.getElementById('bar2');
    const bar3 = document.getElementById('bar3');

    btn.addEventListener('click', () => {
        const open = menu.hidden;
        menu.hidden = !open;
        btn.setAttribute('aria-expanded', String(open));
        btn.setAttribute('aria-label', open ? 'Tutup menu' : 'Buka menu');
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

<style>
#lang-switcher summary::-webkit-details-marker { display: none; }
#lang-switcher [aria-current="true"] { background: rgba(197,168,128,0.15); color: #C5A880; }
body > .skiptranslate { display: none !important; }
body { top: 0 !important; }
</style>

<script>
function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: 'id',
        includedLanguages: 'en,id,zh-CN,ar,ko,ja'
    }, 'google_translate_element');
}

const langSwitcher = document.getElementById('lang-switcher');
const langOptions = Array.from(langSwitcher.querySelectorAll('[data-lang]'));
const langCookie = document.cookie.split('; ').find(cookie => cookie.startsWith('googtrans='));
let activeLang = 'id';
try {
    activeLang = decodeURIComponent(langCookie?.slice('googtrans='.length) || '').split('/').pop() || 'id';
} catch (_) {
    activeLang = 'id';
}
const activeOption = langOptions.find(option => option.dataset.lang === activeLang) || langOptions[0];
document.getElementById('activeLangFlag').textContent = activeOption.children[0].textContent;
document.getElementById('activeLangCode').textContent = activeOption.children[1].textContent;
langSwitcher.querySelector('summary').setAttribute('aria-label', 'Pilih bahasa, ' + activeOption.dataset.name);
langOptions.forEach(option => option.setAttribute('aria-current', String(option === activeOption)));

langSwitcher.addEventListener('click', event => {
    const option = event.target.closest('[data-lang]');
    if (!option) return;

    const lang = option.dataset.lang;
    if (lang === 'id') {
        document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; domain=' + window.location.hostname + '; path=/;';
    } else {
        document.cookie = 'googtrans=/id/' + lang + '; path=/;';
        document.cookie = 'googtrans=/id/' + lang + '; domain=' + window.location.hostname + '; path=/;';
    }
    window.location.reload();
});

document.addEventListener('click', event => {
    if (!langSwitcher.contains(event.target)) langSwitcher.open = false;
});
document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && langSwitcher.open) {
        langSwitcher.open = false;
        langSwitcher.querySelector('summary').focus();
    }
});
</script>
<script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
