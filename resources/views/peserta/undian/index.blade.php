@extends('peserta.layout.app')

@section('content')
<div class="max-w-6xl mx-auto animate-fadeInUp flex flex-col gap-8">
    
    {{-- ================= HEADER SECTION ================= --}}
    <div class="flex justify-between items-end shrink-0 px-4 md:px-0">
        <div>
            <h1 class="text-2xl font-extrabold text-green-900 tracking-tight">Riwayat Pengundian</h1>
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

    <div class="bg-[#064e3b] rounded-[40px] p-8 text-white relative overflow-hidden shadow-xl shadow-green-900/20 transition-all hover:scale-[1.01] mx-4 md:mx-0">
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
                    <p class="text-[10px] font-bold text-gray-400 mt-1 uppercase">Qurban Tahun {{ $undianSaya->tahun_pelaksanaan }}</p>
                @else
                    <p class="text-xl font-black text-orange-500 uppercase tracking-tight">ANTREAN</p>
                    <p class="text-[10px] font-bold text-gray-400 mt-1 italic">Menunggu Pengocokan</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ================= TABEL HASIL UNDIAN ================= --}}
    <div class="space-y-10 mb-12 mx-4 md:mx-0">
        @php
            $groupedUndians = $undians->groupBy('tahun_pelaksanaan');
        @endphp

        @forelse($groupedUndians as $tahun => $data)
            <div class="animate-fadeIn">
                {{-- Label Grouping Periode --}}
                <div class="flex items-center gap-4 mb-4 ml-2">
                    <div class="flex items-center gap-2 px-4 py-2 bg-green-50 border border-green-100 rounded-2xl">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="text-green-800 text-[12px] font-black uppercase tracking-wider">Periode Qurban {{ $tahun }}</span>
                    </div>
                    <div class="h-[1px] flex-1 bg-gradient-to-r from-green-100 to-transparent"></div>
                </div>

                {{-- Table Wrapper --}}
                <div class="bg-white rounded-[30px] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-50/50 border-b border-gray-100">
                                <tr>
                                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Urutan</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Peserta Qurban</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Kategori</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Durasi Skema</th>
                                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Waktu Undi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($data as $item)
                                    @php $isMe = ($item->id_pesertaarisan == $saya->id_pesertaarisan); @endphp
                                    <tr class="group transition-all duration-300 {{ $isMe ? 'bg-green-50/50' : 'hover:bg-slate-50/80' }}">
                                        {{-- Urutan dengan Badge --}}
                                        <td class="px-8 py-5 text-center">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-xs font-black {{ $isMe ? 'bg-green-600 text-white shadow-lg shadow-green-200' : 'bg-slate-100 text-slate-500 group-hover:bg-white transition-colors' }}">
                                                {{ $item->urutan_pemenang }}
                                            </span>
                                        </td>

                                        {{-- Nama & Alamat --}}
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-4">
                                                <div class="relative">
                                                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-sm font-black {{ $isMe ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-400' }} group-hover:scale-110 transition-transform">
                                                        {{ substr($item->peserta->nama, 0, 1) }}
                                                    </div>
                                                    @if($isMe)
                                                        <div class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <p class="text-sm font-black text-slate-700 uppercase tracking-wide">{{ $item->peserta->nama }}</p>
                                                        @if($isMe)
                                                            <span class="text-[8px] bg-green-600 text-white px-2 py-0.5 rounded-md font-black tracking-tighter">SAYA</span>
                                                        @endif
                                                    </div>
                                                    <p class="text-[11px] text-slate-400 mt-0.5 italic line-clamp-1">{{ $item->peserta->alamat }}</p>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Status Kelompok --}}
                                        <td class="px-6 py-5 text-center">
                                            @if($item->peserta->id_kelompok)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-black uppercase tracking-tight border border-blue-100">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                                                    {{ $item->peserta->kelompok->kode_kelompok }}
                                                </span>
                                            @else
                                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Mandiri</span>
                                            @endif
                                        </td>

                                        {{-- Skema Badge --}}
                                        <td class="px-6 py-5 text-center">
                                            <div class="inline-block px-4 py-1.5 bg-slate-50 border border-slate-100 rounded-xl text-[10px] font-black text-slate-600 uppercase tracking-tighter">
                                                {{ $item->skema->nama_skema }}
                                            </div>
                                        </td>

                                        {{-- Tanggal Undi --}}
                                        <td class="px-8 py-5 text-right">
                                            <span class="text-[11px] font-bold text-slate-400 group-hover:text-slate-600 transition-colors">
                                                {{ \Carbon\Carbon::parse($item->tanggal_undian)->translatedFormat('d M Y') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            {{-- Empty State --}}
            <div class="py-20 bg-white rounded-[40px] border-2 border-dashed border-gray-100 text-center flex flex-col items-center justify-center">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                </div>
                <p class="text-slate-400 font-black uppercase text-[10px] tracking-[0.3em]">Data pemenang belum tersedia</p>
            </div>
        @endforelse
    </div>

<style>
    .animate-fadeInUp { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fadeIn { animation: fadeIn 0.5s ease-out; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>
@endsection