@extends('admin.layout.app')

@section('content')
<div class="max-w-7xl mx-auto animate-fadeIn">
    
    {{-- Header Section --}}
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight uppercase mb-1">Transaksi Pembayaran</h1>
            <p class="text-sm text-gray-400 font-medium italic uppercase tracking-wider">Riwayat iuran</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            {{-- FITUR PENCARIAN --}}
            <div class="relative">
                <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="CARI NAMA PESERTA..." 
                    class="bg-white border border-gray-200 text-[10px] font-black uppercase tracking-widest rounded-2xl px-10 py-3 shadow-sm focus:ring-2 focus:ring-green-800 focus:border-transparent outline-none w-64 transition-all">
                <svg class="w-4 h-4 absolute left-4 top-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <button type="button" onclick="confirmGenerate()" class="bg-slate-800 hover:bg-black text-white px-5 py-3 rounded-2xl font-black text-[9px] uppercase tracking-widest shadow-lg transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Tagih Manual
            </button>

            <a href="{{ route('admin.transaksi.export') }}" class="bg-green-800 hover:bg-green-900 text-white px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-green-900/20 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Ekspor PDF
            </a>
        </div>
    </div>

    {{-- FITUR BARU: FILTER PERIODE --}}
    <div class="mb-8 bg-white p-4 rounded-[25px] border border-gray-100 shadow-sm flex flex-wrap gap-4 items-center">
        <form action="" method="GET" class="flex flex-wrap gap-4 items-center w-full">
            <div class="flex flex-col">
                <label class="text-[9px] font-black text-gray-400 uppercase ml-2 mb-1">Bulan</label>
                <select name="bulan" class="bg-gray-50 border-none text-[10px] font-bold uppercase rounded-xl px-4 py-2 focus:ring-2 focus:ring-green-800 transition-all">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ request('bulan', date('n')) == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col">
                <label class="text-[9px] font-black text-gray-400 uppercase ml-2 mb-1">Tahun</label>
                <select name="tahun" class="bg-gray-50 border-none text-[10px] font-bold uppercase rounded-xl px-4 py-2 focus:ring-2 focus:ring-green-800 transition-all">
                    @for($y = date('Y'); $y >= 2023; $y--)
                        <option value="{{ $y }}" {{ request('tahun', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="mt-4 bg-green-50 text-green-700 hover:bg-green-700 hover:text-white px-6 py-2 rounded-xl text-[10px] font-black uppercase transition-all">
                Filter Data
            </button>
        </form>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">
        {{-- Main Content --}}
        <div class="flex-1 space-y-8">
            
            {{-- Statistik Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-[32px] border border-gray-100 shadow-sm">
                    <p class="text-[9px] font-black text-gray-400 uppercase mb-1">Total Saldo</p>
                    <h3 class="text-lg font-black text-slate-800 leading-tight">Rp {{ number_format($totalKas, 0, ',', '.') }}</h3>
                </div>

                {{-- Perbaikan Filter Saldo Tunai --}}
                <div class="bg-white p-5 rounded-[32px] border border-gray-100 shadow-sm">
                    <p class="text-[9px] font-black text-blue-500 uppercase mb-1">Saldo Tunai</p>
                    <h3 class="text-lg font-black text-slate-800 leading-tight">
                        @php
                            $saldoTunai = $transaksi->where('status_pembayaran', 'sukses')->filter(function($item) {
                                return stripos($item->metode_pembayaran, 'tunai') !== false;
                            })->sum('nominal');
                        @endphp
                        Rp {{ number_format($saldoTunai, 0, ',', '.') }}
                    </h3>
                </div>

                {{-- Perbaikan Filter Saldo Transfer --}}
                <div class="bg-white p-5 rounded-[32px] border border-gray-100 shadow-sm">
                    <p class="text-[9px] font-black text-purple-500 uppercase mb-1">Saldo Transfer</p>
                    <h3 class="text-lg font-black text-slate-800 leading-tight">
                        @php
                            $saldoTransfer = $transaksi->where('status_pembayaran', 'sukses')->filter(function($item) {
                                return stripos($item->metode_pembayaran, 'tunai') === false;
                            })->sum('nominal');
                        @endphp
                        Rp {{ number_format($saldoTransfer, 0, ',', '.') }}
                    </h3>
                </div>

                <div class="bg-white p-5 rounded-[32px] border border-orange-100 bg-orange-50/30 shadow-sm">
                    <p class="text-[9px] font-black text-orange-400 uppercase mb-1">Tagihan Pending</p>
                    <h3 class="text-lg font-black text-slate-800 leading-tight">{{ $transaksi->where('status_pembayaran', 'pending')->count() }} Peserta</h3>
                </div>
            </div>

            {{-- DAFTAR TRANSAKSI --}}
            <div id="transactionList" class="space-y-6 max-h-[700px] overflow-y-auto pr-2 custom-scrollbar">
                {{-- ================= FITUR BARU: DAFTAR TUNGGAKAN (PENDING) ================= --}}
                @if($tunggakan->count() > 0)
                <div class="transaction-item bg-orange-50 rounded-[32px] border border-orange-200 shadow-sm overflow-hidden mb-8 animate-pulse-slow">
                    <div class="bg-orange-100/50 px-6 py-4 flex justify-between items-center border-b border-orange-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-orange-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h2 class="text-xs font-black text-orange-900 uppercase tracking-widest">Daftar Tunggakan Belum Bayar</h2>
                                <p class="text-[10px] text-orange-700 font-bold uppercase italic tracking-tighter">Total {{ $tunggakan->count() }} transaksi perlu ditagih</p>
                            </div>
                        </div>
                        <span class="bg-white px-4 py-1.5 rounded-full text-[9px] font-black text-orange-600 border border-orange-200 uppercase">Perhatian Admin</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left searchable-table">
                            <tbody class="divide-y divide-orange-100">
                                @foreach($tunggakan as $tgk)
                                <tr class="hover:bg-orange-100/30 transition-colors">
                                    <td class="px-6 py-4 text-[9px] font-bold text-orange-400 uppercase w-24">#{{ $tgk->order_id }}</td>
                                    <td class="px-6 py-4 name-cell">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-orange-200 text-orange-600 rounded-full flex items-center justify-center text-[10px] font-black uppercase">{{ substr($tgk->peserta->nama, 0, 2) }}</div>
                                            <div>
                                                <p class="text-xs font-bold text-slate-700 leading-tight">{{ $tgk->peserta->nama }}</p>
                                                <p class="text-[9px] font-black text-orange-600 uppercase tracking-tighter">
                                                    {{ $tgk->bulan_iuran }} • {{ $tgk->peserta->kelompok->nama_kelompok ?? 'Individu' }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-black text-slate-800">Rp {{ number_format($tgk->nominal, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <button type="button" onclick="confirmVerifikasi('{{ $tgk->id_transaksi }}', '{{ $tgk->peserta->nama }}')" 
                                            class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-xl font-black text-[9px] uppercase tracking-widest transition-all shadow-md shadow-orange-200">
                                            Verifikasi Manual
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Garis Pemisah Visual --}}
                <div class="relative py-4 flex items-center justify-center">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-100"></div></div>
                    <span class="relative bg-gray-50 px-4 text-[9px] font-black text-gray-400 uppercase tracking-[0.3em]">Riwayat Semua Transaksi</span>
                </div>
                @endif

                {{-- ================= DAFTAR TRANSAKSI NORMAL (SEMUA STATUS) ================= --}}
                
                @php 
                    $transaksiKelompok = $transaksi->whereNotNull('peserta.id_kelompok')->groupBy('peserta.id_kelompok'); 
                @endphp

                @foreach($transaksiKelompok as $idKelompok => $items)
                <div class="transaction-item bg-white rounded-[32px] border border-green-100 shadow-sm overflow-hidden animate-fadeIn mb-6">
                    <div class="bg-green-50 px-6 py-4 flex justify-between items-center border-b border-green-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-800 text-white rounded-2xl flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <div>
                                <h2 class="text-xs font-black text-green-900 uppercase tracking-widest">
                                    {{ $items->first()->peserta->kelompok->nama_kelompok ?? "Kelompok #$idKelompok" }}
                                </h2>
                                <p class="text-[10px] text-green-700 font-bold uppercase italic">Iuran Terbagi 7 Orang</p>
                            </div>
                        </div>
                        <span class="bg-white px-4 py-1.5 rounded-full text-[9px] font-black text-green-800 border border-green-200 uppercase">
                            {{ $items->where('status_pembayaran', 'sukses')->count() }} / 7 Lunas
                        </span>
                    </div>
                    <table class="w-full text-left searchable-table">
                        <tbody class="divide-y divide-gray-50">
                            @foreach($items as $t)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 text-[9px] font-bold text-gray-400 uppercase">#{{ $t->order_id }}</td>
                                <td class="name-cell">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-slate-100 text-slate-500 rounded-full flex items-center justify-center text-[10px] font-black">{{ substr($t->peserta->nama, 0, 2) }}</div>
                                        <div>
                                            <p class="text-xs font-bold text-slate-700 leading-tight">{{ $t->peserta->nama }}</p>
                                            <p class="text-[9px] font-black text-green-600 uppercase tracking-tighter">{{ $t->peserta->skemaArisan->nama_skema ?? '-' }} • {{ $t->bulan_iuran }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-xs font-black text-green-700">Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                                <td class="text-[9px] font-black uppercase text-gray-400">
                                    {{ $t->status_pembayaran == 'sukses' ? ($t->metode_pembayaran ?? 'Online') : '-' }}
                                </td>
                                <td class="text-center">
                                    @if($t->status_pembayaran == 'pending')
                                        <button type="button" onclick="confirmVerifikasi('{{ $t->id_transaksi }}', '{{ $t->peserta->nama }}')" class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-600 hover:text-white transition-all shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                        <form id="verifikasi-form-{{ $t->id_transaksi }}" action="{{ route('admin.transaksi.verifikasi', $t->id_transaksi) }}" method="POST" style="display: none;">@csrf</form>
                                    @else
                                        <span class="text-green-600 font-black text-[10px] uppercase">Lunas</span>
                                    @endif
                                </td>
                                <td class="px-6">
                                    <span class="px-3 py-1.5 rounded-full text-[9px] font-black uppercase {{ $t->status_pembayaran == 'sukses' ? 'bg-green-100 text-green-600' : 'bg-orange-100 text-orange-600' }}">
                                        • {{ $t->status_pembayaran }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endforeach

                {{-- SEKSI PERORANGAN --}}
                <div class="transaction-item bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h2 class="text-xs font-black text-slate-800 uppercase tracking-widest">Iuran Perorangan / Individu</h2>
                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Total: {{ $transaksi->whereNull('peserta.id_kelompok')->count() }}</span>
                    </div>
                    <table class="w-full text-left searchable-table">
                        <tbody class="divide-y divide-gray-50">
                            @forelse($transaksi->whereNull('peserta.id_kelompok') as $t)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 text-[9px] font-bold text-gray-400 uppercase">#{{ $t->order_id }}</td>
                                <td class="name-cell">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-green-800 text-white rounded-full flex items-center justify-center text-[10px] font-black uppercase">{{ substr($t->peserta->nama, 0, 2) }}</div>
                                        <div>
                                            <p class="text-xs font-bold text-slate-700 leading-tight">{{ $t->peserta->nama }}</p>
                                            <p class="text-[9px] font-black text-green-600 uppercase tracking-tighter">{{ $t->peserta->skemaArisan->nama_skema ?? '-' }} • {{ $t->bulan_iuran }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-xs font-black text-green-700">Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                                <td class="text-[9px] font-black uppercase text-gray-400">
                                    {{ $t->status_pembayaran == 'sukses' ? ($t->metode_pembayaran ?? 'Online') : '-' }}
                                </td>
                                <td class="text-center">
                                    @if($t->status_pembayaran == 'pending')
                                        <button type="button" onclick="confirmVerifikasi('{{ $t->id_transaksi }}', '{{ $t->peserta->nama }}')" class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-600 hover:text-white transition-all shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                        <form id="verifikasi-form-{{ $t->id_transaksi }}" action="{{ route('admin.transaksi.verifikasi', $t->id_transaksi) }}" method="POST" style="display: none;">@csrf</form>
                                    @else
                                        <span class="text-green-600 font-black text-[10px] uppercase">Lunas</span>
                                    @endif
                                </td>
                                <td class="px-6">
                                    <span class="px-3 py-1.5 rounded-full text-[9px] font-black uppercase {{ $t->status_pembayaran == 'sukses' ? 'bg-green-100 text-green-600' : 'bg-orange-100 text-orange-600' }}">
                                        • {{ $t->status_pembayaran }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-6 py-8 text-center text-gray-300 text-[10px] font-black uppercase">Belum ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sidebar Stats (Right) --}}
        <div class="w-full lg:w-80 space-y-6">
            <div class="bg-white p-6 rounded-[32px] border border-gray-100 shadow-sm">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Tren Transaksi (6 Bln)</h3>
                <div class="h-48 relative px-2">
                    <svg viewBox="0 0 100 40" class="w-full h-full overflow-visible">
                        <line x1="0" y1="0" x2="100" y2="0" stroke="#f3f4f6" stroke-width="0.5" />
                        <line x1="0" y1="10" x2="100" y2="10" stroke="#f3f4f6" stroke-width="0.5" />
                        <line x1="0" y1="20" x2="100" y2="20" stroke="#f3f4f6" stroke-width="0.5" />
                        <line x1="0" y1="30" x2="100" y2="30" stroke="#f3f4f6" stroke-width="0.5" />
                        <line x1="0" y1="40" x2="100" y2="40" stroke="#e5e7eb" stroke-width="1" />

                        @php 
                            $max = $grafikBulanan->max() > 0 ? $grafikBulanan->max() : 1;
                            $points = "";
                            foreach($grafikBulanan as $index => $val) {
                                $x = ($index / 5) * 100;
                                $y = 40 - (($val / $max) * 40);
                                $points .= "$x,$y ";
                            }
                        @endphp
                        
                        <polyline points="{{ $points }}" fill="none" stroke="#064e3b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        
                        @foreach($grafikBulanan as $index => $val)
                            @php 
                                $cx = ($index / 5) * 100;
                                $cy = 40 - (($val / $max) * 40);
                            @endphp
                            <circle cx="{{ $cx }}" cy="{{ $cy }}" r="2" fill="#064e3b" />
                        @endforeach
                    </svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[32px] border border-gray-100 shadow-sm">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-5">Analitik Keuangan</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <span class="text-[9px] font-bold text-gray-400 uppercase">Rata-rata</span>
                        <span class="text-xs font-black text-slate-800 tracking-tighter">Rp {{ number_format($rataRata, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[9px] font-bold text-gray-400 uppercase">Favorit</span>
                        <span class="text-xs font-black text-slate-800 italic uppercase">{{ $metodeFavorit->metode_pembayaran ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #064e3b; }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // FITUR ALERT SESSION
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'BERHASIL',
            text: "{{ session('success') }}",
            confirmButtonColor: '#064e3b',
            customClass: { popup: 'rounded-[32px]' }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'GAGAL',
            text: "{{ session('error') }}",
            confirmButtonColor: '#064e3b',
            customClass: { popup: 'rounded-[32px]' }
        });
    @endif

    function searchTable() {
        let input = document.getElementById("searchInput").value.toUpperCase();
        let containers = document.getElementsByClassName("transaction-item");
        for (let i = 0; i < containers.length; i++) {
            let table = containers[i].getElementsByClassName("searchable-table")[0];
            if (!table) continue;
            let tr = table.getElementsByTagName("tr");
            let containerHasMatch = false;
            for (let j = 0; j < tr.length; j++) {
                let nameCell = tr[j].getElementsByClassName("name-cell")[0];
                if (nameCell) {
                    let txtValue = nameCell.textContent || nameCell.innerText;
                    if (txtValue.toUpperCase().indexOf(input) > -1) {
                        tr[j].style.display = "";
                        containerHasMatch = true;
                    } else {
                        tr[j].style.display = "none";
                    }
                }
            }
            containers[i].style.display = containerHasMatch ? "" : "none";
        }
    }

    function confirmGenerate() {
        Swal.fire({
            title: 'TAGIH IURAN MANUAL?',
            html: `<span class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Sistem akan memproses tagihan bulan ini.</span>`,
            icon: 'question',
            iconColor: '#147a54',
            showCancelButton: true,
            confirmButtonColor: '#064e3b',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, PROSES',
            cancelButtonText: 'BATALKAN',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-[32px] border-none shadow-2xl',
                title: 'text-2xl font-black text-gray-800 uppercase tracking-tight',
                confirmButton: 'rounded-full px-8 py-3 text-sm font-black uppercase tracking-widest shadow-lg shadow-green-900/20 ml-2',
                cancelButton: 'rounded-full px-8 py-3 text-sm font-black uppercase tracking-widest text-gray-500 hover:bg-gray-200'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                fetch("/admin/transaksi/generate", { 
                    method: "POST",
                    headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" }
                })
                .then(r => r.json())
                .then(data => {
                    Swal.fire({
                        title: 'BERHASIL!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonColor: '#064e3b',
                        customClass: { popup: 'rounded-[32px]' }
                    }).then(() => { location.reload(); });
                });
            }
        });
    }

    function confirmVerifikasi(id, name) {
        Swal.fire({
            title: 'VERIFIKASI TUNAI?',
            text: `KONFIRMASI PEMBAYARAN UNTUK ${name.toUpperCase()}?`,
            icon: 'check-circle',
            iconColor: '#147a54',
            showCancelButton: true,
            confirmButtonColor: '#064e3b',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, VERIFIKASI',
            cancelButtonText: 'NANTI SAJA',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-[32px] border-none shadow-2xl',
                title: 'text-2xl font-black text-gray-800 uppercase tracking-tight',
                confirmButton: 'rounded-full px-8 py-3 text-sm font-black uppercase tracking-widest shadow-lg shadow-green-900/20 ml-2',
                cancelButton: 'rounded-full px-8 py-3 text-sm font-black uppercase tracking-widest text-gray-500 hover:bg-gray-200'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                document.getElementById('verifikasi-form-' + id).submit();
            }
        });
    }

    function showLoading() {
        Swal.fire({
            title: 'MEMPROSES...',
            text: 'SEDANG MEMPERBARUI DATABASE',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
    }
</script>
@endsection