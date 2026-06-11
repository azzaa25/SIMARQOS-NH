@extends('admin.layout.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* 1. STYLING PAGINATION CUSTOM SESUAI PERMINTAAN */
    .custom-pagination nav p { display: none !important; }
    .custom-pagination nav > div:first-child { display: none !important; }
    .custom-pagination nav div:last-child { flex-direction: row !important; gap: 0.25rem; }
    .custom-pagination a, 
    .custom-pagination span[aria-current="page"] span,
    .custom-pagination span[aria-disabled="true"] span {
        display: flex !important; align-items: center; justify-content: center;
        width: 36px; height: 36px; border-radius: 12px !important;
        font-size: 11px !important; font-weight: 800 !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #e2e8f0 !important; background: white !important;
        color: #64748b !important; box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        text-decoration: none !important;
    }
    .custom-pagination span[aria-current="page"] span {
        background: #064e3b !important; color: white !important;
        border-color: #064e3b !important; box-shadow: 0 4px 12px rgba(6, 78, 59, 0.2);
    }
    .custom-pagination a:hover {
        border-color: #064e3b !important; color: #064e3b !important;
        background: #f0fdf4 !important; transform: translateY(-2px);
    }
    .custom-pagination svg { width: 16px !important; height: 16px !important; stroke-width: 3; }

    /* 2. ANIMASI */
    .animate-fadeIn { animation: fadeIn 0.4s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="max-w-7xl mx-auto animate-fadeInUp flex flex-col gap-8">
    
    {{-- ================= HEADER SECTION ================= --}}
    <div class="flex justify-between items-end shrink-0">
        <div>
            <h1 class="text-2xl font-bold text-green-900 leading-tight">Sistem Undian</h1>
            <p class="text-sm text-gray-400 font-medium italic">Manajemen otomatis urutan pemenang qurban Masjid Nurul Huda</p>
        </div>
        <button onclick="document.getElementById('modalUndian').classList.remove('hidden')" 
            class="px-8 py-4 bg-[#b38b2d] hover:bg-[#967526] text-white rounded-[20px] shadow-lg shadow-yellow-900/20 transition-all flex items-center gap-3 active:scale-95 group">
            <svg class="w-5 h-5 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <span class="text-xs font-black uppercase tracking-[0.1em]">Lakukan Undian Baru</span>
        </button>
    </div>

    {{-- ================= STATS SECTION ================= --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 shrink-0">
        <div class="bg-white rounded-[32px] p-6 shadow-sm border-l-8 border-green-600 flex items-center gap-5 transition-all hover:shadow-md">
            <div class="w-14 h-14 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center shadow-inner">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Total Anggota</p>
                <p class="text-2xl font-black text-slate-800">{{ $totalPeserta }} <span class="text-xs font-bold text-gray-300 italic">Peserta</span></p>
            </div>
        </div>

        <div class="bg-white rounded-[32px] p-6 shadow-sm border-l-8 border-orange-500 flex items-center gap-5 transition-all hover:shadow-md">
            <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center shadow-inner">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Belum Menang</p>
                <p class="text-2xl font-black text-slate-800">{{ $menunggu }} <span class="text-xs font-bold text-gray-300 italic">Antrean</span></p>
            </div>
        </div>

        <div class="bg-white rounded-[32px] p-6 shadow-sm border-l-8 border-[#147a54] flex items-center gap-5 transition-all hover:shadow-md">
            <div class="w-14 h-14 bg-green-50 text-[#147a54] rounded-2xl flex items-center justify-center shadow-inner">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Sudah Menang</p>
                <p class="text-2xl font-black text-slate-800">{{ $selesai }} <span class="text-xs font-bold text-gray-300 italic">Total</span></p>
            </div>
        </div>
    </div>

    {{-- ================= TAB NAVIGATION ================= --}}
    <div class="flex gap-4 border-b border-gray-100 shrink-0">
        <button onclick="switchTab('pemenang')" id="btn-pemenang" class="pb-4 px-6 text-xs font-black uppercase tracking-widest border-b-4 border-green-700 text-green-700 transition-all">Daftar Pemenang</button>
        <button onclick="switchTab('antrean')" id="btn-antrean" class="pb-4 px-6 text-xs font-black uppercase tracking-widest border-b-4 border-transparent text-gray-400 hover:text-slate-600 transition-all">Belum Menang (Antrean)</button>
    </div>

    {{-- ================= TAB PEMENANG (GROUPED BY YEAR & SKEMA) ================= --}}
    <div id="tab-pemenang" class="space-y-12 mb-10">

        @forelse($undians->groupBy('tahun_pelaksanaan') as $tahun => $dataTahun)

            <div class="animate-fadeIn">

                {{-- YEAR HEADER --}}
                <div class="flex items-center gap-4 mb-8 ml-4">
                    <span class="px-5 py-2 bg-[#147a54] text-white text-[12px] font-black rounded-2xl uppercase tracking-[0.2em] shadow-lg shadow-green-900/20">
                        Periode Qurban Tahun {{ $tahun }}
                    </span>
                    <div class="h-[2px] flex-1 bg-gradient-to-r from-green-100 to-transparent"></div>
                </div>

                {{-- GROUP PER SKEMA --}}
                @foreach($dataTahun->groupBy('id_skema') as $idSkema => $dataSkema)

                    <div class="mb-8 bg-white rounded-[32px] shadow-sm border border-green-100 overflow-hidden">

                        {{-- HEADER SKEMA --}}
                        <div class="px-8 py-5 bg-green-50 border-b border-green-100 flex justify-between items-center">
                            <div>
                                <h3 class="text-sm font-black text-green-900 uppercase tracking-widest">
                                    {{ $dataSkema->first()->skema->nama_skema }}
                                </h3>
                                <p class="text-[10px] text-green-500 font-bold uppercase tracking-[0.15em] mt-1">
                                    {{ strtoupper($dataSkema->first()->skema->tipe_skema) }}
                                </p>
                            </div>

                            <span class="px-4 py-2 bg-white border border-green-200 rounded-xl text-[10px] font-black text-green-700 uppercase">
                                {{ $dataSkema->count() }} Pemenang
                            </span>
                        </div>

                        @php
                            $pemenangKelompok = $dataSkema->filter(fn($item) => $item->peserta->id_kelompok != null);
                            $pemenangIndividu = $dataSkema->filter(fn($item) => $item->peserta->id_kelompok == null);
                        @endphp

                        {{-- ================= KATEGORI KELOMPOK ================= --}}
                        @if($pemenangKelompok->count() > 0)
                            <div class="border-b border-gray-100">
                                <div class="px-8 py-4 bg-blue-50/40">
                                    <h4 class="text-xs font-black text-blue-700 uppercase tracking-widest">
                                        Kategori Kelompok
                                    </h4>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="w-full text-left">
                                        <thead class="bg-white text-[9px] uppercase tracking-[0.2em] text-gray-400 font-black border-b border-gray-50">
                                            <tr>
                                                <th class="px-8 py-4">Urutan</th>
                                                <th class="px-6 py-4">Nama Peserta</th>
                                                <th class="px-6 py-4 text-center">Kelompok</th>
                                                <th class="px-8 py-4 text-right">Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50">
                                            @foreach($pemenangKelompok as $item)
                                                <tr class="hover:bg-blue-50/20 transition-all">
                                                    <td class="px-8 py-5">
                                                        <span class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-sm font-black">
                                                            {{ sprintf("%02d", $item->urutan_pemenang) }}
                                                        </span>
                                                    </td>

                                                    <td class="px-6 py-5">
                                                        <p class="font-extrabold text-slate-900 text-sm uppercase">
                                                            {{ $item->peserta->nama }}
                                                        </p>
                                                        <p class="text-xs text-gray-500 italic">
                                                            {{ $item->peserta->alamat }}
                                                        </p>
                                                    </td>

                                                    <td class="px-6 py-5 text-center">
                                                        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-[10px] font-black rounded-full">
                                                            {{ $item->peserta->kelompok->kode_kelompok }}
                                                        </span>
                                                    </td>

                                                    <td class="px-8 py-5 text-right text-sm font-bold text-slate-700">
                                                        {{ \Carbon\Carbon::parse($item->tanggal_undian)->translatedFormat('d M Y') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        {{-- ================= KATEGORI PERORANGAN ================= --}}
                        @if($pemenangIndividu->count() > 0)
                            <div>
                                <div class="px-8 py-4 bg-slate-50/40">
                                    <h4 class="text-xs font-black text-slate-700 uppercase tracking-widest">
                                        Kategori Perorangan
                                    </h4>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="w-full text-left">
                                        <thead class="bg-white text-[9px] uppercase tracking-[0.2em] text-gray-400 font-black border-b border-gray-50">
                                            <tr>
                                                <th class="px-8 py-4">Urutan</th>
                                                <th class="px-6 py-4">Nama Peserta</th>
                                                <th class="px-6 py-4 text-center">Jenis</th>
                                                <th class="px-8 py-4 text-right">Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50">
                                            @foreach($pemenangIndividu as $item)
                                                <tr class="hover:bg-green-50/20 transition-all">
                                                    <td class="px-8 py-5">
                                                        <span class="w-10 h-10 rounded-xl bg-green-100 text-green-700 flex items-center justify-center text-sm font-black">
                                                            {{ sprintf("%02d", $item->urutan_pemenang) }}
                                                        </span>
                                                    </td>

                                                    <td class="px-6 py-5">
                                                        <p class="font-extrabold text-slate-900 text-sm uppercase">
                                                            {{ $item->peserta->nama }}
                                                        </p>
                                                        <p class="text-xs text-gray-500 italic">
                                                            {{ $item->peserta->alamat }}
                                                        </p>
                                                    </td>

                                                    <td class="px-6 py-5 text-center">
                                                        <span class="px-3 py-1 bg-slate-100 text-slate-700 text-[10px] font-black rounded-full">
                                                            PERORANGAN
                                                        </span>
                                                    </td>

                                                    <td class="px-8 py-5 text-right text-sm font-bold text-slate-700">
                                                        {{ \Carbon\Carbon::parse($item->tanggal_undian)->translatedFormat('d M Y') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>
        @empty
            <div class="py-24 bg-white rounded-[40px] border border-dashed border-gray-200 text-center">
                <p class="text-gray-400 font-black uppercase text-xs tracking-widest">
                    Belum ada riwayat pengundian
                </p>
            </div>
        @endforelse
        {{-- PAGINATION TAB PEMENANG --}}
        <div class="px-8 py-6 bg-white border border-gray-100 rounded-[24px] flex flex-col md:flex-row items-center justify-between gap-4 shadow-sm">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                Pemenang {{ $undians->firstItem() ?? 0 }} - {{ $undians->lastItem() ?? 0 }} dari {{ $undians->total() }}
            </div>
            <div class="custom-pagination">
                {{ $undians->appends(['page_pemenang' => request('page_pemenang')])->links() }}
            </div>
        </div>
    </div>
    {{-- ================= TAB ANTREAN (GROUPED BERDASARKAN STRUKTUR AWAL) ================= --}}
    <div id="tab-antrean" class="hidden space-y-8 mb-10 animate-fadeIn">
        
        {{-- FILTER SKEMA (SERVER SIDE) --}}
        <div class="flex items-center gap-3 bg-white p-3 rounded-2xl border border-gray-100 shadow-sm w-fit mb-6 ml-1">
            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest pl-2">Filter Skema:</label>
            <select id="filterSkemaAntrean" onchange="filterSkemaAntreanServer(this.value)" class="bg-gray-50 border-none text-xs font-bold rounded-xl focus:ring-[#147a54] cursor-pointer">
                <option value="all" {{ request('filter_skema') == 'all' || !request('filter_skema') ? 'selected' : '' }}>Semua Paket Arisan</option>
                @foreach($skemas as $s)
                    <option value="skema-{{ $s->id_skema }}" {{ request('filter_skema') == 'skema-'.$s->id_skema ? 'selected' : '' }}>{{ $s->nama_skema }}</option>
                @endforeach
            </select>
        </div>

        @php
            // Grouping tingkat 1: Berdasarkan ID Skema Paket
            $antreanGroupedBySkema = $antreanPaginator->groupBy('id_skema');
        @endphp

        @forelse($antreanGroupedBySkema as $idSkema => $membersInSkema)
            <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 overflow-hidden mb-8">
                
                {{-- HEADER SKEMA ANTREAN --}}
                <div class="px-8 py-5 bg-gray-50/50 border-b border-gray-100 flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-emerald-600 text-white rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight">
                                Paket: {{ $membersInSkema->first()->skemaArisan->nama_skema }}
                            </h3>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                                Total Halaman Ini: {{ $membersInSkema->count() }} Orang Antrean
                            </p>
                        </div>
                    </div>
                </div>

                @php
                    // Pisahkan data Kelompok dan Perorangan
                    $antreanKelompokOnly = $membersInSkema->filter(fn($item) => $item->id_kelompok != null);
                    $antreanPeroranganOnly = $membersInSkema->filter(fn($item) => $item->id_kelompok == null);
                    
                    // Grouping tingkat 2: Kelompok disatukan berdasarkan id_kelompok masing-masing
                    $antreanKelompokGrouped = $antreanKelompokOnly->groupBy('id_kelompok');
                @endphp

                {{-- ================= SEPARATOR KATEGORI KELOMPOK ANTREAN (TER-ISOLASI PER KELOMPOK) ================= --}}
                @if($antreanKelompokGrouped->count() > 0)
                    <div class="border-b border-gray-100">
                        <div class="px-8 py-4 bg-blue-50/40 border-b border-blue-100">
                            <h4 class="text-xs font-black text-blue-700 uppercase tracking-widest">Kategori Kelompok</h4>
                        </div>

                        {{-- Loop masing-masing kelompok agar disatukan per kelompoknya sendiri --}}
                        @foreach($antreanKelompokGrouped as $idKelompok => $pesertaKelompok)
                            <div class="p-6 bg-gradient-to-b from-blue-50/20 to-transparent border-b border-gray-100 last:border-b-0">
                                {{-- Label Badge Nama/Kode Kelompok Spesifik --}}
                                <div class="mb-3 flex items-center gap-2">
                                    <span class="px-3 py-1 bg-blue-600 text-white text-[10px] font-black rounded-lg uppercase tracking-wider shadow-sm">
                                        Kelompok: {{ $pesertaKelompok->first()->kelompok->nama_kelompok }}
                                    </span>
                                    <span class="text-[10px] text-gray-400 font-medium italic">({{ $pesertaKelompok->count() }} Anggota dalam antrean page ini)</span>
                                </div>

                                <div class="overflow-x-auto rounded-2xl border border-blue-100/60 bg-white shadow-sm">
                                    <table class="w-full text-left">
                                        <thead class="bg-blue-50/30 text-[9px] uppercase tracking-[0.2em] text-blue-500 font-black border-b border-blue-50">
                                            <tr>
                                                <th class="px-6 py-3.5">Nama Peserta</th>
                                                <th class="px-6 py-3.5 text-center">Progress Iuran</th>
                                                <th class="px-6 py-3.5 text-center">Status Pelunasan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50">
                                            @foreach($pesertaKelompok as $a)
                                                @php
                                                    $targetBulan = $a->skemaArisan->durasi_bulan ?? 12;
                                                    $sudahBayar = $a->total_iuran_sukses ?? 0;
                                                    $isLunas = $sudahBayar >= $targetBulan;
                                                @endphp
                                                <tr class="hover:bg-blue-50/5 transition-all">
                                                    <td class="px-6 py-4">
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-9 h-9 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xs font-black uppercase border border-blue-100">
                                                                {{ substr($a->nama, 0, 1) }}
                                                            </div>
                                                            <div>
                                                                <p class="font-extrabold text-slate-900 text-sm uppercase tracking-tight">{{ $a->nama }}</p>
                                                                <p class="text-[10px] text-gray-400 font-bold mt-0.5">ID #{{ $a->id_pesertaarisan }} | {{ $a->no_hp }}</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <div class="flex flex-col items-center gap-1">
                                                            <div class="flex justify-between w-24">
                                                                <span class="text-[10px] font-black {{ $isLunas ? 'text-green-600' : 'text-orange-500' }}">
                                                                    {{ $sudahBayar }} / {{ $targetBulan }} Bln
                                                                </span>
                                                                <span class="text-[10px] font-bold text-gray-400">{{ round(($sudahBayar/max($targetBulan,1))*100) }}%</span>
                                                            </div>
                                                            <div class="w-24 bg-gray-100 rounded-full h-1.5 border border-gray-200 overflow-hidden">
                                                                <div class="{{ $isLunas ? 'bg-green-500' : 'bg-orange-400' }} h-full transition-all duration-500" style="width: {{ ($sudahBayar/max($targetBulan,1))*100 }}%"></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 text-center">
                                                        @if($isLunas)
                                                            <span class="px-3 py-1 bg-green-100 text-green-700 text-[10px] font-black rounded-lg border border-green-200 uppercase tracking-wider inline-flex items-center gap-1">Lunas</span>
                                                        @else
                                                            <span class="px-3 py-1 bg-red-50 text-red-600 text-[10px] font-black rounded-lg border border-red-100 uppercase tracking-wider inline-flex items-center gap-1">Belum Lunas</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- ================= SEPARATOR KATEGORI PERORANGAN ANTREAN ================= --}}
                @if($antreanPeroranganOnly->count() > 0)
                    <div>
                        <div class="px-8 py-4 bg-slate-50/40 border-b border-gray-100">
                            <h4 class="text-xs font-black text-slate-700 uppercase tracking-widest">Kategori Perorangan</h4>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-white text-[9px] uppercase tracking-[0.2em] text-gray-400 font-black border-b border-gray-50">
                                    <tr>
                                        <th class="px-8 py-4">Nama Peserta</th>
                                        <th class="px-6 py-4 text-center">Jenis</th>
                                        <th class="px-6 py-4 text-center">Progress Iuran</th>
                                        <th class="px-8 py-4 text-center">Status Pelunasan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($antreanPeroranganOnly as $a)
                                        @php
                                            $targetBulan = $a->skemaArisan->durasi_bulan ?? 12;
                                            $sudahBayar = $a->total_iuran_sukses ?? 0;
                                            $isLunas = $sudahBayar >= $targetBulan;
                                        @endphp
                                        <tr class="hover:bg-green-50/10 transition-all">
                                            <td class="px-8 py-5">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-11 h-11 bg-slate-100 text-slate-500 rounded-2xl flex items-center justify-center text-sm font-black uppercase border border-gray-200">
                                                        {{ substr($a->nama, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <p class="font-extrabold text-slate-900 text-sm uppercase tracking-tight">{{ $a->nama }}</p>
                                                        <p class="text-[10px] text-gray-400 font-bold mt-0.5">ID #{{ $a->id_pesertaarisan }} | {{ $a->no_hp }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-5 text-center">
                                                <span class="px-3 py-1 bg-slate-100 text-slate-700 text-[10px] font-black rounded-full">PERORANGAN</span>
                                            </td>
                                            <td class="px-6 py-5">
                                                <div class="flex flex-col items-center gap-1.5">
                                                    <div class="flex justify-between w-24">
                                                        <span class="text-[10px] font-black {{ $isLunas ? 'text-green-600' : 'text-orange-500' }}">
                                                            {{ $sudahBayar }}/{{ $targetBulan }} Bulan
                                                        </span>
                                                        <span class="text-[10px] font-bold text-gray-400">{{ round(($sudahBayar/max($targetBulan,1))*100) }}%</span>
                                                    </div>
                                                    <div class="w-24 bg-gray-100 rounded-full h-1.5 border border-gray-200 overflow-hidden">
                                                        <div class="{{ $isLunas ? 'bg-green-500' : 'bg-orange-400' }} h-full transition-all duration-500" style="width: {{ ($sudahBayar/max($targetBulan,1))*100 }}%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-8 py-5 text-center">
                                                @if($isLunas)
                                                    <span class="px-4 py-1.5 bg-green-100 text-green-700 text-[10px] font-black rounded-xl border border-green-200 uppercase tracking-widest inline-flex items-center gap-1.5">Lunas</span>
                                                @else
                                                    <span class="px-4 py-1.5 bg-red-50 text-red-600 text-[10px] font-black rounded-xl border border-red-100 uppercase tracking-widest inline-flex items-center gap-1.5">Belum Lunas</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

            </div>
        @empty
            <div class="py-24 bg-white rounded-[40px] border border-dashed border-gray-200 text-center">
                <p class="text-gray-400 font-black uppercase text-xs tracking-widest">Tidak ada data antrean pada skema ini</p>
            </div>
        @endforelse

        {{-- PAGINATION TAB ANTREAN --}}
        <div class="px-8 py-6 bg-white border border-gray-100 rounded-[24px] flex flex-col md:flex-row items-center justify-between gap-4 shadow-sm">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                Antrean {{ $antreanPaginator->firstItem() ?? 0 }} - {{ $antreanPaginator->lastItem() ?? 0 }} dari {{ $antreanPaginator->total() }}
            </div>
            <div class="custom-pagination">
                {{ $antreanPaginator->appends(['filter_skema' => request('filter_skema')])->links() }}
            </div>
        </div>
    </div>
</div>

{{-- ================= MODAL PILIH SKEMA ================= --}}
<div id="modalUndian" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
    <div class="bg-white rounded-[40px] p-10 w-full max-w-md shadow-2xl animate-zoomIn relative">
        <button onclick="document.getElementById('modalUndian').classList.add('hidden')" class="absolute top-6 right-6 text-gray-300 hover:text-red-500 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-yellow-50 text-yellow-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <h3 class="text-2xl font-black text-gray-800 uppercase tracking-tight">Mulai Undian</h3>
            <p class="text-[11px] text-gray-400 font-bold uppercase tracking-widest mt-1">Sistem akan mengacak pemenang untuk periode {{ date('Y') }}</p>
        </div>
        
        <form id="formProsesUndian" action="{{ route('admin.undian.proses') }}" method="POST">
            @csrf
            <div class="space-y-4 mb-8">
                <label class="text-[10px] font-black text-gray-400 uppercase ml-1 tracking-[0.2em]">Pilih Paket Arisan</label>
                <div class="relative">
                    <select name="id_skema" id="id_skema" required class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-black text-gray-700 outline-none focus:ring-4 focus:ring-green-500/10 focus:border-green-600 appearance-none cursor-pointer">
                        <option value="" disabled selected>-- DAFTAR PAKET AKTIF --</option>
                        @foreach($skemas as $s)
                            <option value="{{ $s->id_skema }}">{{ strtoupper($s->nama_skema) }} ({{ $s->durasi_bulan }} BULAN)</option>
                        @endforeach
                    </select>
                    <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>
            
            <button type="button" onclick="triggerUndianAnimation()" 
                class="w-full bg-[#147a54] hover:bg-[#064e3b] text-white py-5 rounded-[20px] font-black text-[11px] uppercase tracking-[0.2em] shadow-xl shadow-green-900/20 transition-all active:scale-95">
                Acak Pemenang Baru
            </button>
        </form>
    </div>
</div>

<script>
    function switchTab(tab) {
        const tabPemenang = document.getElementById('tab-pemenang');
        const tabAntrean = document.getElementById('tab-antrean');
        const btnPemenang = document.getElementById('btn-pemenang');
        const btnAntrean = document.getElementById('btn-antrean');

        localStorage.setItem('active_undian_tab', tab);

        if (tab === 'pemenang') {
            tabPemenang.classList.remove('hidden');
            tabAntrean.classList.add('hidden');
            btnPemenang.className = "pb-4 px-6 text-xs font-black uppercase tracking-widest border-b-4 border-green-700 text-green-700 transition-all";
            btnAntrean.className = "pb-4 px-6 text-xs font-black uppercase tracking-widest border-b-4 border-transparent text-gray-400 hover:text-slate-600 transition-all";
        } else {
            tabPemenang.classList.add('hidden');
            tabAntrean.classList.remove('hidden');
            btnAntrean.className = "pb-4 px-6 text-xs font-black uppercase tracking-widest border-b-4 border-green-700 text-green-700 transition-all";
            btnPemenang.className = "pb-4 px-6 text-xs font-black uppercase tracking-widest border-b-4 border-transparent text-gray-400 hover:text-slate-600 transition-all";
        }
    }

    function filterSkemaAntreanServer(val) {
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('filter_skema', val);
        urlParams.delete('page_antrean'); // Reset page antrean saat filter diganti
        window.location.search = urlParams.toString();
    }

    function triggerUndianAnimation() {
        const skemaId = document.getElementById('id_skema').value;
        if(!skemaId) {
            Swal.fire({ icon: 'error', title: 'Pilih Skema!', text: 'Silakan pilih skema qurban terlebih dahulu.' });
            return;
        }

        Swal.fire({
            title: 'Sedang Mengundi...',
            html: 'Sistem sedang memilih pemenang secara acak.',
            timer: 2000,
            timerProgressBar: true,
            didOpen: () => { Swal.showLoading(); },
            willClose: () => { document.getElementById('formProsesUndian').submit(); },
            customClass: { popup: 'rounded-[32px] p-10', title: 'text-2xl font-black uppercase text-slate-800' }
        });
    }

    window.addEventListener('load', () => {
        const urlParams = new URLSearchParams(window.location.search);
        // TAB ANTREAN
        if (
            urlParams.has('page_antrean') ||
            urlParams.has('filter_skema')
        ) {
            switchTab('antrean');
        }
        // TAB PEMENANG
        else {
            switchTab('pemenang');
        }

    });
</script>

{{-- ALERT HANDLING --}}
@if(session('error_pembayaran'))
<script>
    Swal.fire({
        icon: 'warning',
        title: '{{ session("error_pembayaran")["title"] }}',
        html: '<div class="text-left"><p class="mb-2">{{ session("error_pembayaran")["message"] }}</p>' +
              '<p class="text-xs text-red-500 font-bold">Peserta belum lunas: </p>' +
              '<p class="text-xs italic text-gray-500">{{ session("error_pembayaran")["detail"] }}</p></div>',
        confirmButtonColor: '#147a54',
        customClass: { popup: 'rounded-[32px]' }
    });
</script>
@endif

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session("success") }}',
        timer: 3000,
        showConfirmButton: false,
        customClass: { popup: 'rounded-[32px]' }
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: '{{ session("error") }}',
        customClass: { popup: 'rounded-[32px]' }
    });
</script>
@endif
@endsection