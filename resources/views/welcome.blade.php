<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Masjid Nurul Huda - Arisan Qurban & Sosial</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; color: #1e293b; scroll-behavior: smooth; }
        
        /* Glass Effect */
        .glass-nav { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(15px); border-bottom: 1px solid rgba(255, 255, 255, 0.3); }
        .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.5); }
        
        /* Animations */
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        .animate-float { animation: float 5s ease-in-out infinite; }
        
        .blob { position: absolute; z-index: -1; filter: blur(80px); opacity: 0.4; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #147a54; border-radius: 10px; }
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
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest uppercase">Digital Platform</span>
                </div>
            </div>
            
            <div class="hidden md:flex items-center gap-8 text-xs font-bold uppercase tracking-widest text-gray-500">
                <a href="#hero" class="hover:text-[#147a54] transition-colors">Beranda</a>
                <a href="#fitur" class="hover:text-[#147a54] transition-colors">Fitur</a>
                <a href="#masjid" class="hover:text-[#147a54] transition-colors">Informasi Masjid</a>
                <a href="/login" class="px-6 py-3 bg-[#147a54] text-white rounded-full shadow-lg shadow-green-900/20 hover:bg-[#0d5c3f] transition-all">Masuk</a>
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
                <h1 class="text-5xl md:text-6xl font-black text-slate-900 leading-[1.1] tracking-tight">
                    Ibadah Qurban Jadi <span class="text-[#147a54]">Lebih Ringan</span> & Terkelola.
                </h1>
                <p class="text-slate-500 text-lg leading-relaxed max-w-lg">
                    Platform digital khusus jamaah Masjid Nurul Huda untuk mengelola tabungan Arisan Qurban secara transparan, otomatis, dan amanah.
                </p>
                <div class="flex items-center gap-4">
                    <a href="/register" class="px-10 py-5 bg-[#147a54] text-white font-black rounded-2xl shadow-xl shadow-green-900/20 hover:bg-[#0d5c3f] transition-all hover:-translate-y-1">Mulai Daftar</a>
                    <a href="#fitur" class="px-10 py-5 bg-white text-slate-600 font-black rounded-2xl border border-slate-100 shadow-sm hover:bg-slate-50 transition-all">Pelajari Fitur</a>
                </div>
            </div>
            
            <div class="relative hidden md:block">
                <div class="glass-card p-4 rounded-[40px] shadow-2xl animate-float">
                    <img src="https://images.unsplash.com/photo-1590076215667-875d4ef2d97e?auto=format&fit=crop&q=80&w=800" alt="Mosque" class="rounded-[30px] w-full object-cover aspect-square">
                </div>
                <div class="absolute -bottom-10 -left-10 glass-card p-6 rounded-3xl shadow-xl border-l-4 border-l-[#147a54]">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Peserta</p>
                    <p class="text-3xl font-black text-slate-900">250+</p>
                    <p class="text-[10px] font-bold text-green-600">Aktif Tahun Ini</p>
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

    <section id="masjid" class="py-24 px-6 overflow-hidden">
        <div class="max-w-7xl mx-auto glass-card rounded-[50px] overflow-hidden grid md:grid-cols-2 shadow-2xl">
            <div class="p-12 md:p-20 space-y-8">
                <h2 class="text-3xl font-black text-slate-900">Mengenal Masjid <br><span class="text-[#147a54]">Nurul Huda</span></h2>
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 shrink-0 bg-slate-100 rounded-lg flex items-center justify-center text-[#147a54]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <p class="text-sm font-medium text-slate-600 italic">Desa Titik, Kediri, Jawa Timur<br><span class="text-xs font-bold text-gray-400 not-italic uppercase tracking-widest">Titik Village, Kediri Regency</span></p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-slate-50 rounded-2xl">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Kajian Rutin</p>
                            <p class="text-xs font-bold text-slate-700">Setiap Minggu Malam</p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Kapasitas</p>
                            <p class="text-xs font-bold text-slate-700">500+ Jamaah</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-[#147a54] p-12 flex flex-col justify-center items-center text-center text-white space-y-6">
                <div class="w-20 h-20 bg-white/10 rounded-full flex items-center justify-center animate-pulse">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 21l-8.228-9.288a5 5 0 117.454-6.67l.774.88.774-.88a5 5 0 117.454 6.67L12 21z" stroke-width="2"/></svg>
                </div>
                <p class="text-2xl font-black italic italic leading-tight">"Mari Membangun Ukhuwah Melalui Ibadah Qurban Bersama"</p>
                <div class="fixed bottom-0 left-0 w-full z-0 pointer-events-none opacity-[0.2] flex items-end overflow-hidden">
                    <svg class="w-full h-auto min-w-[1200px]" viewBox="0 0 1200 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 300H1200V180C1150 180 1120 150 1100 120C1080 150 1050 180 1000 180V100C1000 80 980 60 960 60H940C920 60 900 80 900 100V180C850 180 820 200 800 230C780 200 750 180 700 180V50C700 22.3858 677.614 0 650 0H550C522.386 0 500 22.3858 500 50V180C450 180 420 200 400 230C380 200 350 180 300 180V100C300 80 280 60 260 60H240C220 60 200 80 200 100V180C150 180 120 150 100 120C80 150 50 180 0 180V300Z" fill="white"/>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-white border-t border-slate-100 py-12 px-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8 text-center md:text-left">
            <div>
                <p class="text-sm font-black text-[#147a54] mb-1">Masjid Nurul Huda Digital</p>
                <p class="text-xs text-slate-400 font-medium tracking-wide italic">© 2026 Arisan Qurban & Kegiatan Sosial. Seluruh Hak Cipta Dilindungi.</p>
            </div>
            <div class="flex gap-4">
                <a href="/login" class="text-xs font-black text-slate-400 hover:text-[#147a54] transition-colors">Login Admin</a>
                <span class="text-slate-200">|</span>
                <a href="/register" class="text-xs font-black text-slate-400 hover:text-[#147a54] transition-colors">Pendaftaran Peserta</a>
            </div>
        </div>
    </footer>

</body>
</html>