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
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #14532d; border-radius: 10px; }
    </style>
</head>
<body class="bg-[#f8fafc] flex min-h-screen overflow-hidden">

    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-20 hidden backdrop-blur-sm md:hidden"></div>

    <div id="sidebar-container" class="fixed inset-y-0 left-0 z-30 transform -translate-x-full transition-transform duration-300 md:relative md:translate-x-0">
        @include('peserta.layout.sidebar') {{-- Load Sidebar Khusus Peserta --}}
    </div>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto custom-scrollbar">
        <header class="bg-white/80 backdrop-blur-md sticky top-0 z-10 border-b border-gray-100 px-4 md:px-8 py-3 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="p-2 bg-green-50 text-green-700 rounded-xl md:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div class="hidden md:block text-[10px] font-medium text-gray-400 italic tracking-wide uppercase">
                    Dashboard Anggota Arisan Nurul Huda
                </div>
            </div>

            <div class="flex items-center gap-3">
                {{-- Notifikasi Pembayaran / Tagihan Baru --}}
                <div class="relative p-2 text-gray-400 bg-white rounded-full border border-gray-100 shadow-sm hover:text-green-700 transition-all cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    {{-- Badge notifikasi jika ada tagihan --}}
                    <span class="absolute top-0 right-0 flex h-4 w-4 transform translate-x-1 -translate-y-1">
                        <span class="relative inline-flex items-center justify-center rounded-full h-4 w-4 bg-red-600 text-[8px] font-bold text-white border-2 border-white">!</span>
                    </span>
                </div>

                {{-- Link ke Edit Profil --}}
                <a href="#" class="flex items-center gap-3 bg-white p-1 pr-4 rounded-full shadow-sm border border-gray-100 hover:border-green-200 transition-all group">
                    <div class="w-8 h-8 bg-[#064e3b] text-white rounded-full flex items-center justify-center text-xs font-bold uppercase">
                        {{ substr(Auth::user()->nama ?? 'P', 0, 2) }}
                    </div>
                    <div class="text-[10px] leading-tight">
                        <p class="font-bold text-gray-800 uppercase tracking-tight">{{ Auth::user()->nama }}</p>
                        <p class="text-gray-500 font-semibold italic">ID: {{ Auth::user()->id_user }}</p>
                    </div>
                </a>
            </div>
        </header>

        <div class="p-4 md:p-8">
            @yield('content')
        </div>
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar-container');
            const overlay = document.getElementById('sidebar-overlay');
            const isHidden = sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden', isHidden);
            overlay.classList.toggle('block', !isHidden);
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>