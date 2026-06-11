@extends('admin.layout.app')

@section('content')
<div class="max-w-7xl mx-auto animate-fadeIn space-y-6">
    
    {{-- ══════════════════════════════════════════
         HEADER & EXPORT SECTION
         ══════════════════════════════════════════ --}}
    <div class="flex flex-col md:flex-row justify-between items-end gap-4">
        <div>
            <h1 class="text-2xl font-black text-green-900 leading-tight tracking-tight uppercase">Monitoring & Laporan Arisan</h1>
            <p class="text-sm text-gray-400 font-medium italic">Transparansi Dana Titipan & Realisasi Hewan Qurban</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.pengeluaran.pdf', request()->query()) }}" 
                class="bg-green-800 hover:bg-green-900 text-white px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-green-900/20 transition-all flex items-center gap-2 group">
                <svg class="w-4 h-4 transform group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export PDF Terfilter
            </a>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         STATISTIK CARDS (Dinamis)
         ══════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        {{-- Card 1: Iuran Masuk Terfilter --}}
        <div class="bg-white p-5 rounded-[32px] border border-gray-100 shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase mb-1 tracking-widest">Iuran Masuk</p>
            <h3 class="text-xl font-black text-blue-700">Rp {{ number_format($totalMasukFilter, 0, ',', '.') }}</h3>
        </div>

        {{-- Card 2: Realisasi Terfilter --}}
        <div class="bg-white p-5 rounded-[32px] border border-gray-100 shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase mb-1 tracking-widest">Total Realisasi</p>
            <h3 class="text-xl font-black text-red-600">Rp {{ number_format($totalKeluarFilter, 0, ',', '.') }}</h3>
        </div>

        {{-- Card 3: Saldo Mengendap Terfilter --}}
        <div class="bg-green-50 p-5 rounded-[32px] border border-green-100 shadow-sm">
            <p class="text-[10px] font-black text-green-600 uppercase mb-1 tracking-widest">Saldo</p>
            <h3 class="text-xl font-black text-green-900">Rp {{ number_format($saldoKasDinamis, 0, ',', '.') }}</h3>
        </div>

        {{-- Card 4: Jumlah Peserta Terfilter --}}
        <div class="bg-white p-5 rounded-[32px] border border-gray-100 shadow-sm transition-all hover:shadow-md">
            <p class="text-[10px] font-black text-gray-400 uppercase mb-1 tracking-widest">Jumlah Peserta</p>
            <h3 class="text-xl font-black text-slate-800">
                {{ $totalPesertaFilter }} <span class="text-xs font-bold text-gray-400">Orang</span>
            </h3>
            
            {{-- Detail Status --}}
            <div class="flex gap-2 mt-2 pt-2 border-t border-gray-50">
                <div class="flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                    <span class="text-[9px] font-black text-gray-500 uppercase">{{ $countAktif }} Aktif</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                    <span class="text-[9px] font-black text-gray-500 uppercase">{{ $countNonaktif }} Selesai</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         ADVANCED FILTER BAR
         ══════════════════════════════════════════ --}}
    <div class="bg-white p-6 rounded-[32px] border border-gray-100 shadow-sm">
        <form action="" method="GET" id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Filter Skema --}}
            <div class="space-y-1">
                <label class="text-[9px] font-black text-gray-400 uppercase ml-2">Pilih Skema</label>
                <select name="skema" class="w-full bg-gray-50 border-none text-xs font-bold rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-800 outline-none cursor-pointer">
                    <option value="">SEMUA SKEMA</option>
                    @foreach($skemas as $s)
                        <option value="{{ $s->id_skema }}" {{ request('skema') == $s->id_skema ? 'selected' : '' }}>{{ $s->nama_skema }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Status Menang --}}
            <div class="space-y-1">
                <label class="text-[9px] font-black text-gray-400 uppercase ml-2">Status Undian</label>
                <select name="status" class="w-full bg-gray-50 border-none text-xs font-bold rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-800 outline-none cursor-pointer">
                    <option value="">SEMUA STATUS</option>
                    <option value="pemenang" {{ request('status') == 'pemenang' ? 'selected' : '' }}>SUDAH MENANG (REALISASI)</option>
                    <option value="belum" {{ request('status') == 'belum' ? 'selected' : '' }}>BELUM MENANG (MONITORING)</option>
                </select>
            </div>

            {{-- Filter Tahun (Opsional jika ingin spesifik tahun menang) --}}
            <div class="space-y-1">
                <label class="text-[9px] font-black text-gray-400 uppercase ml-2">Tahun Pelaksanaan</label>
                <select name="tahun" class="w-full bg-gray-50 border-none text-xs font-bold rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-800 outline-none cursor-pointer">
                    <option value="">SEMUA TAHUN</option>
                    @foreach($daftarTahun as $t)
                        <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest py-3.5 rounded-xl transition-all">
                    Terapkan Filter
                </button>
                @if(request()->anyFilled(['skema', 'status', 'tahun']))
                    <a href="{{ route('admin.pengeluaran.index') }}" class="px-4 py-3.5 bg-gray-100 text-gray-500 rounded-xl hover:bg-gray-200 transition-all flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ══════════════════════════════════════════
         MAIN DATA TABLE
         ══════════════════════════════════════════ --}}
    <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 border-b border-gray-100">
                    <tr>
                        <th class="px-8 py-5">Informasi Peserta</th>
                        <th class="px-6 py-5">Skema Arisan</th>
                        <th class="px-6 py-5">Progres Iuran</th>
                        <th class="px-6 py-5 text-center">Status Undian</th>
                        <th class="px-8 py-5 text-right">Realisasi / Saldo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($pesertas as $p)
                        @php
                            $tenor = $p->skemaArisan->durasi_bulan ?? 1;
                            $lunas = $p->transaksi->count();
                            $persen = min(100, round(($lunas / $tenor) * 100));
                            $isMenang = $p->pengeluaranArisan != null;
                            $nominalTabungan = $p->transaksi->sum('nominal');
                        @endphp
                        <tr class="hover:bg-green-50/20 transition-all group">
                            {{-- PESERTA --}}
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <div class="w-9 h-9 {{ $isMenang ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }} rounded-xl flex items-center justify-center text-[11px] font-black uppercase flex-shrink-0 shadow-sm">
                                            {{ substr($p->nama, 0, 2) }}
                                        </div>

                                        {{-- PERBAIKAN DISINI: Tambahkan 'user->' sebelum status --}}
                                        @if($p->user && $p->user->status == 'nonaktif')
                                            <span class="absolute -top-1 -right-1 w-3 h-3 bg-gray-400 border-2 border-white rounded-full" title="Arisan Selesai (Nonaktif)"></span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-black text-slate-800 uppercase leading-tight">{{ $p->nama }}</p>
                                            
                                            {{-- Tambahkan Label Text agar lebih jelas --}}
                                            @if($p->user && $p->user->status == 'nonaktif')
                                                <span class="text-[8px] bg-gray-100 text-gray-500 px-1 rounded font-bold uppercase">Selesai</span>
                                            @endif
                                        </div>
                                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter mt-0.5">
                                            {{ $p->kelompok->nama_kelompok ?? 'Peserta Individu' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- SKEMA --}}
                            <td class="px-6 py-5">
                                <span class="text-[11px] font-extrabold text-gray-600 uppercase tracking-tight">
                                    {{ $p->skemaArisan->nama_skema ?? '-' }}
                                </span>
                            </td>

                            {{-- PROGRES --}}
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-24 h-2 bg-gray-100 rounded-full overflow-hidden shadow-inner">
                                        <div class="h-full transition-all duration-500 {{ $persen == 100 ? 'bg-green-500' : 'bg-blue-500' }}" style="width: {{ $persen }}%"></div>
                                    </div>
                                    <span class="text-[10px] font-black text-slate-700">{{ $lunas }}/{{ $tenor }}</span>
                                </div>
                            </td>

                            {{-- STATUS --}}
                            <td class="px-6 py-5 text-center">
                                @if($isMenang)
                                    <div class="inline-flex flex-col items-center">
                                        <span class="px-3 py-1 bg-green-800 text-white text-[9px] font-black uppercase rounded-lg shadow-md shadow-green-900/20">MENANG</span>
                                        <span class="text-[8px] text-green-600 font-bold mt-1 uppercase tracking-tighter">TAHUN {{ $p->undian->tahun_pelaksanaan }}</span>
                                    </div>
                                @else
                                    <span class="px-3 py-1 bg-gray-100 text-gray-400 text-[9px] font-black uppercase rounded-lg border border-gray-200">BELUM MENANG</span>
                                @endif
                            </td>

                            {{-- NOMINAL --}}
                            <td class="px-8 py-5 text-right">
                                @if($isMenang)
                                    <p class="text-sm font-black text-red-600">Rp {{ number_format($p->pengeluaranArisan->nominal, 0, ',', '.') }}</p>
                                    <p class="text-[9px] text-gray-400 uppercase font-black italic tracking-tighter">Dana Terealisasi</p>
                                @else
                                    <p class="text-sm font-black text-green-700">Rp {{ number_format($nominalTabungan, 0, ',', '.') }}</p>
                                    <p class="text-[9px] text-gray-400 uppercase font-black italic tracking-tighter">Saldo</p>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-4 text-gray-200">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                    </div>
                                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Tidak ada data yang sesuai filter</h3>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
            {{-- Info Data --}}
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                Menampilkan {{ $pesertas->firstItem() ?? 0 }} - {{ $pesertas->lastItem() ?? 0 }} 
                dari {{ $pesertas->total() }} Data
            </div>
            
            {{-- Navigasi Halaman --}}
            <div class="custom-pagination">
                {{ $pesertas->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    .animate-fadeIn { animation: fadeIn 0.5s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    
    /* Custom Scrollbar for Tabs */
    .overflow-x-auto::-webkit-scrollbar { height: 0px; }
    
    /* Container Navigasi */
    .custom-pagination nav {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Sembunyikan elemen bawaan Laravel yang tidak diperlukan */
    .custom-pagination nav p {
        display: none !important;
    }

    .custom-pagination flex div:first-child {
        display: none !important;
    }

    /* Styling Utama Tombol & Angka */
    .custom-pagination a, 
    .custom-pagination span[aria-current="page"] span,
    .custom-pagination span[aria-disabled="true"] span {
        display: flex !important;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 12px !important;
        font-size: 11px !important;
        font-weight: 800 !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #e2e8f0 !important;
        background: white !important;
        color: #64748b !important;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        text-decoration: none !important;
    }

    /* Status Halaman Aktif */
    .custom-pagination span[aria-current="page"] span {
        background: #064e3b !important; /* Hijau Tua Khas Masjid */
        color: white !important;
        border-color: #064e3b !important;
        box-shadow: 0 4px 12px rgba(6, 78, 59, 0.2);
    }

    /* Efek Hover untuk Link */
    .custom-pagination a:hover {
        border-color: #064e3b !important;
        color: #064e3b !important;
        background: #f0fdf4 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Styling Ikon Panah */
    .custom-pagination svg {
        width: 16px !important;
        height: 16px !important;
        stroke-width: 3;
    }
</style>
@endsection