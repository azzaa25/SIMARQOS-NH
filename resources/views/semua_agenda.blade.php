<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semua Agenda - Masjid Nurul Huda</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; color: #1e293b; }
        .glass-nav { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(0,0,0,0.05); }
        .poster-card { transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .poster-card:hover { transform: translateY(-10px); }
        .filter-btn.active { background-color: #147a54; color: white; box-shadow: 0 10px 15px -3px rgba(20, 122, 84, 0.3); }
        .blob { position: absolute; z-index: -1; filter: blur(80px); opacity: 0.4; }
        
        /* Skeleton loading feel for images */
        .img-container { background: #e2e8f0; position: relative; overflow: hidden; }
        .img-container::after {
            content: ""; position: absolute; inset: 0;
            transform: translateX(-100%);
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            animation: shimmer 2s infinite;
        }
        @keyframes shimmer { 100% { transform: translateX(100%); } }
    </style>
</head>

<body class="antialiased overflow-x-hidden">
    <div class="blob w-[500px] h-[500px] bg-green-100 -top-20 -left-20"></div>
    <div class="blob w-[400px] h-[400px] bg-blue-50 bottom-0 right-0"></div>

    <nav class="glass-nav sticky top-0 z-50 px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <a href="{{ route('welcome') }}" class="group flex items-center gap-3">
                <div class="w-10 h-10 bg-[#147a54] rounded-xl flex items-center justify-center shadow-lg group-hover:rotate-6 transition-transform">
                    <svg viewBox="0 0 24 24" class="w-6 h-6 text-white fill-current"><path d="M12 3a9 9 0 109 9c0-.46-.04-.92-.1-1.36a5.38 5.38 0 01-4.4 2.26 5.4 5.4 0 01-5.4-5.4c0-1.8.88-3.4 2.24-4.4-.44-.06-.9-.1-1.34-.1z"/></svg>
                </div>
                <div>
                    <span class="block text-sm font-black text-gray-900 leading-none tracking-tight">MASJID NURUL HUDA</span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Digital Platform</span>
                </div>
            </a>

            <a href="{{ route('welcome') }}" class="flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 rounded-full text-xs font-black text-slate-600 hover:text-[#147a54] hover:border-[#147a54] hover:shadow-md transition-all group">
                <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-linecap="round" stroke-linejoin="round"/></svg>
                KEMBALI KE BERANDA
            </a>
        </div>
    </nav>

    <header class="pt-20 pb-12 px-6 text-center">
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-full text-[10px] font-black text-[#147a54] uppercase tracking-[0.3em] shadow-sm mb-6 border border-green-50">
            <span class="w-2 h-2 bg-[#147a54] rounded-full animate-pulse"></span>
            Eksplorasi Program Kebaikan
        </div>
        <h1 class="text-4xl md:text-6xl font-black text-slate-900 tracking-tight mb-4">Agenda Sosial Kami.</h1>
        <p class="text-slate-500 max-w-2xl mx-auto font-medium text-base md:text-lg">
            Pilih dan dukung berbagai program dakwah serta aksi sosial yang sedang berjalan.
        </p>
    </header>

    <div class="max-w-7xl mx-auto px-6 mb-12">
        <div class="flex flex-wrap justify-center gap-3">
            <button onclick="filterKategori('all', this)" class="filter-btn active px-6 py-2.5 bg-white text-slate-500 rounded-full text-[11px] font-black uppercase tracking-widest transition-all border border-slate-100 hover:border-green-200 shadow-sm">
                Semua Agenda
            </button>
            @foreach($kategori as $kat)
                <button onclick="filterKategori('kat-{{ $kat->id_kategori }}', this)" class="filter-btn px-6 py-2.5 bg-white text-slate-500 rounded-full text-[11px] font-black uppercase tracking-widest transition-all border border-slate-100 hover:border-green-200 shadow-sm">
                    {{ $kat->nama_kategori }}
                </button>
            @endforeach
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-6 pb-24">
        <div id="agenda-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 transition-all duration-500">
            @forelse($agendas as $agenda)
                <div class="agenda-item poster-card group kat-{{ $agenda->id_kategori }}" data-category="kat-{{ $agenda->id_kategori }}">
                    <a href="{{ route('sosial.detail', $agenda->id_kegiatan) }}" class="block">
                        <div class="relative rounded-[32px] overflow-hidden bg-white p-3 shadow-xl shadow-slate-200/50 border border-slate-100 group-hover:border-green-200 transition-all">
                            
                            <div class="img-container relative rounded-[24px] overflow-hidden aspect-[2/3]">
                                <img src="{{ asset('storage/'.$agenda->pamflet_kegiatan) }}" 
                                     alt="{{ $agenda->nama_kegiatan }}" 
                                     class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-1000">
                                
                                <div class="absolute inset-0 bg-gradient-to-t from-[#147a54]/90 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex flex-col justify-end p-6">
                                    <p class="text-white text-[10px] font-black uppercase tracking-[0.2em] translate-y-4 group-hover:translate-y-0 transition-transform duration-500">Lihat Detail Program</p>
                                </div>

                                <div class="absolute top-4 left-4">
                                    <span class="px-3 py-1.5 bg-white/95 backdrop-blur text-[#147a54] text-[9px] font-black rounded-lg uppercase tracking-wider shadow-xl">
                                        {{ $agenda->kategori->nama_kategori ?? 'Umum' }}
                                    </span>
                                </div>
                            </div>

                            <div class="px-3 py-5">
                                <h3 class="font-extrabold text-slate-800 text-base leading-snug mb-4 line-clamp-2 h-12 group-hover:text-[#147a54] transition-colors">
                                    {{ $agenda->nama_kegiatan }}
                                </h3>
                                
                                @php
                                    $danaMasuk = \App\Models\DanaSosial::where('id_kegiatan', $agenda->id_kegiatan)
                                                ->where('tipe_dana', 'masuk') // Filter hanya dana masuk
                                                ->whereIn('status_pembayaran', ['success', 'settlement'])
                                                ->sum('nominal');
                                    
                                    $persen = $agenda->target_donasi > 0 ? min(($danaMasuk / $agenda->target_donasi) * 100, 100) : 0;
                                @endphp
                                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 group-hover:bg-green-50 group-hover:border-green-100 transition-colors">
                                    <div class="flex justify-between items-center text-[9px] font-black uppercase tracking-widest mb-2">
                                        <span class="text-slate-400">Pencapaian</span>
                                        <span class="text-[#147a54]">{{ round($persen) }}%</span>
                                    </div>
                                    <div class="w-full h-1.5 bg-slate-200 rounded-full overflow-hidden mb-3">
                                        <div class="h-full bg-[#147a54] rounded-full transition-all duration-1000 shadow-[0_0_8px_rgba(20,122,84,0.4)]" style="width: {{ $persen }}%"></div>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Terkumpul</span>
                                        <span class="text-sm font-black text-slate-900">Rp {{ number_format($danaMasuk, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-span-full py-20 flex flex-col items-center">
                    <img src="https://illustrations.popsy.co/flat/searching.svg" class="w-48 h-48 mb-6" alt="not found">
                    <p class="text-slate-400 font-bold uppercase tracking-widest">Belum ada agenda tersedia</p>
                </div>
            @endforelse
        </div>
        
        <div class="mt-16 flex justify-center">
            {{ $agendas->links() }}
        </div>
    </main>

    <footer class="py-12 border-t border-slate-100 text-center">
        <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.4em]">
            &copy; 2024 Masjid Nurul Huda Digital • Terus Berbuat Baik
        </p>
    </footer>

    <script>
        function filterKategori(category, element) {
            // Update UI Tombol menggunakan parameter element agar lebih pasti
            const buttons = document.querySelectorAll('.filter-btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            if(element) element.classList.add('active');

            const items = document.querySelectorAll('.agenda-item');
            const grid = document.getElementById('agenda-grid');

            // Animasi Grid Out
            grid.style.opacity = '0';
            grid.style.transform = 'translateY(10px)';

            setTimeout(() => {
                items.forEach(item => {
                    if (category === 'all' || item.getAttribute('data-category') === category) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                // Animasi Grid In
                grid.style.opacity = '1';
                grid.style.transform = 'translateY(0)';
            }, 300);
        }
    </script>

</body>
</html>