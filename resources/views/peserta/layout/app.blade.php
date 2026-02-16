<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Peserta' }} - Arisan Qurban Masjid Nurul Huda</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #14532d; border-radius: 10px; }
        
        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.95) translateY(-10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .animate-dropdown { animation: fadeInScale 0.2s ease-out forwards; }
    </style>
</head>
<body class="bg-[#f8fafc] flex min-h-screen overflow-hidden">

    {{-- Overlay Mobile --}}
    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-20 hidden backdrop-blur-sm transition-all md:hidden"></div>

    {{-- Sidebar --}}
    <div id="sidebar-container" class="fixed inset-y-0 left-0 z-30 transform -translate-x-full transition-transform duration-300 md:relative md:translate-x-0">
        @include('peserta.layout.sidebar')
    </div>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto custom-scrollbar">
        
        {{-- HEADER --}}
        <header class="bg-white/80 backdrop-blur-md sticky top-0 z-40 border-b border-gray-100 px-4 md:px-8 py-3 flex justify-between items-center">
            
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="p-2 bg-green-50 text-green-700 rounded-xl md:hidden hover:bg-green-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div class="hidden md:block text-[10px] font-medium text-gray-400 italic tracking-wide uppercase">
                    Portal Anggota Masjid Nurul Huda
                </div>
            </div>

            <div class="flex items-center gap-3">
                
                {{-- NOTIFIKASI DROPDOWN --}}
                <div class="relative" id="notif-wrapper">
                    @php 
                        // 1. Cek apakah ada undian baru dalam 7 hari terakhir
                        $adaUndianBaru = \App\Models\UndianArisan::where('created_at', '>=', now()->subDays(7))->exists();
                        
                        // 2. Logika: Jika ada undian DAN belum diklik, maka hitung sebagai 1 notif
                        $displayUndianNotif = ($adaUndianBaru && !session('undian_notif_read')) ? 1 : 0;
                        
                        // 3. Simulasi Notif Iuran (Selalu 1 jika belum dibaca)
                        $adaTagihan = !session('iuran_notif_read') ? 1 : 0; 
                        
                        // Total badge sekarang maksimal hanya 2 (1 Undian + 1 Iuran)
                        $totalBadge = $displayUndianNotif + $adaTagihan;
                    @endphp

                    <button onclick="toggleNotif()" class="relative p-2 text-gray-400 bg-white rounded-full border border-gray-100 shadow-sm hover:text-green-700 hover:bg-green-50 transition-all active:scale-90 outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        
                        @if($totalBadge > 0)
                        <span class="absolute top-0 right-0 flex h-5 w-5 transform translate-x-1 -translate-y-1">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex items-center justify-center rounded-full h-5 w-5 bg-red-600 text-[10px] font-bold text-white border-2 border-white shadow-sm">
                                {{ $totalBadge }}
                            </span>
                        </span>
                        @endif
                    </button>

                    {{-- MENU DROPDOWN --}}
                    <div id="notif-dropdown" class="hidden absolute right-0 mt-4 w-80 bg-white rounded-[28px] shadow-2xl border border-gray-100 overflow-hidden z-50 animate-dropdown">
                        <div class="p-5 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest">Pemberitahuan</h3>
                            <span class="text-[9px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold uppercase">Terbaru</span>
                        </div>
                        
                        <div class="max-h-96 overflow-y-auto custom-scrollbar">
                            {{-- Notif 1: Hasil Undian --}}
                            @if($displayUndianNotif > 0)
                            <a href="{{ route('peserta.mark-undian-read') }}" class="block p-4 border-b border-gray-50 hover:bg-green-50/30 transition-all group">
                                <div class="flex gap-4">
                                    <div class="w-10 h-10 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center shrink-0 group-hover:rotate-12 transition-transform">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-black text-slate-800 leading-tight mb-1 uppercase">Undian Telah Keluar!</p>
                                        <p class="text-[10px] text-gray-500 leading-relaxed italic">Admin baru saja mengundi pemenang periode terbaru. Lihat hasilnya.</p>
                                    </div>
                                </div>
                            </a>
                            @endif

                            {{-- Notif 2: Tagihan Iuran --}}
                            @if($adaTagihan > 0)
                            <a href="#" class="block p-4 border-b border-gray-50 hover:bg-orange-50/30 transition-all group">
                                <div class="flex gap-4">
                                    <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center shrink-0 group-hover:rotate-12 transition-transform">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1c-1.11 0-2.08-.402-2.599-1M12 8V7m0 11v1m0-1c-1.11 0-2.08-.402-2.599-1M12 18V17m0 1c1.11 0 2.08.402 2.599 1M12 18V17"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-black text-slate-800 leading-tight mb-1 uppercase text-orange-600">Waktunya Bayar Iuran</p>
                                        <p class="text-[10px] text-gray-500 leading-relaxed italic">Tagihan arisan bulan ini sudah tersedia. Segera lakukan pembayaran.</p>
                                    </div>
                                </div>
                            </a>
                            @endif

                            {{-- Empty State --}}
                            @if($totalBadge == 0)
                            <div class="p-10 text-center">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest italic">Tidak ada kabar terbaru</p>
                            </div>
                            @endif
                        </div>

                        <a href="{{ route('peserta.mark-undian-read') }}" class="block p-4 text-center bg-[#064e3b] text-white text-[10px] font-black uppercase tracking-[0.2em] hover:bg-green-900 transition-all">
                            Lihat Semua Aktivitas
                        </a>
                    </div>
                </div>

                {{-- PROFIL USER --}}
                <a href="#" class="flex items-center gap-3 bg-white p-1 pr-4 rounded-full shadow-sm border border-gray-100 transition-all hover:shadow-md hover:border-green-200 active:scale-95 group">
                    <div class="w-8 h-8 bg-green-800 text-white rounded-full flex items-center justify-center text-xs font-bold shadow-inner group-hover:rotate-12 transition-all uppercase">
                        {{ substr(Auth::user()->nama ?? 'U', 0, 2) }}
                    </div>
                    <div class="text-[10px] leading-tight">
                        <p class="font-bold text-gray-800 tracking-tight uppercase group-hover:text-green-700 transition-colors">
                            {{ Auth::user()->nama }}
                        </p>
                        <p class="text-gray-400 font-semibold tracking-tighter italic">
                            ID: {{ Auth::user()->id_user }}
                        </p>
                    </div>
                </a>
            </div>
        </header>

        <div class="p-4 md:p-8 flex-1">
            @yield('content')
        </div>
    </main>

    {{-- SCRIPTS --}}
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar-container');
            const isHidden = sidebar.classList.toggle('-translate-x-full');
            const overlay = document.getElementById('sidebar-overlay');
            overlay.classList.toggle('hidden', isHidden);
        }

        function toggleNotif() {
            const dropdown = document.getElementById('notif-dropdown');
            dropdown.classList.toggle('hidden');
        }

        window.addEventListener('click', function(e) {
            const wrapper = document.getElementById('notif-wrapper');
            const dropdown = document.getElementById('notif-dropdown');
            if (wrapper && !wrapper.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>