<aside class="w-72 bg-[#064e3b] text-white flex flex-col shadow-2xl z-20 h-screen sticky top-0">
    <div class="p-8 shrink-0">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center border border-green-500/30">
                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <h1 class="font-extrabold text-sm leading-tight tracking-wide uppercase">
                Dashboard<br> Peserta
            </h1>
        </div>
        <p class="text-[10px] text-green-300/60 uppercase font-bold tracking-[0.2em] border-t border-white/10 pt-4 italic">
            Masjid Nurul Huda
        </p>
    </div>

    <nav class="flex-1 px-4 overflow-y-auto custom-scrollbar">
        <div class="mb-6">
            <p class="text-[10px] font-bold text-green-500/50 uppercase px-4 mb-2 tracking-widest">Aktivitas Saya</p>
            
            {{-- Dashboard & Profil --}}
            <a href="{{ route('peserta.dashboard') }}" class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('peserta.dashboard') ? 'bg-green-500/10 text-green-400 font-bold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span class="text-sm">Ringkasan Info</span>
            </a>

            {{-- LOGIKA BARU: Cek tipe_skema secara langsung --}}
            @php $p = auth()->user()->peserta; @endphp
            @if($p && $p->skemaArisan && $p->skemaArisan->tipe_skema === 'kelompok')
            <a href="{{ route('peserta.kelompok.index') }}" class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('peserta.kelompok.*') ? 'bg-green-500/10 text-green-400 font-bold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="text-sm">Anggota Kelompok</span>
            </a>
            @endif

            {{-- Jadwal & Iuran --}}
            <a href="{{ route('peserta.jadwal.index') }}" class="group flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all {{ request()->routeIs('peserta.jadwal.*') ? 'bg-green-500/10 text-green-400 font-bold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span class="text-sm">Jadwal & Tagihan</span>
            </a>

            {{-- Hasil Undian --}}
            <a href="{{ route('peserta.undian.index') }}" class="group flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all {{ request()->routeIs('peserta.undian.*') ? 'bg-green-500/10 text-green-400 font-bold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                <span class="text-sm">Hasil Pengundian</span>
            </a>
        </div>

        <div class="mb-6">
            <p class="text-[10px] font-bold text-green-500/50 uppercase px-4 mb-2 tracking-widest">Keuangan & Bukti</p>

            {{-- Pembayaran VA/QRIS --}}
            <a href="{{ route('peserta.transaksi.index') }}" class="group flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all {{ request()->routeIs('peserta.transaksi.*') ? 'bg-green-500/10 text-green-400 font-bold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <span class="text-sm">Bayar Iuran</span>
            </a>

            {{-- Riwayat & Laporan --}}
            <a href="{{ route('peserta.laporan.index') }}" class="group flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all {{ request()->routeIs('peserta.laporan.*') ? 'bg-green-500/10 text-green-400 font-bold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span class="text-sm">Laporan Keuangan Diri</span>
            </a>
        </div>

        <div class="mb-6">
            <p class="text-[10px] font-bold text-green-500/50 uppercase px-4 mb-2 tracking-widest">Masjid</p>
            <a href="#" class="group flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                <span class="text-sm">Kegiatan Sosial</span>
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