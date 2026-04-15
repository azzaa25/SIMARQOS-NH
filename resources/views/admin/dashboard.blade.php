@extends('admin.layout.app')

@section('content')
<div class="space-y-8 animate-fadeIn">
    {{-- Header --}}
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-bold text-green-900 leading-tight tracking-tight">Dashboard Admin</h1>
            <p class="text-sm text-gray-500 font-medium italic">Sistem Manajemen Arisan & Sosial Masjid Nurul Huda</p>
            <p class="text-xs text-gray-400">Ringkasan data dan aktivitas terkini sistem arisan qurban dan kegiatan sosial</p>
        </div>
        <div class="bg-green-100 px-4 py-2 rounded-2xl flex items-center gap-2 border border-green-200">
            <span class="w-2 h-2 rounded-full bg-green-600 animate-pulse"></span>
            <span class="text-[10px] font-black text-green-700 uppercase tracking-widest">{{ now()->translatedFormat('d F Y') }}</span>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="space-y-4">        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            {{-- Total Peserta --}}
            <div class="bg-white p-5 rounded-[24px] shadow-sm border border-gray-50 group hover:shadow-md transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 bg-green-50 text-green-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <span class="text-[10px] font-bold text-green-500 flex items-center">Aktif</span>
                </div>
                <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Total Peserta</p>
                <h3 class="text-3xl font-black text-gray-800 mt-1">{{ number_format($totalPeserta) }}</h3>
            </div>

            {{-- Total Skema --}}
            <div class="bg-white p-5 rounded-[24px] shadow-sm border border-gray-50 group hover:shadow-md transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 bg-orange-50 text-orange-500 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                </div>
                <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Total Skema</p>
                <h3 class="text-3xl font-black text-gray-800 mt-1">{{ $totalSkema }}</h3>
            </div>

            {{-- SISA SALDO KAS (Update: Diambil dari Sisa Saldo Riil) --}}
            <div class="bg-green-900 p-5 rounded-[24px] shadow-lg shadow-green-900/20 group hover:shadow-xl transition-all lg:col-span-1 sm:col-span-2 border border-green-800 transform hover:-translate-y-1">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 bg-white/10 text-white rounded-xl flex items-center justify-center font-bold border border-white/20">Rp</div>
                    <span class="text-[9px] font-black text-green-300 bg-white/10 px-2 py-1 rounded-lg uppercase tracking-widest border border-white/10">Saldo Riil</span>
                </div>
                <p class="text-[10px] text-green-200 uppercase font-black tracking-widest leading-tight">Sisa Saldo Kas</p>
                <h3 class="text-xl font-black text-white mt-1">Rp {{ number_format($sisaSaldoKas, 0, ',', '.') }}</h3>
            </div>

            {{-- Transaksi Lunas --}}
            <div class="bg-white p-5 rounded-[24px] shadow-sm border border-gray-50 group hover:shadow-md transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 bg-cyan-50 text-cyan-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                </div>
                <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Transaksi Lunas</p>
                <h3 class="text-3xl font-black text-gray-800 mt-1">{{ $pembayaranLunas }}</h3>
            </div>

            {{-- Agenda Sosial --}}
            <div class="bg-white p-5 rounded-[24px] shadow-sm border border-gray-50 group hover:shadow-md transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 bg-yellow-50 text-yellow-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                </div>
                <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Agenda Sosial</p>
                <h3 class="text-3xl font-black text-gray-800 mt-1">{{ $kegiatanAktif }}</h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- List Transaksi Terbaru --}}
        <div class="lg:col-span-2 bg-white rounded-[32px] shadow-sm border border-gray-50 overflow-hidden flex flex-col">
            <div class="p-6 flex justify-between items-center border-b border-gray-50 bg-gray-50/30">
                <h3 class="font-black text-slate-800 uppercase text-xs tracking-[0.2em] flex items-center gap-3">
                    <span class="w-2 h-6 bg-green-800 rounded-full"></span>
                    Transaksi Pembayaran Terbaru
                </h3>
                <a href="{{ route('admin.transaksi.index') }}" class="text-[10px] font-bold text-gray-400 hover:text-green-700 uppercase tracking-widest flex items-center gap-1 transition-all">
                    Lihat Semua <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
            <div class="p-2 space-y-1">
                @forelse($transaksiTerbaru as $t)
                <div class="flex items-center justify-between p-4 rounded-2xl hover:bg-green-50/50 transition-colors border-b last:border-0 border-gray-50">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-green-800 text-white rounded-xl flex items-center justify-center text-xs font-bold uppercase shadow-sm">
                            {{ substr($t->peserta->nama ?? 'A', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800 leading-none uppercase">{{ $t->peserta->nama ?? 'Unknown' }}</p>
                            <p class="text-[10px] text-gray-400 mt-1 uppercase font-bold tracking-tighter">
                                {{ $t->peserta->skemaArisan->nama_skema ?? 'Umum' }} • {{ $t->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-black text-gray-800 tracking-tight">Rp {{ number_format($t->nominal, 0, ',', '.') }}</p>
                        <span class="px-3 py-1 text-[8px] font-black rounded-full uppercase {{ $t->status_pembayaran == 'sukses' ? 'bg-green-100 text-green-600' : 'bg-orange-100 text-orange-600' }}">
                            {{ strtoupper($t->status_pembayaran) }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="p-20 text-center text-gray-400 text-xs font-black uppercase tracking-widest italic italic">Belum ada transaksi terbaru</p>
                @endforelse
            </div>
        </div>

        {{-- Pemenang Arisan dengan Scroll (Update: Terdapat Tahun & Tombol Dinamis) --}}
        <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 overflow-hidden flex flex-col h-[550px]">
            <div class="p-6 flex items-center gap-2 border-b border-gray-50 bg-gray-50/30 sticky top-0 z-10">
                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="font-black text-slate-800 uppercase text-xs tracking-[0.2em]">Pemenang Terbaru</h3>
            </div>
            <div class="p-6 space-y-4 overflow-y-auto custom-scrollbar flex-1">
                @forelse($pemenangTerbaru as $u)
                <div class="p-4 rounded-[24px] border border-gray-100 flex flex-col gap-3 group hover:border-green-200 transition-all bg-gradient-to-br from-white to-green-50/30 relative overflow-hidden">
                    <div class="flex justify-between items-center leading-none relative z-10">
                        <h4 class="text-sm font-bold text-gray-800 uppercase tracking-tight">{{ $u->peserta->nama ?? 'Peserta' }}</h4>
                        <span class="text-[9px] font-black text-green-600 bg-green-50 px-2 py-1 rounded-md uppercase border border-green-100">WINNER</span>
                    </div>
                    <div class="flex items-center justify-between text-[10px] text-gray-400 font-bold uppercase tracking-widest relative z-10">
                        <div class="flex items-center gap-1">
                             {{ $u->skema->nama_skema ?? 'Skema' }}
                        </div>
                        {{-- MENAMPILKAN TAHUN PELAKSANAAN --}}
                        <div class="text-orange-600 bg-orange-50 px-2 py-0.5 rounded-lg border border-orange-100">
                            Realisasi {{ $u->tahun_pelaksanaan }}
                        </div>
                    </div>
                    {{-- Dekorasi Bintang di Belakang --}}
                    <div class="absolute -right-4 -bottom-4 text-green-100 opacity-20 group-hover:scale-125 transition-transform">
                        <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                    </div>
                </div>
                @empty
                <div class="text-center py-20 flex flex-col items-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                    </div>
                    <p class="text-[10px] text-gray-300 font-black uppercase tracking-[0.2em]">Belum ada data undian</p>
                </div>
                @endforelse
            </div>
            
            {{-- TOMBOL LIHAT DETAIL SELENGKAPNYA --}}
            <div class="p-6 bg-gray-50 border-t border-gray-100">
                <a href="{{ route('admin.undian.index') }}" class="flex items-center justify-center gap-3 w-full bg-white border border-gray-200 py-4 rounded-[20px] text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 hover:text-green-700 hover:border-green-600 hover:bg-green-50 transition-all shadow-sm group">
                    Lihat Hasil Undian Lengkap
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>
        </div>
    </div>

    {{-- Aksi Cepat --}}
    <div class="space-y-4">
        <div class="flex items-center gap-2">
            <div class="w-6 h-6 bg-green-50 text-green-700 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            </div>
            <h3 class="font-bold text-green-900 leading-none uppercase text-sm tracking-widest">Aksi Cepat</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('admin.skema.create') }}" class="flex items-center justify-between p-5 bg-white rounded-2xl border border-gray-50 shadow-sm hover:shadow-md hover:border-green-200 transition-all group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-gray-50 text-gray-500 rounded-xl flex items-center justify-center group-hover:bg-green-50 group-hover:text-green-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <span class="text-sm font-bold text-gray-600 group-hover:text-green-900 transition-colors tracking-tight">Tambah Skema Arisan</span>
                </div>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
            </a>
            
            <a href="{{ route('admin.peserta.create') }}" class="flex items-center justify-between p-5 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-green-200 transition-all group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-gray-50 text-gray-500 rounded-xl flex items-center justify-center group-hover:bg-green-50 group-hover:text-green-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    </div>
                    <span class="text-sm font-bold text-gray-600 group-hover:text-green-900 transition-colors tracking-tight">Tambah Peserta</span>
                </div>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>
    </div>
</div>

<style>
    .animate-fadeIn { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
</style>
@endsection