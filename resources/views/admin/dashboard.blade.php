@extends('admin.layout.app')

@section('content')
<div class="space-y-8">
    {{-- Header --}}
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-bold text-green-900 leading-tight">Dashboard Admin</h1>
            <p class="text-sm text-gray-500">Selamat datang kembali, Ringkasan aktivitas sistem arisan & sosial</p>
            <p class="text-xs text-gray-400">Ringkasan data dan aktivitas terkini sistem arisan qurban dan kegiatan sosial</p>
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

            {{-- Total Kas --}}
            <div class="bg-white p-5 rounded-[24px] shadow-sm border border-gray-50 group hover:shadow-md transition-all lg:col-span-1 sm:col-span-2">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 bg-green-600 text-white rounded-xl flex items-center justify-center font-bold">Rp</div>
                    <span class="text-[10px] font-bold text-green-500 flex items-center">Kas</span>
                </div>
                <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest leading-tight">Total Kas Arisan</p>
                <h3 class="text-2xl font-black text-gray-800 mt-1">Rp {{ number_format($totalKasArisan, 0, ',', '.') }}</h3>
            </div>

            {{-- Transaksi Sukses --}}
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
        {{-- Transaksi Terbaru Dinamis --}}
        <div class="lg:col-span-2 bg-white rounded-[32px] shadow-sm border border-gray-50 overflow-hidden flex flex-col">
            <div class="p-6 flex justify-between items-center border-b border-gray-50">
                <h3 class="font-bold text-green-900 flex items-center gap-2">
                    <span class="text-gray-400 font-bold">$</span> Transaksi Pembayaran Terbaru
                </h3>
                <a href="{{ route('admin.transaksi.index') }}" class="text-[10px] font-bold text-gray-400 hover:text-green-700 uppercase tracking-widest flex items-center gap-1">
                    Lihat Semua <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
            <div class="p-2 space-y-1">
                @forelse($transaksiTerbaru as $t)
                <div class="flex items-center justify-between p-4 rounded-2xl hover:bg-gray-50 transition-colors border-b last:border-0 border-gray-50">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-green-800 text-white rounded-xl flex items-center justify-center text-xs font-bold uppercase">
                            {{ substr($t->peserta->nama ?? 'A', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800 leading-none">{{ $t->peserta->nama ?? 'Unknown' }}</p>
                            <p class="text-[10px] text-gray-400 mt-1 uppercase font-bold tracking-tighter">
                                {{ $t->peserta->skemaArisan->nama_skema ?? 'Umum' }} • {{ $t->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-black text-gray-800">Rp {{ number_format($t->nominal, 0, ',', '.') }}</p>
                        <span class="px-3 py-1 text-[9px] font-black rounded-full uppercase {{ $t->status_pembayaran == 'sukses' ? 'bg-green-100 text-green-600' : 'bg-orange-100 text-orange-600' }}">
                            {{ strtoupper($t->status_pembayaran) }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="p-8 text-center text-gray-400 text-sm italic">Belum ada transaksi</p>
                @endforelse
            </div>
        </div>

        {{-- Pemenang Arisan dengan Scroll --}}
        <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 overflow-hidden flex flex-col h-[500px]">
            <div class="p-6 flex items-center gap-2 border-b border-gray-50 bg-white sticky top-0 z-10">
                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="font-bold text-green-900 leading-none">Pemenang Terbaru</h3>
            </div>
            <div class="p-6 space-y-4 overflow-y-auto custom-scrollbar flex-1">
                @forelse($pemenangTerbaru as $u)
                <div class="p-4 rounded-[24px] border border-gray-100 flex flex-col gap-3 group hover:border-green-200 transition-all bg-gradient-to-br from-white to-green-50/30">
                    <div class="flex justify-between items-center leading-none">
                        <h4 class="text-sm font-bold text-gray-800">{{ $u->peserta->nama ?? 'Peserta' }}</h4>
                        <span class="text-[9px] font-black text-green-600 bg-green-50 px-2 py-1 rounded-md uppercase">WINNER</span>
                    </div>
                    <div class="flex items-center justify-between text-[10px] text-gray-400 font-bold uppercase tracking-tighter">
                        <div class="flex items-center gap-1">
                             {{ $u->skema->nama_skema ?? 'Skema' }}
                        </div>
                        <div class="text-orange-600">
                            Thn Ke-{{ $u->tahun_ke }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-20">
                    <p class="text-xs text-gray-400 italic">Belum ada data undian</p>
                </div>
                @endforelse
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
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
</style>
@endsection