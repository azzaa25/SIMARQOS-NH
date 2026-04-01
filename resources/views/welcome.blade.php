<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Masjid Nurul Huda - Arisan Qurban & Sosial</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script type="text/javascript"
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.clientKey') }}"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; color: #1e293b; scroll-behavior: smooth; }
        .glass-nav { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(15px); border-bottom: 1px solid rgba(255, 255, 255, 0.3); }
        .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.5); }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        .animate-float { animation: float 5s ease-in-out infinite; }
        .blob { position: absolute; z-index: -1; filter: blur(80px); opacity: 0.4; }
        .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
</head>

<body>

    <div class="blob w-[500px] h-[500px] bg-green-200 top-[-100px] left-[-100px] animate-pulse"></div>
    <div class="blob w-[400px] h-[400px] bg-blue-100 bottom-0 right-0"></div>

    <nav class="glass-nav sticky top-0 z-50 px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-[#147a54] rounded-xl flex items-center justify-center shadow-lg shadow-green-900/20">
                    <svg viewBox="0 0 24 24" class="w-6 h-6 text-white fill-current">
                        <path d="M12 3a9 9 0 109 9c0-.46-.04-.92-.1-1.36a5.38 5.38 0 01-4.4 2.26 5.4 5.4 0 01-5.4-5.4c0-1.8.88-3.4 2.24-4.4-.44-.06-.9-.1-1.34-.1z"/>
                    </svg>
                </div>
                <div>
                    <span class="block text-sm font-black text-gray-900 leading-none">MASJID NURUL HUDA</span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Digital Platform</span>
                </div>
            </div>
            
            <div class="hidden md:flex items-center gap-8 text-xs font-bold uppercase tracking-widest text-gray-500">
                <a href="#hero" class="hover:text-[#147a54] transition-colors">Beranda</a>
                <a href="#fitur" class="hover:text-[#147a54] transition-colors">Fitur</a>
                <a href="#sosial" class="hover:text-[#147a54] transition-colors">Kegiatan Sosial</a>
                <a href="#masjid" class="hover:text-[#147a54] transition-colors">Informasi Masjid</a>
                <a href="{{ route('login') }}" class="px-6 py-3 bg-[#147a54] text-white rounded-full shadow-lg shadow-green-900/20 hover:bg-[#0d5c3f] transition-all">Masuk</a>
            </div>
        </div>
    </nav>

    <section id="hero" class="relative px-6 py-20 overflow-hidden">
        <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-16 items-center">
            <div class="space-y-8">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-green-100 text-[#147a54] rounded-full text-[10px] font-black uppercase tracking-[0.2em]">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-600"></span>
                    </span>
                    Tersedia Untuk Jamaah
                </div>
                
                {{-- Perbaikan Judul --}}
                <h1 class="text-5xl md:text-6xl font-black text-slate-900 leading-[1.1] tracking-tight">
                    Ibadah <span class="text-[#147a54]">Qurban</span> & Aksi <span class="text-[#147a54]">Sosial</span> Jadi Lebih Mudah.
                </h1>
                
                {{-- Perbaikan Deskripsi --}}
                <p class="text-slate-500 text-lg leading-relaxed max-w-lg">
                    Platform digital amanah Masjid Nurul Huda untuk pengelolaan Arisan Qurban yang terencana serta penyaluran bantuan sosial jamaah secara transparan dan otomatis.
                </p>
                
                <div class="flex items-center gap-4">
                    <a href="{{ route('register') }}" class="px-10 py-5 bg-[#147a54] text-white font-black rounded-2xl shadow-xl shadow-green-900/20 hover:bg-[#0d5c3f] transition-all hover:-translate-y-1">Mulai Daftar</a>
                    <a href="#sosial" class="px-10 py-5 bg-white text-slate-600 font-black rounded-2xl border border-slate-100 shadow-sm hover:bg-slate-50 transition-all">Kegiatan Sosial</a>
                </div>
            </div>
            
            <div class="relative hidden md:block">
                <div class="glass-card p-4 rounded-[40px] shadow-2xl animate-float">
                    {{-- Perbaikan Link Gambar: Menggunakan Unsplash Mosque yang lebih stabil --}}
                    <img src="https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?auto=format&fit=crop&q=80&w=800" 
                        alt="Masjid Nurul Huda" 
                        class="rounded-[30px] w-full object-cover aspect-square shadow-inner">
                </div>
                
                {{-- Badge Statistik dengan Data Dinamis --}}
                <div class="absolute -bottom-10 -left-10 glass-card p-6 rounded-3xl shadow-xl border-l-4 border-l-[#147a54]">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Peserta Arisan</p>
                    <p class="text-3xl font-black text-slate-900">{{ $totalPeserta ?? 0 }}</p>
                    <p class="text-[10px] font-bold text-green-600 italic">Terverifikasi Aktif</p>
                </div>
            </div>
        </div>
    </section>

    <section id="fitur" class="py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h2 class="text-xs font-black text-[#147a54] uppercase tracking-[0.4em] mb-4">Kenapa Menggunakan Platform Kami?</h2>
            <p class="text-3xl font-black text-slate-900 mb-16 italic">"Kemudahan Ibadah dalam Genggaman"</p>
            
            <div class="grid md:grid-cols-3 gap-8 text-left">
                <div class="glass-card p-10 rounded-[40px] hover:shadow-2xl transition-all group">
                    <div class="w-14 h-14 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 mb-4">Transparansi Dana</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">Pantau setiap rupiah tabungan arisan Anda secara real-time melalui dashboard pribadi.</p>
                </div>

                <div class="glass-card p-10 rounded-[40px] hover:shadow-2xl transition-all group">
                    <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 mb-4">Pengingat Otomatis</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">Sistem akan memberikan pengingat waktu pembayaran agar tabungan Qurban Anda tidak terhambat.</p>
                </div>

                <div class="glass-card p-10 rounded-[40px] hover:shadow-2xl transition-all group">
                    <div class="w-14 h-14 bg-green-100 text-[#147a54] rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 mb-4">Kegiatan Sosial</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">Ikuti perkembangan berbagai kegiatan sosial dan penyaluran qurban di wilayah Masjid Nurul Huda.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="sosial" class="py-24 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
                <div class="max-w-xl">
                    <h2 class="text-xs font-black text-[#147a54] uppercase tracking-[0.4em] mb-4">Aksi Nyata Kami</h2>
                    <p class="text-4xl font-black text-slate-900 leading-tight">Bantu Sesama Melalui <br>Kegiatan Sosial Masjid</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Dana Aktif Terkumpul</p>
                    <p class="text-4xl font-black text-[#147a54]">Rp {{ number_format($totalDonasi ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                @forelse($kegiatan as $item)
                <div class="glass-card rounded-[40px] overflow-hidden flex flex-col hover:shadow-2xl transition-all border-b-4 border-b-[#147a54]">
                    <div class="relative h-48 overflow-hidden">
                        <img src="{{ $item->pamflet_kegiatan ? asset('storage/'.$item->pamflet_kegiatan) : 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?auto=format&fit=crop&q=80&w=800' }}" 
                             alt="{{ $item->nama_kegiatan }}" class="w-full h-full object-cover">
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 bg-white/90 backdrop-blur-md text-[#147a54] text-[10px] font-black rounded-full uppercase tracking-wider shadow-sm">
                                {{ $item->kategori->nama_kategori ?? 'Umum' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-8 flex-1 flex flex-col">
                        <h3 class="text-xl font-black text-slate-900 mb-3">{{ $item->nama_kegiatan }}</h3>
                        <p class="text-sm text-slate-500 leading-relaxed mb-6 line-clamp-3">
                            {{ $item->deskripsi_kegiatan }}
                        </p>
                        
                        <div class="mt-auto space-y-3">
                            @php
                                // PERBAIKAN: Hanya hitung tipe_dana 'masuk'
                                $danaMasuk = \App\Models\DanaSosial::where('id_kegiatan', $item->id_kegiatan)
                                            ->where('tipe_dana', 'masuk')
                                            ->whereIn('status_pembayaran', ['success', 'settlement'])
                                            ->sum('nominal');
                                $persentase = $item->target_donasi > 0 ? ($danaMasuk / $item->target_donasi) * 100 : 0;
                                if($persentase > 100) $persentase = 100;
                            @endphp

                            <div class="flex justify-between text-[10px] font-black uppercase tracking-widest text-gray-400">
                                <span>Terkumpul</span>
                                <span>{{ round($persentase) }}%</span>
                            </div>
                            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-[#147a54] rounded-full" style="width: {{ $persentase }}%"></div>
                            </div>
                            <div class="flex justify-between items-center pt-4">
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase">Target</p>
                                    <p class="text-sm font-black text-slate-900">Rp {{ number_format($item->target_donasi, 0, ',', '.') }}</p>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('sosial.detail', $item->id_kegiatan) }}" 
                                    class="flex-1 px-4 py-2.5 bg-slate-100 text-slate-600 text-[10px] font-black rounded-xl hover:bg-slate-200 transition-colors uppercase tracking-widest text-center whitespace-nowrap">
                                        Detail
                                    </a>

                                    <button onclick="openDonasiModal('{{ $item->id_kegiatan }}', '{{ $item->nama_kegiatan }}')" 
                                            class="flex-1 px-4 py-2.5 bg-[#147a54] text-white text-[10px] font-black rounded-xl hover:bg-[#0d5c3f] transition-colors uppercase tracking-widest">
                                        Donasi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-3 py-20 text-center glass-card rounded-[40px]">
                    <p class="text-slate-400 font-bold italic tracking-widest">Belum ada kegiatan sosial aktif saat ini.</p>
                </div>
                @endforelse
            </div>

            <div class="mt-16 text-center">
                <a href="{{ route('sosial.semua') }}" class="inline-flex items-center gap-3 text-sm font-black text-[#147a54] hover:gap-5 transition-all">
                    LIHAT SEMUA AGENDA SOSIAL 
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    <section id="masjid" class="py-24 px-6 overflow-hidden">
        <div class="max-w-7xl mx-auto glass-card rounded-[50px] overflow-hidden grid md:grid-cols-2 shadow-2xl">
            <div class="p-12 md:p-20 space-y-8">
                <h2 class="text-3xl font-black text-slate-900">Mengenal Masjid <br><span class="text-[#147a54]">Nurul Huda</span></h2>
                <div class="space-y-6">
                    <a href="https://www.google.com/maps/search/?api=1&query=Masjid+Nurul+Huda+Desa+Titik+Kediri" 
                    target="_blank" 
                    class="flex items-start gap-4 group cursor-pointer">
                        <div class="w-10 h-10 shrink-0 bg-slate-100 rounded-lg flex items-center justify-center text-[#147a54] group-hover:bg-[#147a54] group-hover:text-white transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-600 italic group-hover:text-[#147a54] transition-colors">Desa Titik, Kediri, Jawa Timur</p>
                            <span class="text-[10px] font-bold text-gray-400 not-italic uppercase tracking-widest group-hover:text-slate-500 transition-colors">Klik untuk Petunjuk Arah</span>
                        </div>
                    </a>
                    
                    <div class="grid grid-cols-1 gap-4">
                        <div class="p-4 bg-slate-50 rounded-2xl overflow-hidden">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                                <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                                Jadwal Rutinan & Kajian
                            </p>
                            <div class="relative flex overflow-hidden bg-white/50 py-2 rounded-xl border border-green-50">
                                <div class="absolute inset-y-0 left-0 w-8 bg-gradient-to-r from-[#f8fafc] to-transparent z-10"></div>
                                <div class="absolute inset-y-0 right-0 w-8 bg-gradient-to-l from-[#f8fafc] to-transparent z-10"></div>

                                <div class="animate-marquee whitespace-nowrap flex items-center">
                                    <span class="text-[11px] font-bold text-slate-700 tracking-wide uppercase">
                                        <span class="text-[#147a54] bg-green-50 px-2 py-1 rounded">Kajian Bapak-bapak:</span> Selasa Malam Rabu 
                                        <span class="mx-6 text-slate-300">|</span>
                                        <span class="text-[#147a54] bg-green-50 px-2 py-1 rounded">Kajian Ibu-ibu:</span> Rabu Malam Kamis 
                                        <span class="mx-6 text-slate-300">|</span>
                                        <span class="text-[#147a54] bg-green-50 px-2 py-1 rounded">Rutinan Diba'an Laki-laki:</span> Hari Kamis 
                                        <span class="mx-6 text-slate-300">|</span>
                                        <span class="text-[#147a54] bg-green-50 px-2 py-1 rounded">Rutinan Diba'an Perempuan:</span> Hari Jumat
                                    </span>
                                    
                                    <span class="text-[11px] font-bold text-slate-700 tracking-wide uppercase ml-6">
                                        <span class="text-[#147a54] bg-green-50 px-2 py-1 rounded">Kajian Bapak-bapak:</span> Selasa Malam Rabu 
                                        <span class="mx-6 text-slate-300">|</span>
                                        <span class="text-[#147a54] bg-green-50 px-2 py-1 rounded">Kajian Ibu-ibu:</span> Rabu Malam Kamis 
                                        <span class="mx-6 text-slate-300">|</span>
                                        <span class="text-[#147a54] bg-green-50 px-2 py-1 rounded">Rutinan Diba'an Laki-laki:</span> Kamis Malam Jumat 
                                        <span class="mx-6 text-slate-300">|</span>
                                        <span class="text-[#147a54] bg-green-50 px-2 py-1 rounded">Rutinan Diba'an Perempuan:</span> Jumat Malam Sabtu
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 bg-slate-50 rounded-2xl">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Kapasitas Utama</p>
                            <p class="text-xs font-bold text-slate-700">500+ Jamaah Terakomodasi</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-[#147a54] p-12 md:p-20 flex flex-col justify-center items-center text-center text-white space-y-8 relative overflow-hidden">
                {{-- Ikon Variatif (Z-index 10 agar di atas pattern) --}}
                <div class="flex items-center gap-4 z-10">
                    <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center animate-pulse shadow-2xl">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                </div>

                <div class="z-10 space-y-4 max-w-lg">
                    <h3 class="text-3xl md:text-4xl font-black leading-tight tracking-tight">
                        Sempurnakan <span class="text-green-200">Qurban</span>, <br> 
                        Perluas <span class="text-green-200">Kepedulian Sosial</span>.
                    </h3>
                    <p class="text-lg font-medium italic opacity-90">
                        "Menabung amanah untuk ibadah qurban yang terencana, <br class="hidden md:block"> menebar manfaat nyata bagi sesama melalui aksi sosial."
                    </p>
                </div>
                
                <div class="absolute bottom-0 left-0 w-full z-0 pointer-events-none opacity-[0.15] flex items-end overflow-hidden">
                    <svg class="w-full h-auto min-w-[1200px]" viewBox="0 0 1200 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 300H1200V180C1150 180 1120 150 1100 120C1080 150 1050 180 1000 180V100C1000 80 980 60 960 60H940C920 60 900 80 900 100V180C850 180 820 200 800 230C780 200 750 180 700 180V50C700 22.3858 677.614 0 650 0H550C522.386 0 500 22.3858 500 50V180C450 180 420 200 400 230C380 200 350 180 300 180V100C300 80 280 60 260 60H240C220 60 200 80 200 100V180C150 180 120 150 100 120C80 150 50 180 0 180V300Z" fill="white"/>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <div id="modalDonasi" class="fixed inset-0 z-[60] hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeDonasiModal()"></div>
        <div class="glass-card relative w-full max-w-md p-8 rounded-[40px] shadow-2xl border border-white/20">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-2xl font-black text-slate-900">Infaq Terbaik</h3>
                    <p id="namaKegiatanModal" class="text-[10px] text-[#147a54] font-black uppercase tracking-widest mt-1"></p>
                </div>
                <button onclick="closeDonasiModal()" class="text-slate-400 hover:text-red-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <form id="donasiForm" class="space-y-5">
                <input type="hidden" id="modal_id_kegiatan_hidden">
                <div>
                    <label class="text-[10px] font-black uppercase text-gray-400 block mb-2">Nama Donatur</label>
                    <input type="text" id="modal_nama" placeholder="Hamba Allah" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50/50 focus:ring-2 focus:ring-green-500 outline-none text-sm font-bold transition-all">
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase text-gray-400 block mb-2">Nominal (Rp)</label>
                    <input type="number" id="modal_nominal" min="10000" placeholder="Contoh: 50000" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50/50 focus:ring-2 focus:ring-green-500 outline-none text-sm font-bold transition-all" required>
                </div>
                
                <div class="pt-4">
                    <button type="submit" id="pay-button" class="w-full py-5 bg-[#147a54] text-white font-black rounded-2xl shadow-xl shadow-green-900/20 hover:bg-[#0d5c3f] transition-all flex items-center justify-center gap-3">
                        <span>Lanjutkan Pembayaran</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openDonasiModal(id, nama) {
            document.getElementById('modal_id_kegiatan_hidden').value = id;
            document.getElementById('namaKegiatanModal').innerText = nama;
            document.getElementById('modalDonasi').classList.remove('hidden');
            document.body.style.overflow = 'hidden'; 
        }

        function closeDonasiModal() {
            document.getElementById('modalDonasi').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        document.getElementById('donasiForm').onsubmit = function(e) {
            e.preventDefault();
            const btn = document.getElementById('pay-button');
            const originalText = btn.innerHTML;
            
            btn.innerHTML = "Memproses...";
            btn.disabled = true;

            const dataDonasi = {
                id_kegiatan: document.getElementById('modal_id_kegiatan_hidden').value,
                nama_donatur: document.getElementById('modal_nama').value || 'Hamba Allah',
                nominal: document.getElementById('modal_nominal').value
            };

            fetch("{{ route('donasi.checkout') }}", {
                method: "POST",
                headers: { 
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}" 
                },
                body: JSON.stringify(dataDonasi)
            })
            .then(response => response.json())
            .then(data => {
                if(data.token) {
                    window.snap.pay(data.token, {
                        onSuccess: function(result) { window.location.reload(); },
                        onPending: function(result) { window.location.reload(); },
                        onError: function(result) { 
                            alert("Pembayaran Gagal"); 
                            btn.disabled = false; 
                            btn.innerHTML = originalText; 
                        },
                        onClose: function() {
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        }
                    });
                } else {
                    alert("Gagal mendapatkan token: " + (data.error || "Unknown error"));
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(err => {
                console.error(err);
                alert("Terjadi kesalahan sistem.");
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        };
    </script>
    <style>
    @keyframes marquee {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    .animate-marquee {
        animation: marquee 25s linear infinite;
        display: flex;
        width: max-content;
    }
    /* Berhenti saat kursor diarahkan agar mudah dibaca */
    .animate-marquee:hover {
        animation-play-state: paused;
    }
</style>
</body>
</html>