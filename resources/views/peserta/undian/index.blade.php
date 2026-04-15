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

    {{-- ================= TABEL HASIL UNDIAN (SESUAI GAMBAR) ================= --}}
    <div class="space-y-10 mb-12 mx-4 md:mx-0">
        @php
            $groupedUndians = $undians->groupBy('tahun_pelaksanaan');
        @endphp

        @forelse($groupedUndians as $tahun => $data)
            <div class="animate-fadeIn">
                {{-- Label Grouping --}}
                <div class="flex items-center gap-4 mb-6 ml-4">
                    <span class="px-5 py-2 bg-slate-100 text-[#147a54] text-[11px] font-black rounded-2xl uppercase tracking-[0.2em] border border-slate-200/50">Periode Qurban {{ $tahun }}</span>
                    <div class="h-[1px] flex-1 bg-gradient-to-r from-gray-200 to-transparent"></div>
                </div>

                <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/30 text-[11px] uppercase tracking-[0.15em] text-slate-400 font-bold border-b border-gray-50">
                                <tr>
                                    <th class="px-10 py-6 text-center w-24">Urutan</th>
                                    <th class="px-6 py-6">Nama Peserta</th>
                                    <th class="px-6 py-6 text-center">Individu/Kelompok</th>
                                    <th class="px-6 py-6 text-center">Skema</th>
                                    <th class="px-10 py-6 text-right">Tgl Diundi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50/50">
                                @foreach($data as $item)
                                @php $isMe = ($item->id_pesertaarisan == $saya->id_pesertaarisan); @endphp
                                <tr class="hover:bg-slate-50/50 transition-all group {{ $isMe ? 'bg-green-50/40' : '' }}">
                                    {{-- Urutan --}}
                                    <td class="px-10 py-6 text-center">
                                        <span class="text-sm font-bold {{ $isMe ? 'text-[#147a54]' : 'text-slate-400' }}">
                                            {{ sprintf("%02d", $item->urutan_pemenang) }}
                                        </span>
                                    </td>
                                    
                                    {{-- Nama Peserta --}}
                                    <td class="px-6 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 {{ $isMe ? 'bg-[#147a54] text-white shadow-lg shadow-green-900/20' : 'bg-slate-100 text-slate-400' }} rounded-xl flex items-center justify-center text-xs font-black uppercase">
                                                {{ substr($item->peserta->nama, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-base font-black text-slate-700 leading-none mb-1.5 uppercase tracking-wide">
                                                    {{ $item->peserta->nama }}
                                                    @if($isMe)
                                                        <span class="ml-2 text-[9px] bg-[#147a54] text-white px-2 py-0.5 rounded-lg tracking-widest font-black">SAYA</span>
                                                    @endif
                                                </p>
                                                <p class="text-[10px] text-slate-400 font-medium tracking-tight line-clamp-1 max-w-[300px]">{{ $item->peserta->alamat }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Info Kelompok --}}
                                    <td class="px-6 py-6 text-center">
                                        @if($item->peserta->id_kelompok)
                                            <span class="px-3 py-1 bg-blue-50 text-blue-500/80 text-[11px] font-bold rounded-lg uppercase italic tracking-wider">Grup: {{ $item->peserta->kelompok->kode_kelompok }}</span>
                                        @else
                                            <span class="text-[11px] text-slate-300 font-bold uppercase tracking-widest italic italic">Perorangan</span>
                                        @endif
                                    </td>

                                    {{-- Skema --}}
                                    <td class="px-6 py-6 text-center">
                                        <span class="text-[10px] font-black text-[#147a54] bg-green-50 px-3 py-1.5 rounded-full border border-green-100 uppercase tracking-tighter">{{ $item->skema->nama_skema }}</span>
                                    </td>

                                    {{-- Tanggal --}}
                                    <td class="px-10 py-6 text-right">
                                        <p class="text-xs font-bold text-slate-400 italic">{{ \Carbon\Carbon::parse($item->tanggal_undian)->translatedFormat('d M Y') }}</p>
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
                <p class="text-gray-400 font-black uppercase text-xs tracking-widest">Belum ada pengumuman pemenang qurban</p>
            </div>
        @endforelse
    </div>
</div>

<style>
    .animate-fadeInUp { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fadeIn { animation: fadeIn 0.5s ease-out; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>
@endsection