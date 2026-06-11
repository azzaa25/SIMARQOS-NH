<aside class="w-72 bg-[#064e3b] text-white flex flex-col shadow-2xl z-20 h-screen sticky top-0">
    <div class="p-8 shrink-0">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center border border-green-500/30">
                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2l2 2m-4-2l2 2m-2 2h4l1 3h-6l1-3zM5 22h14v-7l-2-2v-4a5 5 0 00-10 0v4l-2 2v7zM10 16h4v6h-4v-6z"></path>
                </svg>
            </div>
            <h1 class="font-extrabold text-sm leading-tight tracking-wide">
                MASJID <br> NURUL HUDA
            </h1>
        </div>
        <p class="text-[10px] text-green-300/60 uppercase font-bold tracking-[0.2em] border-t border-white/10 pt-4">
            Arisan Qurban & Kegiatan Sosial
        </p>
    </div>

    <nav class="flex-1 px-4 overflow-y-auto custom-scrollbar">
        
        <div class="mb-6">
            <p class="text-[10px] font-bold text-green-500/50 uppercase px-4 mb-2 tracking-widest">Menu Utama</p>
            
            <a href="{{ route('admin.dashboard') }}" class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-green-500/10 text-green-400 font-bold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span class="text-sm">Dashboard</span>
            </a>

            <a href="{{ route('admin.peserta.index') }}" class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.peserta.*') ? 'bg-green-500/10 text-green-400 font-bold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span class="text-sm">Peserta Arisan</span>
            </a>

            <a href="{{ route('admin.skema.index') }}" class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.skema.*') ? 'bg-green-500/10 text-green-400 font-bold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <span class="text-sm">Skema Arisan</span>
            </a>

            <a href="{{ route('admin.undian.index') }}" class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.undian.*') ? 'bg-green-500/10 text-green-400 font-bold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                <span class="text-sm">Undian Arisan</span>
            </a>

            <a href="{{ route('admin.transaksi.index') }}" class="group flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all {{ request()->routeIs('admin.transaksi.*') ? 'bg-green-500/10 text-green-400 font-bold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-sm">Transaksi Pembayaran</span>
            </a>
            <a href="{{ route('admin.pengeluaran.index') }}" class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.pengeluaran.*') ? 'bg-green-500/10 text-green-400 font-bold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm">Laporan Arisan Qurban</span>
            </a>
        </div>

        <div class="mb-6">
            <p class="text-[10px] font-bold text-green-500/50 uppercase px-4 mb-2 tracking-widest">Kegiatan Sosial</p>

            <a href="{{ route('admin.sosial.index') }}" class="group flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all {{ request()->routeIs('admin.sosial.index') ? 'bg-green-500/10 text-green-400 font-bold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                <span class="text-sm">Kegiatan Sosial</span>
            </a>

            <a href="{{ route('admin.sosial.laporan') }}" class="group flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all {{ request()->routeIs('admin.sosial.laporan') ? 'bg-green-500/10 text-green-400 font-bold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span class="text-sm">Laporan Kegiatan</span>
            </a>
        </div>
    </nav>

    <div class="p-4 border-t border-white/5 bg-[#043d2e] shrink-0 mt-auto">
        <form action="/logout" method="POST">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-400 hover:bg-red-500/10 transition-all font-semibold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span class="text-sm font-bold">Keluar Sistem</span>
            </button>
        </form>
    </div>
</aside>