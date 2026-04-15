@extends('admin.layout.app')

@section('content')
<div class="max-w-7xl mx-auto animate-fadeIn space-y-6">
    
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-end gap-4">
        <div>
            <h1 class="text-2xl font-black text-green-900 leading-tight tracking-tight uppercase">Laporan Pengeluaran</h1>
            <p class="text-sm text-gray-400 font-medium italic">Realisasi Hewan Qurban Masjid Nurul Huda</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            {{-- FITUR FILTER TAHUN --}}
            <form action="{{ route('admin.pengeluaran.index') }}" method="GET" id="filterForm" class="flex items-center gap-2">
                <div class="relative">
                    <select name="tahun" onchange="document.getElementById('filterForm').submit()" 
                        class="bg-white border border-gray-200 text-[10px] font-black uppercase tracking-widest rounded-2xl px-5 py-3 pr-10 shadow-sm focus:ring-2 focus:ring-green-800 outline-none cursor-pointer appearance-none transition-all">
                        <option value="">SEMUA TAHUN</option>
                        @foreach($daftarTahun as $t)
                            <option value="{{ $t }}" {{ $filterTahun == $t ? 'selected' : '' }}>TAHUN {{ $t }}</option>
                        @endforeach
                    </select>
                    <div class="absolute right-4 top-3.5 pointer-events-none text-gray-400">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </form>

            {{-- TOMBOL CETAK --}}
            <a href="{{ route('admin.pengeluaran.pdf', ['tahun' => $filterTahun]) }}" 
                class="bg-green-800 hover:bg-green-900 text-white px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-green-900/20 transition-all flex items-center gap-2 group">
                <svg class="w-4 h-4 transform group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Cetak {{ $filterTahun ?? 'Semua' }}
            </a>
        </div>
    </div>

    {{-- Statistik Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-[32px] border border-gray-100 shadow-sm">
            <p class="text-[9px] font-black text-gray-400 uppercase mb-1 tracking-widest">Total Iuran</p>
            <h3 class="text-xl font-black text-green-700">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-white p-5 rounded-[32px] border border-gray-100 shadow-sm">
            <p class="text-[9px] font-black text-red-400 uppercase mb-1 tracking-widest">Dana Keluar</p>
            <h3 class="text-xl font-black text-slate-800">- Rp {{ number_format($totalKeluarGlobal, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-green-50 p-5 rounded-[32px] border border-green-100 shadow-sm">
            <p class="text-[9px] font-black text-green-600 uppercase mb-1 tracking-widest">Sisa Saldo Kas</p>
            <h3 class="text-xl font-black text-green-900">Rp {{ number_format($saldoKasSaatIni, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-white p-5 rounded-[32px] border border-gray-100 shadow-sm">
            <p class="text-[9px] font-black text-gray-400 uppercase mb-1 tracking-widest">Record</p>
            <h3 class="text-xl font-black text-slate-800">{{ $pengeluarans->total() }} Item</h3>
        </div>
    </div>

    {{-- Data Section --}}
    <div class="space-y-10">
        @php
            $groupedByYear = $pengeluarans->groupBy(function($item) {
                return \Carbon\Carbon::parse($item->tanggal_pengeluaran)->format('Y');
            });
            $rowNumber = $pengeluarans->firstItem();
        @endphp

        @forelse($groupedByYear as $tahun => $dataTahun)
            <div class="space-y-4">
                {{-- Header Grup Tahun --}}
                <div class="flex items-center gap-4 ml-4">
                    <span class="px-5 py-2 bg-green-800 text-white text-[11px] font-black rounded-2xl uppercase tracking-[0.3em] shadow-lg shadow-green-900/20">
                        TAHUN PELAKSANAAN {{ $tahun }}
                    </span>
                    <div class="h-px flex-1 bg-gradient-to-r from-green-200 to-transparent"></div>
                </div>

                @php
                    $groups = $dataTahun->groupBy(function($item) {
                        return $item->undian->peserta->id_kelompok ?? 'perorangan';
                    });
                @endphp

                <div class="grid grid-cols-1 gap-6">
                    @foreach($groups as $groupId => $items)
                        <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-8 py-4 {{ $groupId === 'perorangan' ? 'bg-gray-50/50' : 'bg-blue-50/30' }} border-b border-gray-100 flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 {{ $groupId === 'perorangan' ? 'bg-slate-400' : 'bg-blue-600' }} text-white rounded-lg flex items-center justify-center shadow-md">
                                        @if($groupId === 'perorangan')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="text-xs font-black {{ $groupId === 'perorangan' ? 'text-slate-700' : 'text-blue-900' }} uppercase tracking-widest">
                                            {{ $groupId === 'perorangan' ? 'Peserta Perorangan' : 'Kelompok: ' . $items->first()->undian->peserta->kelompok->nama_kelompok }}
                                        </h3>
                                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter">
                                            Skema: {{ $items->first()->undian->skema->nama_skema }}
                                        </p>
                                    </div>
                                </div>
                                {{-- TOTAL REALISASI (FIX: Menggunakan data Global agar tidak kepotong pagination) --}}
                                <div class="text-right">
                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Total Realisasi</p>
                                    <p class="text-sm font-black {{ $groupId === 'perorangan' ? 'text-slate-800' : 'text-blue-700' }}">
                                        @php
                                            $totalGrup = $allPengeluarans->filter(function($ap) use ($groupId, $tahun) {
                                                $apYear = \Carbon\Carbon::parse($ap->tanggal_pengeluaran)->format('Y');
                                                $apGroupId = $ap->undian->peserta->id_kelompok ?? 'perorangan';
                                                return $apGroupId == $groupId && $apYear == $tahun;
                                            })->sum('nominal');
                                        @endphp
                                        Rp {{ number_format($totalGrup, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead class="bg-white text-[9px] uppercase tracking-[0.2em] text-gray-400 font-black border-b border-gray-100">
                                        <tr>
                                            <th class="px-8 py-4 text-center" width="10%">No</th>
                                            <th class="px-6 py-4">Nama Peserta</th>
                                            <th class="px-6 py-4 text-center">Nominal Per Orang</th>
                                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Tahun Undian</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @foreach($items as $p)
                                            <tr class="hover:bg-green-50/20 transition-all group">
                                                <td class="px-8 py-4 text-center">
                                                    <span class="text-[10px] font-black text-slate-300">{{ str_pad($rowNumber++, 2, '0', STR_PAD_LEFT) }}</span>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <p class="text-xs font-black text-slate-800 uppercase">{{ $p->undian->peserta->nama }}</p>
                                                    <p class="text-[9px] text-gray-400 font-medium italic mt-0.5">{{ \Carbon\Carbon::parse($p->tanggal_pengeluaran)->translatedFormat('d M Y') }}</p>
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    <span class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-[10px] font-black italic shadow-sm border border-red-100">
                                                        Rp {{ number_format($p->nominal, 0, ',', '.') }}
                                                    </span>
                                                </td>
                                                <td class="px-8 py-6 text-center">
                                                    <div class="inline-block {{ $loop->parent->iteration % 2 == 0 ? 'bg-slate-100 text-slate-600' : 'bg-green-100 text-green-700' }} px-4 py-1.5 rounded-[12px] text-[10px] font-black uppercase tracking-widest border border-black/5 shadow-sm">
                                                        Undian {{ $p->undian->tahun_pelaksanaan ?? '-' }}
                                                    </div>
                                                    <p class="text-[8px] font-bold text-gray-300 uppercase mt-1.5 tracking-widest">ID: {{ $p->order_id }}</p>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="py-24 bg-white rounded-[40px] border border-dashed border-gray-200 text-center">
                <div class="flex flex-col items-center">
                    <div class="w-20 h-20 bg-gray-50 rounded-[32px] flex items-center justify-center mb-6 text-gray-200">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                    </div>
                    <h3 class="text-base font-black text-gray-400 uppercase tracking-[0.2em]">Data Tidak Ditemukan</h3>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination (FIX: Tambahkan appends agar filter tahun tetap terbawa) --}}
    @if($pengeluarans->hasPages())
        <div class="mt-10 px-8 py-6 bg-white rounded-[32px] border border-gray-100 shadow-sm">
            {{ $pengeluarans->appends(['tahun' => $filterTahun])->links() }}
        </div>
    @endif
</div>

<style>
    .animate-fadeIn { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection