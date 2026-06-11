@extends('admin.layout.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-6 animate-fadeIn">

    {{-- ══════════════════════════════════════════
         HEADER
         ══════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <p class="text-[10px] font-bold tracking-[0.25em] text-green-700 uppercase mb-1">Masjid Nurul Huda</p>
            <h1 class="text-2xl font-black text-green-900 tracking-tight">Transaksi Pembayaran</h1>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <div class="relative">
                <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Cari peserta..."
                    class="bg-white border border-green-200 text-xs rounded-xl px-4 py-2.5 pl-9 focus:ring-2 focus:ring-green-700 outline-none w-56 transition-all shadow-sm">
                <svg class="w-3.5 h-3.5 absolute left-3 top-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <button onclick="confirmGenerate()"
                class="inline-flex items-center gap-1.5 bg-green-900 hover:bg-green-950 text-white px-4 py-2.5 rounded-xl text-[11px] font-bold uppercase tracking-wider transition-all shadow-sm active:scale-95">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                Tagih Manual
            </button>

            <a href="{{ route('admin.transaksi.export', ['bulan' => request('bulan'), 'tahun' => request('tahun'), 'skema' => request('skema')]) }}"
               class="inline-flex items-center gap-1.5 bg-white hover:bg-green-50 text-green-800 border border-green-300 px-4 py-2.5 rounded-xl text-[11px] font-bold uppercase tracking-wider transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export PDF
            </a>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         STATS
         ══════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- BOX 1: TOTAL DINAMIS --}}
        <div class="bg-green-900 text-white rounded-2xl p-5 shadow-lg">
            <p class="text-[9px] font-bold tracking-widest uppercase text-green-300 mb-2">
                Pendapatan {{ request()->anyFilled(['bulan','tahun','skema']) ? 'Terfilter' : 'Global' }}
            </p>
            <p class="text-xl font-black">Rp {{ number_format($totalPeriode, 0, ',', '.') }}</p>
            
            {{-- Jika sedang difilter, tampilkan sisa saldo global di bawahnya sebagai info tambahan --}}
            @if(request()->anyFilled(['bulan','tahun','skema']))
                <p class="text-[8px] text-green-400 mt-2 border-t border-green-800 pt-2 uppercase tracking-wider">
                    Total Seluruh Periode: <span class="text-white">Rp {{ number_format($totalKas, 0, ',', '.') }}</span>
                </p>
            @else
                <p class="text-[9px] text-green-400 mt-1 uppercase tracking-wider">Semua transaksi sukses</p>
            @endif
        </div>

        {{-- BOX 2: TUNAI --}}
        <div class="bg-white rounded-2xl p-5 border border-green-100 shadow-sm">
            <p class="text-[9px] font-bold tracking-widest uppercase text-green-600 mb-2">Tunai</p>
            <p class="text-xl font-black text-green-900">Rp {{ number_format($totalTunai, 0, ',', '.') }}</p>
            <p class="text-[9px] text-gray-400 mt-1 uppercase tracking-wider">
                {{ request()->anyFilled(['bulan','tahun','skema']) ? 'Periode terfilter' : 'Global' }}
            </p>
        </div>

        {{-- BOX 3: TRANSFER --}}
        <div class="bg-white rounded-2xl p-5 border border-green-100 shadow-sm">
            <p class="text-[9px] font-bold tracking-widest uppercase text-green-600 mb-2">Transfer</p>
            <p class="text-xl font-black text-green-900">Rp {{ number_format($totalTransfer, 0, ',', '.') }}</p>
            <p class="text-[9px] text-gray-400 mt-1 uppercase tracking-wider">
                {{ request()->anyFilled(['bulan','tahun','skema']) ? 'Periode terfilter' : 'Global' }}
            </p>
        </div>

        {{-- BOX 4: TUNGGAKAN --}}
        <div class="bg-orange-50 rounded-2xl p-5 border border-orange-200 shadow-sm">
            <p class="text-[9px] font-bold tracking-widest uppercase text-orange-500 mb-2">Tunggakan</p>
            <p class="text-xl font-black text-orange-700">{{ $tunggakan->count() }} <span class="text-sm font-bold">item</span></p>
            <p class="text-[9px] text-orange-400 mt-1 uppercase tracking-wider">Belum lunas terfilter</p>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         FILTER BAR (Tanpa Filter Tipe)
         ══════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-green-100 shadow-sm p-5">
        <form action="" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-black text-green-700 uppercase tracking-widest">Bulan</label>
                <select name="bulan" class="bg-green-50 border border-green-200 text-xs font-semibold rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-700 outline-none cursor-pointer min-w-[130px]">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ request('bulan', date('n')) == $m ? 'selected' : '' }}>
                            {{-- Tambahkan locale('id') agar menjadi bahasa Indonesia --}}
                            {{ \Carbon\Carbon::create(date('Y'), $m, 1)->locale('id')->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-black text-green-700 uppercase tracking-widest">Tahun</label>
                <select name="tahun" class="bg-green-50 border border-green-200 text-xs font-semibold rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-700 outline-none cursor-pointer">
                    @for($y = date('Y'); $y >= 2023; $y--)
                        <option value="{{ $y }}" {{ request('tahun', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-black text-green-700 uppercase tracking-widest">Skema</label>
                <select name="skema" class="bg-green-50 border border-green-200 text-xs font-semibold rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-700 outline-none cursor-pointer min-w-[150px]">
                    <option value="">Semua Skema</option>
                    @foreach(\App\Models\SkemaArisan::all() as $sk)
                        <option value="{{ $sk->id_skema }}" {{ request('skema') == $sk->id_skema ? 'selected' : '' }}>
                            {{ $sk->nama_skema }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="bg-green-800 hover:bg-green-900 text-white px-6 py-2 rounded-lg text-[11px] font-bold uppercase tracking-wider transition-all">
                    Filter
                </button>
                @if(request()->anyFilled(['bulan','tahun','skema']))
                <a href="{{ route('admin.transaksi.index') }}" class="text-[11px] font-bold text-gray-400 hover:text-green-700 py-2 transition-all">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- TUNGGAKAN TABLE --}}
    @if($tunggakan->count() > 0)
    <div class="bg-white rounded-2xl border border-orange-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-orange-50 border-b border-orange-200 flex items-center gap-3">
            <div class="w-8 h-8 bg-orange-500 text-white rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-black text-orange-900 uppercase tracking-tight">Daftar Tunggakan</p>
                <p class="text-[9px] font-bold text-orange-500 uppercase tracking-widest">{{ $tunggakan->count() }} transaksi belum lunas</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-orange-100 bg-orange-50/40">
                        <th class="px-6 py-3 text-[9px] font-black text-orange-400 uppercase tracking-widest">Peserta</th>
                        <th class="px-4 py-3 text-[9px] font-black text-orange-400 uppercase tracking-widest">Skema</th>
                        <th class="px-4 py-3 text-[9px] font-black text-orange-400 uppercase tracking-widest">Bulan Iuran</th>
                        <th class="px-4 py-3 text-[9px] font-black text-orange-400 uppercase tracking-widest">Progres</th>
                        <th class="px-4 py-3 text-[9px] font-black text-orange-400 uppercase tracking-widest text-right">Nominal</th>
                        <th class="px-6 py-3 text-[9px] font-black text-orange-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-orange-50">
                    @foreach($tunggakan as $tgk)
                    @php
                        $tenorTgk   = $tgk->peserta->skemaArisan->durasi_bulan ?? 12;
                        $dibayarTgk = \App\Models\TransaksiPembayaran::where('id_pesertaarisan', $tgk->peserta->id_pesertaarisan)->where('status_pembayaran','sukses')->count();
                        $pctTgk     = $tenorTgk > 0 ? min(100, round(($dibayarTgk / $tenorTgk) * 100)) : 0;
                    @endphp
                    <tr class="hover:bg-orange-50/30 transition-colors">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center text-[10px] font-black uppercase flex-shrink-0">
                                    {{ substr($tgk->peserta->nama, 0, 2) }}
                                </div>
                                <div>
                                    <p class="text-[12px] font-black text-gray-900 uppercase leading-tight">{{ $tgk->peserta->nama }}</p>
                                    <p class="text-[9px] text-gray-400 font-bold">#{{ $tgk->order_id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-[10px] font-bold text-gray-600">{{ $tgk->peserta->skemaArisan->nama_skema ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-[11px] font-bold text-gray-700">{{ $tgk->bulan_iuran }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2">
                                <div class="w-20 h-1.5 bg-orange-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-orange-400 rounded-full" style="width: {{ $pctTgk }}%"></div>
                                </div>
                                <span class="text-[10px] font-black text-gray-500">{{ $dibayarTgk }}/{{ $tenorTgk }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <span class="text-[12px] font-black text-gray-900 whitespace-nowrap">Rp {{ number_format($tgk->nominal, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                {{-- 🌟 BUTTON TAGIH WA PERSONAL PER BARIS DATA 🌟 --}}
                                <form action="{{ route('admin.transaksi.tagih-wa', $tgk->id_transaksi) }}" method="POST" onsubmit="showLoading()">
                                    @csrf
                                    <button type="submit" class="p-2 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-lg transition-all" title="Kirim Tagihan WhatsApp">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                        </svg>
                                    </button>
                                </form>

                                {{-- BUTTON MANUAl VERIFIKASI --}}
                                <button onclick="confirmVerifikasi('{{ $tgk->id_transaksi }}', '{{ $tgk->peserta->nama }}')"
                                    class="inline-flex items-center gap-1 bg-green-700 hover:bg-green-800 text-white px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-wider transition-all">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    Verifikasi
                                </button>
                                <form id="verifikasi-form-{{ $tgk->id_transaksi }}" action="{{ route('admin.transaksi.verifikasi', $tgk->id_transaksi) }}" method="POST" class="hidden">@csrf</form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════
         TABEL UTAMA TRANSAKSI
         ══════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-green-100 shadow-sm overflow-hidden transaction-item">
        <div class="flex items-center justify-between px-6 py-4 bg-green-50 border-b border-green-100">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-green-800 text-white rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-black text-green-900 uppercase tracking-tight">Riwayat Transaksi</p>
                    <p class="text-[9px] font-bold text-green-600 uppercase tracking-widest">
                        {{ method_exists($transaksi, 'total') ? $transaksi->total() : $transaksi->count() }} data
                        @if(request()->anyFilled(['bulan','tahun','skema'])) · Terfilter @endif
                    </p>
                </div>
            </div>
            
            {{-- 🛠️ PERBAIKAN DI BAGIAN BADGE PERIODE INI --}}
            @if(request()->anyFilled(['bulan','tahun']))
            <span class="bg-green-100 text-green-800 text-[10px] font-black px-3 py-1.5 rounded-lg uppercase tracking-wider">
                {{ 
                    \Carbon\Carbon::createFromDate(
                        (int)request('tahun', date('Y')), 
                        (int)request('bulan', date('n')), 
                        1
                    )->locale('id')->translatedFormat('F Y') 
                }}
            </span>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left searchable-table">
                <thead>
                    <tr class="bg-gray-50 border-b border-green-100">
                        <th class="px-6 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest">Peserta</th>
                        <th class="px-4 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest">Skema / Tipe</th>
                        <th class="px-4 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest">Progres</th>
                        <th class="px-4 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest">Bulan</th>
                        <th class="px-4 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right">Nominal</th>
                        <th class="px-4 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                        <th class="px-6 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($transaksi as $t)
                    @php
                        $tenor      = $t->peserta->skemaArisan->durasi_bulan ?? 12;
                        $dibayar    = \App\Models\TransaksiPembayaran::where('id_pesertaarisan', $t->peserta->id_pesertaarisan)->where('status_pembayaran','sukses')->count();
                        $pct        = $tenor > 0 ? min(100, round(($dibayar / $tenor) * 100)) : 0;
                        $isKelompok = !is_null($t->peserta->id_kelompok);
                    @endphp
                    <tr class="hover:bg-green-50/30 transition-colors">

                        {{-- PESERTA --}}
                        <td class="px-6 py-4 name-cell">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 {{ $t->status_pembayaran == 'sukses' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }} rounded-xl flex items-center justify-center text-[10px] font-black uppercase flex-shrink-0">
                                    {{ substr($t->peserta->nama, 0, 2) }}
                                </div>
                                <div>
                                    <p class="text-[12px] font-black text-gray-900 uppercase leading-tight">{{ $t->peserta->nama }}</p>
                                    <p class="text-[9px] text-gray-400 font-bold">#{{ $t->order_id }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- SKEMA / TIPE --}}
                        <td class="px-4 py-4">
                            <p class="text-[11px] font-bold text-gray-700 leading-tight">{{ $t->peserta->skemaArisan->nama_skema ?? 'Tanpa Skema' }}</p>
                            <span class="inline-block mt-1 px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-wider {{ $isKelompok ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $isKelompok ? 'Kelompok' : 'Individu' }}
                            </span>
                        </td>

                        {{-- PROGRES --}}
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-24 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all {{ $pct == 100 ? 'bg-green-500' : 'bg-green-300' }}" style="width: {{ $pct }}%"></div>
                                </div>
                                <span class="text-[10px] font-black {{ $pct == 100 ? 'text-green-700' : 'text-gray-500' }} whitespace-nowrap">
                                    {{ $dibayar }}/{{ $tenor }}
                                </span>
                            </div>
                        </td>

                        {{-- BULAN --}}
                        <td class="px-4 py-4">
                            <span class="text-[11px] font-bold text-gray-700">{{ $t->bulan_iuran }}</span>
                        </td>

                        {{-- NOMINAL --}}
                        <td class="px-4 py-4 text-right">
                            <span class="text-[12px] font-black text-gray-900 whitespace-nowrap">Rp {{ number_format($t->nominal, 0, ',', '.') }}</span>
                        </td>

                        {{-- STATUS --}}
                        <td class="px-4 py-4 text-center">
                            @if($t->status_pembayaran == 'sukses')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[9px] font-black uppercase bg-green-100 text-green-700 tracking-wider">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    Lunas
                                </span>
                            @else
                                <span class="inline-flex px-2.5 py-1 rounded-lg text-[9px] font-black uppercase bg-amber-50 text-amber-600 tracking-wider border border-amber-200">
                                    Pending
                                </span>
                            @endif
                        </td>

                        {{-- AKSI --}}
                        <td class="px-6 py-4 text-right">
                            @if($t->status_pembayaran == 'pending')
                                <button onclick="confirmVerifikasi('{{ $t->id_transaksi }}', '{{ $t->peserta->nama }}')"
                                    class="inline-flex items-center gap-1 bg-green-700 hover:bg-green-800 text-white px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-wider transition-all">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    Verifikasi
                                </button>
                                <form id="verifikasi-form-{{ $t->id_transaksi }}" action="{{ route('admin.transaksi.verifikasi', $t->id_transaksi) }}" method="POST" class="hidden">@csrf</form>
                            @else
                                <span class="text-[10px] font-bold text-gray-300 uppercase">{{ $t->metode_pembayaran ?? 'Online' }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="w-12 h-12 bg-green-50 text-green-400 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            </div>
                            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Tidak ada data transaksi</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION & EMPTY STATE RIWAYAT TRANSAKSI --}}
        @if(method_exists($transaksi, 'total') && $transaksi->total() > 0)
            {{-- Tampilkan pagination yang sudah dipercantik jika ada data --}}
            <div class="px-6 py-4 border-t border-green-50 bg-white flex flex-col md:flex-row items-center justify-between gap-4 shadow-sm animate-fadeIn">
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                    Menampilkan {{ $transaksi->firstItem() }} - {{ $transaksi->lastItem() }} dari {{ $transaksi->total() }} data
                </div>
                <div class="custom-pagination">
                    {{-- Mempertahankan semua filter request saat pindah halaman secara otomatis --}}
                    {{ $transaksi->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
@if(session('success'))
    Swal.fire({ icon:'success', title:'Berhasil', text:"{{ session('success') }}", confirmButtonColor:'#166534', customClass:{popup:'rounded-2xl'} });
@endif
@if(session('error'))
    Swal.fire({ icon:'error', title:'Gagal', text:"{{ session('error') }}", confirmButtonColor:'#166534', customClass:{popup:'rounded-2xl'} });
@endif

function searchTable() {
    // Ambil kata kunci pencarian dan ubah ke huruf besar
    const input = document.getElementById("searchInput").value.toUpperCase();
    
    // Cari semua baris data (tr) yang ada di dalam tbody di seluruh tabel
    document.querySelectorAll("table tbody tr").forEach(tr => {
        // Cari cell yang mengandung nama peserta
        const nameCell = tr.querySelector(".name-cell") || tr.querySelector("td:first-child");
        
        if (nameCell) {
            // Ambil teks nama di dalam cell tersebut
            const textValue = nameCell.textContent || nameCell.innerText;
            
            // Jika cocok dengan kata kunci, tampilkan barisnya. Jika tidak, sembunyikan.
            if (textValue.toUpperCase().includes(input)) {
                tr.style.display = "";
            } else {
                tr.style.display = "none";
            }
        }
    });
}

function confirmGenerate() {
    Swal.fire({
        title: 'Tagih Iuran Manual?',
        text: 'Sistem akan membuat tagihan bulan ini untuk semua peserta aktif.',
        icon: 'question', iconColor: '#166534',
        showCancelButton: true,
        confirmButtonColor: '#166534', cancelButtonColor: '#f3f4f6',
        confirmButtonText: 'Ya, Proses', cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'rounded-xl px-6 py-2.5 text-sm font-bold ml-2',
            cancelButton: 'rounded-xl px-6 py-2.5 text-sm font-bold text-gray-500'
        },
        buttonsStyling: false
    }).then(result => {
        if (result.isConfirmed) {
            showLoading();
            fetch("/admin/transaksi/generate", {
                method: "POST",
                headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" }
            })
            .then(r => r.json())
            .then(data => {
                Swal.fire({ title:'Berhasil!', text: data.message, icon:'success', confirmButtonColor:'#166534', customClass:{popup:'rounded-2xl'} })
                .then(() => location.reload());
            });
        }
    });
}

function confirmVerifikasi(id, name) {
    Swal.fire({
        title: 'Verifikasi Pembayaran?',
        text: `Konfirmasi pembayaran tunai untuk ${name}?`,
        icon: 'question', iconColor: '#166534',
        showCancelButton: true,
        confirmButtonColor: '#166534', cancelButtonColor: '#f3f4f6',
        confirmButtonText: 'Ya, Verifikasi', cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'rounded-xl px-6 py-2.5 text-sm font-bold ml-2',
            cancelButton: 'rounded-xl px-6 py-2.5 text-sm font-bold text-gray-500'
        },
        buttonsStyling: false
    }).then(result => {
        if (result.isConfirmed) {
            showLoading();
            document.getElementById('verifikasi-form-' + id).submit();
        }
    });
}

function showLoading() {
    Swal.fire({ title:'Memproses...', text:'Sedang mengirim notifikasi & memperbarui data', showConfirmButton:false, allowOutsideClick:false, didOpen:()=>Swal.showLoading() });
}
</script>
<style>
    /* 1. STYLING PAGINATION CUSTOM */
    .custom-pagination nav p { display: none !important; }
    .custom-pagination nav > div:first-child { display: none !important; }
    .custom-pagination nav div:last-child { flex-direction: row !important; gap: 0.25rem; display: flex !important; }
    
    .custom-pagination a, 
    .custom-pagination span[aria-current="page"] span,
    .custom-pagination span[aria-disabled="true"] span,
    .custom-pagination span[aria-disabled="true"] a {
        display: flex !important; align-items: center; justify-content: center;
        width: 36px; height: 36px; border-radius: 12px !important;
        font-size: 11px !important; font-weight: 800 !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #e2e8f0 !important; background: white !important;
        color: #64748b !important; box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        text-decoration: none !important;
    }
    
    /* Tombol Aktif / Halaman Sekarang */
    .custom-pagination span[aria-current="page"] span {
        background: #064e3b !important; color: white !important;
        border-color: #064e3b !important; box-shadow: 0 4px 12px rgba(6, 78, 59, 0.2);
    }
    
    /* Hover state untuk tombol yang bisa diklik */
    .custom-pagination a:hover {
        border-color: #064e3b !important; color: #064e3b !important;
        background: #f0fdf4 !important; transform: translateY(-2px);
    }
    
    /* Menjaga icon arrow SVG tetap proporsional */
    .custom-pagination svg { width: 14px !important; height: 14px !important; stroke-width: 3; display: inline-block; }

    /* 2. ANIMASI TRANSISI */
    .animate-fadeIn { animation: fadeIn 0.4s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection