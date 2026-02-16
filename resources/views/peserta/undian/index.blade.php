@extends('peserta.layout.app')

@section('content')
<div class="max-w-6xl mx-auto animate-fadeInUp flex flex-col gap-8">
    
    {{-- ================= HEADER SECTION ================= --}}
    <div class="flex justify-between items-end shrink-0">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase leading-none mb-2">Riwayat Pengundian</h1>
            <p class="text-sm text-gray-400 font-medium italic">Daftar pemenang qurban Masjid Nurul Huda per periode</p>
        </div>
        <div class="hidden md:flex items-center gap-3 bg-white px-5 py-3 rounded-2xl shadow-sm border border-gray-100">
            <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Update Otomatis oleh Sistem</span>
        </div>
    </div>

    {{-- ================= STATUS CARD (INFO DIRI) ================= --}}
    @php 
        $saya = auth()->user()->peserta;
        $undianSaya = \App\Models\UndianArisan::where('id_pesertaarisan', $saya->id_pesertaarisan)->first();
    @endphp

    <div class="bg-[#064e3b] rounded-[40px] p-8 text-white relative overflow-hidden shadow-xl shadow-green-900/20 transition-all hover:scale-[1.01]">
        <svg class="absolute -right-10 -bottom-10 w-64 h-64 text-white opacity-5 rotate-12" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 bg-white/10 backdrop-blur-md rounded-3xl flex items-center justify-center border border-white/20">
                    <svg class="w-10 h-10 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <h2 class="text-2xl font-black uppercase tracking-tight">{{ $saya->nama }}</h2>
                    <p class="text-green-300/80 font-bold text-xs uppercase tracking-[0.2em] mb-2">{{ $saya->skemaArisan->nama_skema }}</p>
                    @if($saya->id_kelompok)
                        <span class="px-3 py-1 bg-white/10 rounded-full text-[10px] font-black uppercase tracking-tighter border border-white/20">Grup: {{ $saya->kelompok->kode_kelompok }}</span>
                    @endif
                </div>
            </div>

            <div class="px-8 py-5 bg-white rounded-3xl text-center min-w-[200px] shadow-2xl">
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-2">Status Undian Anda</p>
                @if($undianSaya)
                    <p class="text-xl font-black text-green-700 uppercase tracking-tight">TERPILIH</p>
                    <p class="text-[10px] font-bold text-gray-400 mt-1">Tahun Ke-{{ $undianSaya->tahun_ke }}</p>
                @else
                    <p class="text-xl font-black text-orange-500 uppercase tracking-tight">ANTREAN</p>
                    <p class="text-[10px] font-bold text-gray-400 mt-1 italic italic">Menunggu Pengocokan</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ================= TABEL HASIL UNDIAN (GROUPED BY YEAR) ================= --}}
    <div class="space-y-10 mb-12">
        @php
            $undians = \App\Models\UndianArisan::with(['peserta.kelompok', 'skema'])
                        ->orderBy('tahun_ke', 'desc')
                        ->orderBy('urutan_pemenang', 'asc')
                        ->get()
                        ->groupBy('tahun_ke');
        @endphp

        @forelse($undians as $tahun => $data)
            <div class="animate-fadeIn">
                <div class="flex items-center gap-4 mb-4 ml-4">
                    <span class="px-4 py-1.5 bg-slate-100 text-slate-500 text-[11px] font-black rounded-full uppercase tracking-[0.2em]">Pemenang Qurban Tahun Ke-{{ $tahun }}</span>
                    <div class="h-[1px] flex-1 bg-gray-200"></div>
                </div>

                <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/50 text-[10px] uppercase tracking-widest text-gray-400 font-black border-b border-gray-100">
                                <tr>
                                    <th class="px-8 py-5 text-center">No</th>
                                    <th class="px-6 py-5">Nama Peserta</th>
                                    <th class="px-6 py-5 text-center">Info Kelompok</th>
                                    <th class="px-6 py-5 text-center">Skema</th>
                                    <th class="px-8 py-5 text-right">Tanggal Undi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($data as $index => $item)
                                <tr class="hover:bg-green-50/20 transition-all group {{ $item->id_pesertaarisan == $saya->id_pesertaarisan ? 'bg-green-50/50' : '' }}">
                                    <td class="px-8 py-4 text-center">
                                        <span class="text-xs font-black {{ $item->id_pesertaarisan == $saya->id_pesertaarisan ? 'text-green-600' : 'text-slate-300' }}">
                                            {{ sprintf("%02d", $item->urutan_pemenang) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-slate-100 text-slate-500 rounded-lg flex items-center justify-center text-[10px] font-black uppercase {{ $item->id_pesertaarisan == $saya->id_pesertaarisan ? 'bg-green-600 text-white shadow-lg' : '' }}">
                                                {{ substr($item->peserta->nama, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-black text-slate-800 uppercase leading-tight">
                                                    {{ $item->peserta->nama }}
                                                    @if($item->id_pesertaarisan == $saya->id_pesertaarisan)
                                                        <span class="ml-2 text-[9px] bg-green-600 text-white px-2 py-0.5 rounded-full">SAYA</span>
                                                    @endif
                                                </p>
                                                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter">{{ $item->peserta->alamat }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($item->peserta->id_kelompok)
                                            <span class="px-3 py-1 bg-blue-50 text-blue-600 text-[10px] font-black rounded-lg border border-blue-100 uppercase">{{ $item->peserta->kelompok->kode_kelompok }}</span>
                                        @else
                                            <span class="text-[10px] text-gray-300 font-bold uppercase tracking-widest italic">Perorangan</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-[10px] font-black text-green-700 uppercase tracking-tighter bg-green-50 px-3 py-1 rounded-full border border-green-100">{{ $item->skema->nama_skema }}</span>
                                    </td>
                                    <td class="px-8 py-4 text-right">
                                        <p class="text-[11px] font-bold text-slate-600 leading-none">{{ \Carbon\Carbon::parse($item->tanggal_undian)->format('d M Y') }}</p>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="py-24 bg-white rounded-[40px] border border-dashed border-gray-200 text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-gray-400 font-black uppercase text-xs tracking-widest">Belum ada riwayat pengundian</p>
            </div>
        @endforelse
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .animate-fadeInUp { animation: fadeInUp 0.5s ease-out; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fadeIn { animation: fadeIn 0.4s ease-out; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>
@endsection