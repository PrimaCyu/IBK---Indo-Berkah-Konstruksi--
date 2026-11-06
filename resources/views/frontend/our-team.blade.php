<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Our Team - Indo Berkah</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-white text-gray-900 font-sans antialiased">
    @include('layout.header')
    
    <main class="pt-32 pb-24 md:pt-40 md:pb-32 px-6 md:px-12 min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="text-center mb-20 md:mb-28">
                <p class="uppercase tracking-[0.3em] text-[#C5A880] text-xs md:text-sm mb-4 font-medium" data-aos="fade-down">
                    Orang-Orang Hebat di Balik Karya Kami
                </p>
                <h1 class="text-4xl md:text-6xl font-light text-gray-900 mb-6 tracking-wide" data-aos="fade-up" data-aos-delay="100">
                    Meet Our <span class="font-bold text-[#C5A880]">Team</span>
                </h1>
                <div class="w-16 h-[1px] bg-[#C5A880] mx-auto mb-8"></div>
                <p class="max-w-2xl mx-auto text-gray-500 leading-relaxed font-light text-sm md:text-base" data-aos="fade-up" data-aos-delay="200">
                    Profesional berdedikasi yang menyatukan keahlian, inovasi, dan integritas demi hasil konstruksi terbaik.
                </p>
                <div class="mt-6" data-aos="fade-up" data-aos-delay="300">
                    <p class="text-gray-400 text-xs md:text-sm mb-3 font-light">Ingin menjadi bagian dari tim kami?</p>
                    <a href="{{ route('join-us') }}" class="inline-flex items-center gap-1 text-[#C5A880] hover:text-gray-800 text-sm font-medium tracking-widest uppercase transition-colors duration-300">
                        Join Us
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
            </div>

            <!-- Team Grid -->
            @if($teamMembers->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6 md:gap-8">
                    @foreach($teamMembers as $member)
                        <div class="group relative overflow-hidden bg-white shadow-lg hover:shadow-xl border border-gray-100 transition-all duration-500 rounded-lg flex flex-col h-full cursor-pointer" onclick="openTeamModal({{ $member->id }})" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 50 }}">
                            
                            <!-- Image Container -->
                            <div class="relative w-full pt-[100%] overflow-hidden bg-gray-100">
                                @if($member->foto)
                                    <img src="{{ asset('storage/' . $member->foto) }}" alt="{{ $member->nama }}" class="absolute top-0 left-0 w-full h-full object-cover object-top filter grayscale group-hover:grayscale-0 transition-all duration-700 transform group-hover:scale-105">
                                @else
                                    <div class="absolute top-0 left-0 w-full h-full flex items-center justify-center text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Info Container -->
                            <div class="p-5 flex flex-col flex-grow relative z-10 bg-white text-center">
                                <h3 class="text-base md:text-lg font-medium text-gray-800 mb-1 group-hover:text-[#C5A880] transition-colors duration-300">
                                    {{ $member->nama_pendek ?: $member->nama }}
                                </h3>
                                @if($member->posisi)
                                <p class="text-xs text-gray-500 mb-2 font-medium">{{ $member->posisi }}</p>
                                @endif
                                
                                <p class="text-[10px] text-[#C5A880] uppercase tracking-wider opacity-70 group-hover:opacity-100 transition-opacity">
                                    Lihat Profil
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-20 border border-gray-200 bg-white shadow-sm rounded-lg">
                    <p class="text-gray-500 font-light tracking-widest uppercase">Belum ada data anggota tim</p>
                </div>
            @endif
        </div>
    </main>

    @include('layout.footer')
    
    <!-- Team Modal -->
    <div id="team-modal" class="fixed inset-0 z-[100] hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
        <div class="bg-white rounded-3xl w-full max-w-4xl min-h-[70vh] md:min-h-[600px] max-h-[90vh] overflow-hidden flex flex-col md:flex-row shadow-2xl relative">
            <button onclick="closeTeamModal()" class="absolute top-4 right-4 z-10 w-10 h-10 bg-black/50 text-white rounded-full flex items-center justify-center hover:bg-black transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            
            <!-- Photo Side -->
            <div class="w-full md:w-1/2 bg-gray-100 relative h-80 md:h-auto flex-shrink-0">
                <img id="modal-foto" src="" alt="Foto" class="absolute inset-0 w-full h-full object-cover object-top">
                <div id="modal-foto-placeholder" class="absolute inset-0 w-full h-full flex items-center justify-center text-gray-400 bg-gray-100 hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
            
            <!-- Content Side -->
            <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col overflow-y-auto">
                <span id="modal-posisi" class="block text-[10px] tracking-[0.2em] font-bold text-[#C5A880] uppercase mb-2">Profil Anggota</span>
                <h2 id="modal-nama" class="text-3xl md:text-4xl font-bold text-gray-900 mb-6"></h2>
                
                <div class="w-12 h-[2px] bg-[#C5A880] mb-6"></div>
                
                <div id="modal-profil" class="text-gray-600 font-light leading-relaxed text-base md:text-lg whitespace-pre-wrap">
                </div>
            </div>
        </div>
    </div>
    
    <!-- AOS Animation script and Modal script -->
    <script>
        const teamMembers = @json($teamMembers);

        document.addEventListener('DOMContentLoaded', () => {
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 1000,
                    once: true,
                    offset: 50,
                });
            }
        });

        function openTeamModal(id) {
            const member = teamMembers.find(m => m.id === id);
            if (!member) return;

            document.getElementById('modal-nama').innerText = member.nama;
            document.getElementById('modal-posisi').innerText = member.posisi || 'Profil Anggota';
            document.getElementById('modal-profil').innerHTML = member.profil_singkat || 'Profil belum ditambahkan.';

            const imgEl = document.getElementById('modal-foto');
            const placeholderEl = document.getElementById('modal-foto-placeholder');

            if (member.foto) {
                imgEl.src = '/storage/' + member.foto;
                imgEl.classList.remove('hidden');
                placeholderEl.classList.add('hidden');
            } else {
                imgEl.src = '';
                imgEl.classList.add('hidden');
                placeholderEl.classList.remove('hidden');
            }

            document.getElementById('team-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        function closeTeamModal() {
            document.getElementById('team-modal').classList.add('hidden');
            document.body.style.overflow = '';
        }
    </script>
</body>
</html>
