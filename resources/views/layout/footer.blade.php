<footer class="bg-[#0A0A0A] text-gray-300 font-sans border-t border-white/5 relative overflow-hidden">
    <!-- Subtle luxury glow -->
    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-full max-w-lg h-[200px] bg-[#C5A880] opacity-[0.02] blur-[100px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-6 md:px-12 py-20 relative z-10">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-16">

            <!-- Company -->
            <div class="lg:col-span-1">
                <h3 class="text-lg font-light tracking-[0.25em] uppercase text-white mb-6 flex items-center gap-3">
                    Indo Berkah Konstruksi
                </h3>
                <p class="text-gray-500 font-light leading-loose text-sm">
                    {{ $settings['footer_company_text'] ?? 'Menghadirkan mahakarya arsitektur dan konstruksi dengan standar kualitas premium, presisi, dan dedikasi penuh.' }}
                </p>
            </div>

            <!-- Navigasi -->
            <div>
                <h4 class="text-[10px] font-medium tracking-[0.25em] uppercase text-[#C5A880] mb-8">
                    {{ $settings['footer_eksplorasi_label'] ?? 'Eksplorasi' }}
                </h4>
                <ul class="space-y-4 text-sm font-light text-gray-400">
                    <li><a href="/" class="hover:text-[#C5A880] transition-colors duration-300 flex items-center gap-2"><span class="w-2 h-[1px] bg-[#C5A880]/50"></span> Beranda</a></li>
                    <li><a href="/tentang-kami" class="hover:text-[#C5A880] transition-colors duration-300 flex items-center gap-2"><span class="w-2 h-[1px] bg-[#C5A880]/50"></span> Tentang Kami</a></li>
                    <li><a href="{{ url('/') }}#program" class="hover:text-[#C5A880] transition-colors duration-300 flex items-center gap-2"><span class="w-2 h-[1px] bg-[#C5A880]/50"></span> Layanan Eksklusif</a></li>
                    <li><a href="/portofolio" class="hover:text-[#C5A880] transition-colors duration-300 flex items-center gap-2"><span class="w-2 h-[1px] bg-[#C5A880]/50"></span> Koleksi Portofolio</a></li>
                    <li><a href="{{ route('video') }}" class="hover:text-[#C5A880] transition-colors duration-300 flex items-center gap-2"><span class="w-2 h-[1px] bg-[#C5A880]/50"></span> Galeri Video</a></li>
                </ul>
            </div>

            <!-- Layanan -->
            <div>
                <h4 class="text-[10px] font-medium tracking-[0.25em] uppercase text-[#C5A880] mb-8">
                    {{ $settings['footer_keahlian_label'] ?? 'Keahlian Kami' }}
                </h4>
                <ul class="space-y-4 text-sm font-light text-gray-400">
                    <li class="hover:text-white transition-colors cursor-default">{{ $settings['footer_keahlian_1'] ?? 'Hunian Mewah' }}</li>
                    <li class="hover:text-white transition-colors cursor-default">{{ $settings['footer_keahlian_2'] ?? 'Komersial Premium' }}</li>
                    <li class="hover:text-white transition-colors cursor-default">{{ $settings['footer_keahlian_3'] ?? 'Desain Interior' }}</li>
                    <li class="hover:text-white transition-colors cursor-default">{{ $settings['footer_keahlian_4'] ?? 'Manajemen Konstruksi' }}</li>
                </ul>
            </div>

            <!-- Kontak -->
            <div>
                <h4 class="text-[10px] font-medium tracking-[0.25em] uppercase text-[#C5A880] mb-8">
                    {{ $settings['footer_kontak_label'] ?? 'Konsultasi' }}
                </h4>
                <div class="space-y-5 text-sm font-light text-gray-400">
                    <p class="hover:text-white transition-colors"><span class="block text-[10px] tracking-[0.2em] text-gray-600 mb-1 uppercase">Telepon</span> {{ $settings['footer_telepon'] ?? '+62 878 6530 9966' }}</p>
                    <p class="hover:text-white transition-colors"><span class="block text-[10px] tracking-[0.2em] text-gray-600 mb-1 uppercase">Email</span> {{ $settings['footer_email'] ?? 'partners@indoberkahkonstruksi.com' }}</p>
                    
                    <div class="flex gap-5 pt-4">
                        @if(!empty($settings['footer_facebook']))
                        <a href="{{ $settings['footer_facebook'] }}" target="_blank" class="text-gray-500 hover:text-[#C5A880] transition-colors duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                        </a>
                        @endif
                        @if(!empty($settings['footer_instagram']))
                        <a href="{{ $settings['footer_instagram'] }}" target="_blank" class="text-gray-500 hover:text-[#C5A880] transition-colors duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                        </a>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <!-- Bottom bar -->
        <div class="border-t border-white/5 mt-20 pt-8 flex flex-col md:flex-row justify-between items-center gap-6 text-center md:text-left">
            <p class="text-gray-600 text-[11px] tracking-wider uppercase">
                &copy; {{ date('Y') }} Indo Berkah Konstruksi. Hak Cipta Dilindungi.
            </p>
            <p class="text-[#C5A880] text-[10px] tracking-[0.3em] uppercase">
                {{ $settings['footer_tagline'] ?? 'Menjaga Kualitas Mewujud Berkah' }}
            </p>
        </div>

    </div>
</footer>