<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }} - Arisan Qurban Masjid Nurul Huda</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #14532d; border-radius: 10px; }
        
        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.95) translateY(-10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .animate-dropdown { animation: fadeInScale 0.2s ease-out forwards; }
    </style>
</head>
<body class="bg-[#f8fafc] flex min-h-screen overflow-hidden">

    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-20 hidden backdrop-blur-sm transition-all md:hidden"></div>

    <div id="sidebar-container" class="fixed inset-y-0 left-0 z-30 transform -translate-x-full transition-transform duration-300 md:relative md:translate-x-0">
        @include('admin.layout.sidebar')
    </div>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto custom-scrollbar">
        
        <header class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100 px-4 md:px-8 py-3 flex justify-between items-center">
            
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="p-2 bg-green-50 text-green-700 rounded-xl md:hidden hover:bg-green-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <div class="hidden md:block text-[10px] font-medium text-gray-400 italic tracking-wide">
                    Sistem Manajemen Masjid Nurul Huda
                </div>
            </div>

            <div class="flex items-center gap-3">
                {{-- NOTIFIKASI DROPDOWN --}}
                <div class="relative" id="notif-wrapper">
                    @php 
                        $pendingUsers = \App\Models\User::where('status', 'pending')->where('role', 'peserta')->latest()->get();
                        $pendingCount = $pendingUsers->count();
                        $displayUsers = $pendingUsers->take(3);
                    @endphp
                    @php
                        $pembayaranHariIni = \App\Models\TransaksiPembayaran::whereDate('updated_at', now()->toDateString())
                            ->where('status_pembayaran', 'sukses')
                            ->where('metode_pembayaran', '!=', 'tunai') // Menampilkan selain tunai
                            ->where('is_read', 0)
                            ->latest('updated_at') // Urutkan berdasarkan waktu update terbaru
                            ->get();

                        $notifPembayaranCount = $pembayaranHariIni->count();
                        $displayPembayaran = $pembayaranHariIni->take(3);
                    @endphp

                    <button onclick="toggleNotif()" class="relative p-2 text-gray-400 bg-white rounded-full border border-gray-100 shadow-sm hover:text-green-700 hover:bg-green-50 transition-all active:scale-90 outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        
                        @if(($pendingCount + $notifPembayaranCount) > 0)
                        <span class="absolute top-0 right-0 flex h-5 w-5 transform translate-x-1 -translate-y-1">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex items-center justify-center rounded-full h-5 w-5 bg-red-600 text-[10px] font-bold text-white border-2 border-white shadow-sm">
                                {{ $pendingCount + $notifPembayaranCount }}
                            </span>
                        </span>
                        @endif
                    </button>

                    <div id="notif-dropdown" class="hidden absolute right-0 mt-4 w-80 bg-white rounded-[28px] shadow-2xl border border-gray-100 overflow-hidden z-50 animate-dropdown">
                        <div class="p-5 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                            <div>
                                <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest">Notifikasi Sistem</h3>
                                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter">Aktivitas Terbaru</p>
                            </div>
                            <span class="text-[9px] bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full font-bold uppercase">{{ $pendingCount + $notifPembayaranCount }} Notif</span>
                        </div>
                        
                        <div class="max-h-96 overflow-y-auto custom-scrollbar">
                            {{-- ================= NOTIF PEMBAYARAN ================= --}}
                            <div class="px-4 pt-3 pb-1">
                                <p class="text-[10px] font-bold text-green-700 uppercase tracking-widest">
                                    Notifikasi Pembayaran
                                </p>
                            </div>
                            @forelse($displayPembayaran as $trx)
                            <a href="{{ route('admin.transaksi.index') }}" 
                                onclick="hapusNotif(event, this)"
                                class="notif-item block p-4 border-b border-gray-50 hover:bg-green-50/40 transition-all">

                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 overflow-hidden">
                                        
                                        <div class="w-10 h-10 bg-green-600 text-white rounded-2xl flex items-center justify-center shrink-0 font-black text-xs">
                                            Rp
                                        </div>

                                        <div class="overflow-hidden">
                                            <p class="text-xs font-black text-green-800 leading-tight uppercase truncate">
                                                Pembayaran Arisan Masuk
                                            </p>
                                            <p class="text-[10px] text-gray-500 font-bold truncate">
                                                {{ $trx->peserta->nama ?? 'Peserta' }} • Rp {{ number_format($trx->nominal,0,',','.') }}
                                            </p>

                                            <p class="text-[9px] text-gray-400 italic">
                                                Bulan {{ $trx->bulan_iuran }}
                                            </p>
                                            <p class="text-[9px] text-gray-400 italic">
                                                {{ $trx->updated_at->diffForHumans() }}
                                            </p>
                                        </div>

                                    </div>

                                    <div class="text-green-600 text-xs font-black">
                                        ✔
                                    </div>
                                </div>
                            </a>
                            @empty
                            <div class="p-6 text-center">
                                <p class="text-[10px] text-gray-400 font-bold uppercase">
                                    Belum ada pembayaran hari ini
                                </p>
                            </div>
                            @endforelse
                            {{-- ================= NOTIF PENDAFTAR BARU ================= --}}
                            <div class="px-4 pt-3 pb-1">
                                <p class="text-[10px] font-bold text-orange-600 uppercase tracking-widest">
                                    Pendaftar Baru
                                </p>
                            </div>
                            @forelse($displayUsers as $user)
                            <div class="p-4 border-b border-gray-50 hover:bg-gray-50/30 transition-all">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 overflow-hidden">
                                        <div class="w-10 h-10 bg-green-800 text-white rounded-2xl flex items-center justify-center shrink-0 font-black text-xs uppercase">
                                            {{ substr($user->nama, 0, 2) }}
                                        </div>
                                        <div class="overflow-hidden">
                                            <p class="text-xs font-black text-slate-800 leading-tight uppercase truncate">{{ $user->nama }}</p>
                                            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter italic">{{ $user->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center gap-2 shrink-0">
                                        <a href="{{ route('admin.pending.index') }}" class="p-2 bg-green-50 text-green-600 rounded-xl hover:bg-green-600 hover:text-white transition-all active:scale-90" title="Buka Halaman Verifikasi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </a>

                                        {{-- Tombol Reject (X) Biarkan Tetap Utuh --}}
                                        <form action="{{ route('admin.pending.reject', $user->id_user) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="p-2 bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all active:scale-90" title="Tolak & Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="p-10 text-center">
                                <p class="text-[10px] font-bold text-gray-300 uppercase tracking-widest italic">Belum ada pendaftar</p>
                            </div>
                            @endforelse
                        </div>

                        <a href="{{ route('admin.pending.index') }}" class="block p-4 text-center bg-gray-50 text-gray-500 text-[10px] font-black uppercase tracking-[0.2em] hover:bg-gray-100 transition-all">
                            @if($pendingCount > 3) Lihat +{{ $pendingCount - 3 }} Lainnya @else Lihat Semua Peserta Pending @endif
                        </a>
                    </div>
                </div>

                {{-- PROFIL USER --}}
                <a href="{{ route('admin.profile.index') }}" class="flex items-center gap-3 bg-white p-1 pr-4 rounded-full shadow-sm border border-gray-100 transition-all hover:shadow-md hover:border-green-200 active:scale-95 group">
                    <div class="w-8 h-8 bg-green-800 text-white rounded-full flex items-center justify-center text-xs font-bold shadow-inner group-hover:bg-green-700 transition-colors uppercase">
                        {{ substr(Auth::user()->nama ?? 'U', 0, 2) }}
                    </div>
                    <div class="text-[10px] leading-tight">
                        <p class="font-bold text-gray-800 tracking-tight group-hover:text-green-700 transition-colors uppercase">
                            {{ Auth::user()->nama }}
                        </p>
                        <p class="text-gray-500 lowercase font-semibold tracking-tighter">
                            {{ Auth::user()->role }}
                        </p>
                    </div>
                    <svg class="w-3 h-3 text-gray-300 group-hover:text-green-500 transition-colors ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                    </svg>
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
            sidebar.classList.toggle('translate-x-0', !isHidden);
            overlay.classList.toggle('hidden', isHidden);
            overlay.classList.toggle('block', !isHidden);
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
    
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            timer: 2500,
            showConfirmButton: false,
            customClass: { popup: 'rounded-[32px]' }
        });
    </script>
    @endif
    <script>
    function hapusNotif(e, el) {
        e.preventDefault();

        el.style.transition = "all 0.3s ease";
        el.style.opacity = "0";
        el.style.transform = "translateX(50px)";

        setTimeout(() => {
            window.location.href = el.href;
        }, 300);
    }
    </script>

</body>
</html>