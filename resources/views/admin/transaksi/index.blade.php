@extends('admin.layout.app')

@section('content')
<div class="max-w-7xl mx-auto animate-fadeIn flex flex-col gap-8">
    
    {{-- ================= HEADER SECTION ================= --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-green-900 leading-tight">Transaksi Pembayaran</h1>
            <p class="text-sm text-gray-400 font-medium italic tracking-wider">Monitoring Riwayat Iuran Masjid Nurul Huda</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="CARI NAMA PESERTA..." 
                    class="bg-white border border-gray-200 text-[10px] font-black uppercase tracking-widest rounded-2xl px-10 py-3.5 shadow-sm focus:ring-2 focus:ring-green-800 focus:border-transparent outline-none w-72 transition-all">
                <svg class="w-4 h-4 absolute left-4 top-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <button type="button" onclick="confirmGenerate()" class="bg-slate-800 hover:bg-black text-white px-6 py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg transition-all flex items-center gap-2 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Tagih
            </button>

            <a href="{{ route('admin.transaksi.export', ['bulan' => request('bulan'), 'tahun' => request('tahun')]) }}" class="bg-green-800 hover:bg-green-900 text-white px-6 py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-green-900/20 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                PDF
            </a>
        </div>
    </div>

    {{-- ================= FILTER PERIODE ================= --}}
    <div class="bg-white p-5 rounded-[25px] border border-gray-100 shadow-sm flex flex-wrap gap-4 items-center">
        <form action="" method="GET" class="flex flex-wrap gap-6 items-center w-full">
            <div class="flex flex-col">
                <label class="text-[9px] font-black text-gray-400 uppercase ml-2 mb-1 tracking-widest">Bulan</label>
                <select name="bulan" class="bg-gray-50 border-none text-[10px] font-bold uppercase rounded-xl px-5 py-2.5 focus:ring-2 focus:ring-green-800 transition-all cursor-pointer">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ request('bulan', date('n')) == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col">
                <label class="text-[9px] font-black text-gray-400 uppercase ml-2 mb-1 tracking-widest">Tahun</label>
                <select name="tahun" class="bg-gray-50 border-none text-[10px] font-bold uppercase rounded-xl px-5 py-2.5 focus:ring-2 focus:ring-green-800 transition-all cursor-pointer">
                    @for($y = date('Y'); $y >= 2023; $y--)
                        <option value="{{ $y }}" {{ request('tahun', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="mt-4 bg-green-50 text-green-700 hover:bg-green-700 hover:text-white px-8 py-2.5 rounded-xl text-[10px] font-black uppercase transition-all shadow-sm">
                Filter Data
            </button>
        </form>
    </div>

    {{-- ================= TOP ROW: STATS & TUNGGAKAN ================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- STATS (COLS 4) --}}
        <div class="lg:col-span-4 grid grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-[32px] border border-gray-100 shadow-sm">
                <p class="text-[10px] font-black text-gray-400 uppercase mb-2 tracking-wider">Total Saldo</p>
                <h3 class="text-xl font-black text-slate-800 leading-tight">Rp {{ number_format($totalKas, 0, ',', '.') }}</h3>
            </div>
            <div class="bg-white p-6 rounded-[32px] border border-gray-100 shadow-sm">
                <p class="text-[10px] font-black text-blue-500 uppercase mb-2 tracking-wider">Tunai</p>
                <h3 class="text-xl font-black text-slate-800 leading-tight">Rp {{ number_format($totalTunai, 0, ',', '.') }}</h3>
            </div>
            <div class="bg-white p-6 rounded-[32px] border border-gray-100 shadow-sm">
                <p class="text-[10px] font-black text-purple-500 uppercase mb-2 tracking-wider">Transfer</p>
                <h3 class="text-xl font-black text-slate-800 leading-tight">Rp {{ number_format($totalTransfer, 0, ',', '.') }}</h3>
            </div>
            <div class="bg-white p-6 rounded-[32px] border border-orange-100 bg-orange-50/30 shadow-sm">
                <p class="text-[10px] font-black text-orange-400 uppercase mb-2 tracking-wider">Tunggakan</p>
                <h3 class="text-xl font-black text-slate-800 leading-tight">{{ $tunggakan->count() }} Item</h3>
            </div>
        </div>

        {{-- TUNGGAKAN CARD (COLS 8) --}}
        <div class="lg:col-span-8">
            <div class="transaction-item bg-orange-50 rounded-[40px] border border-orange-200 shadow-sm overflow-hidden flex flex-col h-full max-h-[340px]">
                <div class="bg-orange-100/50 px-8 py-5 flex justify-between items-center border-b border-orange-200 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-orange-500 text-white rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h2 class="text-sm font-black text-orange-900 uppercase tracking-widest">Daftar Tunggakan</h2>
                    </div>
                    <form action="{{ route('admin.transaksi.tagih-wa') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-xl font-black text-[9px] uppercase tracking-widest transition-all shadow-md">Tagih WA</button>
                    </form>
                </div>
                <div class="overflow-y-auto flex-1 custom-scrollbar bg-white/50">
                    <table class="w-full text-left searchable-table">
                        <tbody class="divide-y divide-orange-100">
                            @foreach($tunggakan as $tgk)
                            <tr class="hover:bg-orange-100/30 transition-colors">
                                <td class="px-8 py-4 text-[10px] font-bold text-orange-400 uppercase w-28 tracking-tighter">#{{ $tgk->order_id }}</td>
                                <td class="px-4 py-4 name-cell">
                                    <div class="flex items-center gap-4">
                                        <div class="w-9 h-9 bg-orange-200 text-orange-700 rounded-full flex items-center justify-center text-[11px] font-black uppercase">{{ substr($tgk->peserta->nama, 0, 2) }}</div>
                                        <div>
                                            <p class="text-[13px] font-black text-slate-800 leading-tight uppercase">{{ $tgk->peserta->nama }}</p>
                                            <p class="text-[9px] font-bold text-orange-600 uppercase tracking-widest">{{ $tgk->bulan_iuran }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-[13px] font-black text-slate-900 whitespace-nowrap">Rp {{ number_format($tgk->nominal, 0, ',', '.') }}</td>
                                <td class="px-8 py-4 text-right">
                                    {{-- TOMBOL VERIFIKASI DENGAN LOGIKA YANG SAMA --}}
                                    <button type="button" onclick="confirmVerifikasi('{{ $tgk->id_transaksi }}', '{{ $tgk->peserta->nama }}')" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-xl font-black text-[9px] uppercase shadow-md transition-all">
                                        Verifikasi
                                    </button>
                                    
                                    {{-- HIDDEN FORM UNTUK VERIFIKASI CEPAT --}}
                                    <form id="verifikasi-form-{{ $tgk->id_transaksi }}" action="{{ route('admin.transaksi.verifikasi', $tgk->id_transaksi) }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= BOTTOM SECTION: MAIN LIST ================= --}}
    <div class="flex flex-col lg:flex-row gap-8">
        <div class="flex-1 space-y-10">
            {{-- 1. SEKSI KELOMPOK --}}
            @php $transaksiKelompok = $transaksi->whereNotNull('peserta.id_kelompok')->groupBy('peserta.id_kelompok'); @endphp
            @foreach($transaksiKelompok as $idKelompok => $items)
            <div class="transaction-item bg-white rounded-[40px] border border-gray-100 shadow-sm overflow-hidden flex flex-col h-full max-h-[450px]">
                <div class="bg-green-50 px-8 py-6 flex justify-between items-center border-b border-green-100 shrink-0">
                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 bg-green-800 text-white rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-green-900 uppercase tracking-tighter">{{ $items->first()->peserta->kelompok->nama_kelompok ?? "Kelompok #$idKelompok" }}</h2>
                            <p class="text-[11px] text-green-700 font-bold uppercase tracking-widest">Iuran Bulanan Kelompok</p>
                        </div>
                    </div>
                    <span class="bg-white px-5 py-2 rounded-full text-[10px] font-black text-green-800 border-2 border-green-100 uppercase tracking-widest">{{ $items->where('status_pembayaran', 'sukses')->count() }} / 7 Lunas</span>
                </div>
                <div class="overflow-y-auto flex-1 custom-scrollbar">
                    <table class="w-full text-left searchable-table">
                        <tbody class="divide-y divide-gray-50">
                            @foreach($items as $t)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="px-8 py-7 text-[11px] font-bold text-gray-400 uppercase w-32 tracking-tighter">#{{ $t->order_id }}</td>
                                <td class="px-4 py-7 name-cell">
                                    <div class="flex items-center gap-5">
                                        <div class="w-12 h-12 {{ $t->status_pembayaran == 'sukses' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-400' }} rounded-2xl flex items-center justify-center text-sm font-black uppercase shadow-inner">{{ substr($t->peserta->nama, 0, 2) }}</div>
                                        <div>
                                            <p class="text-[14px] font-black text-slate-900 leading-tight uppercase tracking-tight">{{ $t->peserta->nama }}</p>
                                            <p class="text-[11px] font-black text-green-600 uppercase mt-1 tracking-widest">{{ $t->bulan_iuran }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-7 text-[14px] font-black text-slate-900 whitespace-nowrap">Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                                <td class="px-4 py-7 text-center">
                                    @if($t->status_pembayaran == 'pending')
                                        <button type="button" onclick="confirmVerifikasi('{{ $t->id_transaksi }}', '{{ $t->peserta->nama }}')" class="p-4 bg-green-50 text-green-600 rounded-2xl hover:bg-green-600 hover:text-white transition-all shadow-md active:scale-90">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                        <form id="verifikasi-form-{{ $t->id_transaksi }}" action="{{ route('admin.transaksi.verifikasi', $t->id_transaksi) }}" method="POST" style="display: none;">@csrf</form>
                                    @else
                                        <span class="px-5 py-2.5 rounded-2xl text-[10px] font-black uppercase bg-green-500/10 text-green-700 border-2 border-green-200/50 tracking-widest whitespace-nowrap">Lunas</span>
                                    @endif
                                </td>
                                <td class="px-8 py-7 text-right">
                                    <span class="text-[11px] font-black text-slate-900 uppercase tracking-widest">{{ $t->status_pembayaran == 'sukses' ? ($t->metode_pembayaran ?? 'Online') : 'Pending' }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach

            {{-- 2. SEKSI PERORANGAN / INDIVIDU --}}
            @php $transaksiIndividu = $transaksi->whereNull('peserta.id_kelompok'); @endphp
            @if($transaksiIndividu->count() > 0)
            <div class="transaction-item bg-white rounded-[40px] border border-gray-100 shadow-sm overflow-hidden flex flex-col h-full mt-10">
                <div class="bg-slate-50 px-8 py-6 flex justify-between items-center border-b border-slate-100 shrink-0">
                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 bg-slate-800 text-white rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-900 uppercase tracking-tighter">Iuran Perorangan</h2>
                            <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest">Tabungan Individu / Mandiri</p>
                        </div>
                    </div>
                    <span class="bg-white px-5 py-2 rounded-full text-[10px] font-black text-slate-600 border-2 border-slate-100 uppercase tracking-widest">Total: {{ $transaksiIndividu->count() }} Data</span>
                </div>
                <div class="custom-scrollbar overflow-x-auto">
                    <table class="w-full text-left searchable-table">
                        <tbody class="divide-y divide-gray-50">
                            @foreach($transaksiIndividu as $t)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="px-8 py-7 text-[11px] font-bold text-gray-400 uppercase w-32 tracking-tighter">#{{ $t->order_id }}</td>
                                <td class="px-4 py-7 name-cell">
                                    <div class="flex items-center gap-5">
                                        <div class="w-12 h-12 {{ $t->status_pembayaran == 'sukses' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-400' }} rounded-2xl flex items-center justify-center text-sm font-black uppercase shadow-inner">{{ substr($t->peserta->nama, 0, 2) }}</div>
                                        <div>
                                            <p class="text-[14px] font-black text-slate-900 leading-tight uppercase tracking-tight">{{ $t->peserta->nama }}</p>
                                            <p class="text-[11px] font-black text-slate-400 uppercase mt-1 tracking-widest">{{ $t->peserta->skemaArisan->nama_skema ?? 'Tanpa Skema' }} • {{ $t->bulan_iuran }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-7 text-[14px] font-black text-slate-900 whitespace-nowrap">Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                                <td class="px-4 py-7 text-center">
                                    @if($t->status_pembayaran == 'pending')
                                        <button type="button" onclick="confirmVerifikasi('{{ $t->id_transaksi }}', '{{ $t->peserta->nama }}')" class="p-4 bg-green-50 text-green-600 rounded-2xl hover:bg-green-600 hover:text-white transition-all shadow-md active:scale-90">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                        <form id="verifikasi-form-{{ $t->id_transaksi }}" action="{{ route('admin.transaksi.verifikasi', $t->id_transaksi) }}" method="POST" style="display: none;">@csrf</form>
                                    @else
                                        <span class="px-5 py-2.5 rounded-2xl text-[10px] font-black uppercase bg-green-500/10 text-green-700 border-2 border-green-200/50 tracking-widest whitespace-nowrap">Lunas</span>
                                    @endif
                                </td>
                                <td class="px-8 py-7 text-right">
                                    <span class="text-[11px] font-black text-slate-900 uppercase tracking-widest">{{ $t->status_pembayaran == 'sukses' ? ($t->metode_pembayaran ?? 'Online') : 'Pending' }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        {{-- SIDEBAR AREA --}}
        <div class="w-full lg:w-96 space-y-8">
            <div class="bg-white p-8 rounded-[40px] border border-gray-100 shadow-sm sticky top-8">
                <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] mb-8">Tren Transaksi (6 Bln)</h3>
                <div class="h-48 relative px-2 mb-10">
                    <svg viewBox="0 0 100 40" class="w-full h-full overflow-visible">
                        @foreach(range(0, 4) as $line)
                            <line x1="0" y1="{{ $line * 10 }}" x2="100" y2="{{ $line * 10 }}" stroke="#f3f4f6" stroke-width="0.5" />
                        @endforeach
                        @php 
                            $max = $grafikBulanan->max() > 0 ? $grafikBulanan->max() : 1;
                            $points = ""; $labels = []; $i = 0;
                            foreach(($grafikBulananFiltered ?? $grafikBulanan) as $bulanKey => $val) {
                                $x = ($i / 5) * 100; $y = 40 - (($val / $max) * 40);
                                $points .= "$x,$y ";
                                $labels[] = ['x' => $x, 'text' => \Carbon\Carbon::parse($bulanKey)->translatedFormat('M')];
                                $i++;
                            }
                        @endphp
                        <polyline points="{{ $points }}" fill="none" stroke="#064e3b" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                        @php $i = 0; @endphp
                        @foreach($grafikBulanan as $index => $val)
                            @php $cx = ($i / 5) * 100; $cy = 40 - (($val / $max) * 40); $i++; @endphp
                            <circle cx="{{ $cx }}" cy="{{ $cy }}" r="3" fill="#064e3b" stroke="white" stroke-width="1.5" />
                        @endforeach
                    </svg>
                </div>
                <div class="space-y-6 pt-8 border-t border-gray-50">
                    <div class="flex justify-between items-center bg-gray-50 p-5 rounded-2xl border border-gray-100">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Rata-rata</span>
                        <span class="text-sm font-black text-slate-800">Rp {{ number_format($rataRata, 0, ',', '.') }}</span>
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