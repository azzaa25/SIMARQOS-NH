@extends('admin.layout.app')

@section('content')
<div class="max-w-7xl mx-auto animate-fadeIn space-y-8">
    
    {{-- Breadcrumb & Back Button --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.sosial.index') }}" class="p-3 bg-white rounded-2xl border border-gray-100 shadow-sm text-gray-400 hover:text-green-700 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Detail Agenda Sosial</h1>
            <p class="text-xs font-bold text-gray-400 tracking-widest uppercase">Platform Digital Masjid Nurul Huda</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        {{-- Sidebar: Pamflet & Progress --}}
        <div class="lg:col-span-4 space-y-6 sticky top-6">
            <div class="bg-white p-3 rounded-[40px] border border-gray-100 shadow-xl shadow-slate-200/50">
                <div class="relative rounded-[32px] overflow-hidden bg-slate-100 aspect-[3/4]">
                    <img src="{{ asset('storage/'.$item->pamflet_kegiatan) }}" 
                         alt="{{ $item->nama_kegiatan }}"
                         class="w-full h-full object-cover">
                    
                    <div class="absolute top-4 left-4">
                        <span class="px-4 py-2 bg-white/90 backdrop-blur-md text-[#147a54] text-[10px] font-black rounded-xl uppercase tracking-widest shadow-lg">
                            {{ $item->kategori->nama_kategori ?? 'Umum' }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-8 rounded-[35px] border border-gray-100 shadow-sm">
                @php
                    $danaMasuk = $donatur->sum('nominal');
                    $persentase = $item->target_donasi > 0 ? ($danaMasuk / $item->target_donasi) * 100 : 0;
                    if($persentase > 100) $persentase = 100;
                @endphp
                <div class="flex justify-between items-end mb-4">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Capaian Dana</span>
                    <span class="text-2xl font-black text-[#147a54]">{{ round($persentase) }}%</span>
                </div>
                <div class="w-full h-4 bg-slate-100 rounded-full overflow-hidden border border-slate-50 p-1">
                    <div class="h-full {{ $item->status_kegiatan == 'selesai' ? 'bg-slate-400' : 'bg-gradient-to-r from-green-600 to-green-400' }} rounded-full transition-all duration-1000 shadow-sm" style="width: {{ $persentase }}%"></div>
                </div>
                <div class="mt-6 flex justify-between items-center bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Terkumpul</p>
                        <p class="text-sm font-black text-slate-900">Rp {{ number_format($danaMasuk, 0, ',', '.') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Target</p>
                        <p class="text-sm font-black text-slate-400">Rp {{ number_format($item->target_donasi, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content: Description & Donors --}}
        <div class="lg:col-span-8 space-y-8">
            
            {{-- Info Card --}}
            <div class="bg-white p-10 md:p-14 rounded-[45px] border border-gray-100 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 w-40 h-40 bg-green-50 rounded-bl-[100px] -z-0 opacity-50"></div>
                
                <div class="relative z-10 space-y-8">
                    <div class="inline-flex items-center gap-2 px-4 py-2 {{ $item->status_kegiatan == 'selesai' ? 'bg-slate-100 text-slate-500' : 'bg-green-100 text-[#147a54]' }} rounded-xl text-[10px] font-black uppercase tracking-widest">
                        <span class="relative flex h-2 w-2">
                            @if($item->status_kegiatan != 'selesai')
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-600"></span>
                            @else
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-slate-400"></span>
                            @endif
                        </span>
                        {{ $item->status_kegiatan == 'selesai' ? 'Agenda Telah Selesai' : 'Agenda Sedang Berjalan' }}
                    </div>
                    
                    <h1 class="text-4xl md:text-5xl font-black text-slate-900 leading-tight tracking-tight uppercase">
                        {{ $item->nama_kegiatan }}
                    </h1>

                    <div class="flex flex-wrap gap-8 py-6 border-y border-slate-50">
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Lokasi Pelaksanaan</p>
                            <p class="text-lg font-black text-slate-800 uppercase italic">{{ $item->lokasi }}</p>
                        </div>
                        <div class="h-10 w-px bg-slate-100 hidden md:block"></div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tanggal</p>
                            <p class="text-lg font-black text-slate-800">{{ date('d M Y', strtotime($item->tanggal_kegiatan)) }}</p>
                        </div>
                    </div>

                    <div class="prose prose-slate max-w-none">
                        <h4 class="text-xs font-black text-slate-400 uppercase tracking-[0.3em] mb-4">Deskripsi Kegiatan</h4>
                        <p class="text-slate-600 text-lg leading-relaxed whitespace-pre-line font-medium italic">
                            "{{ $item->deskripsi_kegiatan }}"
                        </p>
                    </div>

                    {{-- Dokumentasi Section --}}
                    @if($item->status_kegiatan == 'selesai')
                    <div class="pt-10 border-t border-slate-50">
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Dokumentasi Kegiatan
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            @if($item->dokumentasi && is_array($item->dokumentasi))
                                @foreach($item->dokumentasi as $foto)
                                    <div class="rounded-[24px] overflow-hidden shadow-sm h-48 border-2 border-slate-50">
                                        <img src="{{ asset('storage/' . $foto) }}" class="w-full h-full object-cover hover:scale-110 transition-transform duration-700">
                                    </div>
                                @endforeach
                            @else
                                <div class="col-span-2 py-10 text-center bg-slate-50 rounded-[24px] border border-dashed">
                                    <p class="text-slate-400 font-bold italic text-xs uppercase tracking-widest">Belum ada foto diunggah</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Donor List Card --}}
            <div class="bg-white p-10 rounded-[45px] border border-gray-100 shadow-sm border-l-8 border-l-green-700">
                <div class="flex justify-between items-center mb-10">
                    <div>
                        <h3 class="text-2xl font-black text-slate-900 tracking-tight">Daftar Donatur</h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1 italic">Daftar Infaq Terkonfirmasi</p>
                    </div>
                    <div class="w-12 h-12 bg-green-50 text-green-700 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($donatur as $d)
                        <div class="p-5 bg-slate-50/50 rounded-3xl border border-slate-100 flex justify-between items-center group hover:bg-white hover:shadow-md transition-all">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-slate-400 group-hover:bg-green-700 group-hover:text-white transition-all shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2.5" stroke-linecap="round"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-900">{{ $d->nama_donatur }}</p>
                                    <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">
                                        {{ \Carbon\Carbon::parse($d->tanggal_input)->translatedFormat('d M Y') }}
                                    </p>
                                </div>
                            </div>
                            <p class="text-sm font-black text-green-700">Rp{{ number_format($d->nominal, 0, ',', '.') }}</p>
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-16 bg-slate-50 rounded-[30px] border border-dashed">
                            <p class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">Belum ada donatur masuk</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .animate-fadeIn { animation: fadeIn 0.6s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection