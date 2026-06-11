@extends('admin.layout.app')

@section('content')

{{-- Perintah agar Carbon menggunakan Bahasa Indonesia --}}
@php \Carbon\Carbon::setLocale('id'); @endphp

{{-- 1. HEADER HALAMAN --}}
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 animate-fadeIn">
    <div>
        <h1 class="text-3xl font-black text-green-900 leading-tight uppercase tracking-tighter">Laporan Kegiatan Sosial</h1>
        <p class="text-sm text-gray-500 italic font-medium">Arsip data dan pencetakan laporan resmi Masjid Nurul Huda.</p>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="cetakLaporanTerpilih()" id="btnCetak" disabled
            class="bg-gray-400 text-white px-8 py-3 rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg flex items-center gap-2 transition-all cursor-not-allowed opacity-70">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Cetak PDF
        </button>
    </div>
</div>

{{-- 2. STATISTIK SINGKAT --}}
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-[28px] shadow-sm border border-gray-100 flex items-center gap-4 transition-all hover:shadow-md">
        <div class="w-14 h-14 bg-green-50 text-green-700 rounded-2xl flex items-center justify-center">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M12 4v16m8-8H4"></path></svg>
        </div>
        <div>
            <p class="text-2xl font-black text-green-700 tracking-tighter">Rp {{ number_format($totalDanaMasuk, 0, ',', '.') }}</p>
            <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Total Dana Masuk</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-[28px] shadow-sm border border-gray-100 flex items-center gap-4 transition-all hover:shadow-md">
        <div class="w-14 h-14 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <p class="text-2xl font-black text-red-600 tracking-tighter">Rp {{ number_format($totalDanaKeluar, 0, ',', '.') }}</p>
            <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Dana Penyaluran</p>
        </div>
    </div>

    <div class="bg-[#064e3b] p-6 rounded-[28px] shadow-xl text-white flex items-center gap-4 transition-all hover:scale-[1.02]">
        <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
        </div>
        <div>
            <p class="text-2xl font-black tracking-tighter">Rp {{ number_format($totalDanaMasuk - $totalDanaKeluar, 0, ',', '.') }}</p>
            <p class="text-[10px] text-green-200 uppercase font-black tracking-widest opacity-80">Sisa Kas Sosial</p>
        </div>
    </div>
</div>

{{-- 3. FILTER TABS --}}
<div class="mb-6 flex bg-white p-1.5 rounded-2xl border border-gray-200 shadow-sm w-fit overflow-x-auto">
    @php $currentStatus = request('status'); @endphp
    <a href="{{ route('admin.sosial.laporan') }}" class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase transition-all {{ !$currentStatus ? 'bg-[#064e3b] text-white shadow-md' : 'text-gray-400 hover:bg-gray-50' }}">Semua</a>
    <a href="{{ route('admin.sosial.laporan', ['status' => 'rencana']) }}" class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase transition-all {{ $currentStatus == 'rencana' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-400 hover:bg-gray-50' }}">Rencana</a>
    <a href="{{ route('admin.sosial.laporan', ['status' => 'berlangsung']) }}" class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase transition-all {{ $currentStatus == 'berlangsung' ? 'bg-orange-500 text-white shadow-md' : 'text-gray-400 hover:bg-gray-50' }}">Berlangsung</a>
    <a href="{{ route('admin.sosial.laporan', ['status' => 'selesai']) }}" class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase transition-all {{ $currentStatus == 'selesai' ? 'bg-green-600 text-white shadow-md' : 'text-gray-400 hover:bg-gray-50' }}">Selesai</a>
</div>

{{-- 4. TABEL DATA --}}
<div class="bg-white rounded-[32px] shadow-2xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-white text-[10px] uppercase tracking-[0.2em] text-gray-400 font-black border-b border-gray-100">
                <tr>
                    <th class="px-8 py-5 text-center w-20">Pilih</th>
                    <th class="px-8 py-5">Informasi Agenda</th>
                    <th class="px-8 py-5 text-center">Status</th>
                    <th class="px-8 py-5 text-center">Tanggal</th>
                    <th class="px-8 py-5 text-right">Donasi Masuk</th>
                    <th class="px-8 py-5 text-right">Dana Keluar</th>
                    <th class="px-8 py-5 text-center w-20">Lihat Donatur</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm">
                @forelse($kegiatanSelesai as $item)
                <tr class="hover:bg-green-50/40 transition-all cursor-pointer group" onclick="pilihBaris('radio_{{ $item->id_kegiatan }}')">
                    <td class="px-8 py-6 text-center">
                        <input type="radio" name="selected_kegiatan" id="radio_{{ $item->id_kegiatan }}" value="{{ $item->id_kegiatan }}" class="w-6 h-6 text-green-600 border-gray-300 focus:ring-green-500 cursor-pointer" onclick="handleRadioClick(this, event)">
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex flex-col">
                            <span class="font-extrabold text-slate-800 text-base uppercase tracking-tight group-hover:text-[#147a54]">{{ $item->nama_kegiatan }}</span>
                            <span class="text-[9px] text-green-600 font-black uppercase mt-1 tracking-widest">{{ $item->kategori->nama_kategori ?? 'Umum' }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-center">
                        @php
                            $statusClasses = [
                                'rencana' => 'bg-blue-50 text-blue-600 border-blue-100',
                                'berlangsung' => 'bg-orange-50 text-orange-600 border-orange-100',
                                'selesai' => 'bg-green-50 text-green-600 border-green-100'
                            ];
                        @endphp
                        <span class="px-4 py-1.5 border rounded-xl text-[9px] font-black uppercase {{ $statusClasses[$item->status_kegiatan] ?? 'bg-gray-50' }}">
                            {{ $item->status_kegiatan }}
                        </span>
                    </td>
                    <td class="px-8 py-6 text-center font-bold text-gray-500 uppercase text-[11px]">
                        {{-- Format Bulan Indonesia --}}
                        {{ \Carbon\Carbon::parse($item->tanggal_kegiatan)->translatedFormat('d F Y') }}
                    </td>
                    <td class="px-8 py-6 text-right font-black text-[#147a54] text-base">
                        Rp {{ number_format($item->total_masuk, 0, ',', '.') }}
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="flex flex-col items-end">
                            <span class="font-black text-red-600 text-base">Rp {{ number_format($item->total_keluar, 0, ',', '.') }}</span>
                            @php
                                $rincianKeluar = \App\Models\DanaSosial::where('id_kegiatan', $item->id_kegiatan)
                                                ->where('tipe_dana', 'keluar')
                                                ->orderBy('tanggal_input', 'desc')->get();
                            @endphp
                            @if($rincianKeluar->count() > 0)
                                <p class="text-[9px] text-gray-400 font-medium italic truncate w-32 text-right">
                                    • {{ $rincianKeluar->first()->keterangan_transaksi }}
                                </p>
                            @endif
                        </div>
                    </td>
                    <td class="px-8 py-6 text-center">
                        @php
                            // Ambil data donatur langsung untuk diselipkan di Button (Anti-Muter)
                            $listDonatur = \App\Models\DanaSosial::where('id_kegiatan', $item->id_kegiatan)
                                            ->where('tipe_dana', 'masuk')
                                            ->whereIn('status_pembayaran', ['success', 'settlement'])
                                            ->orderBy('tanggal_input', 'desc')->get();
                            
                            $donaturData = $listDonatur->map(function($d) {
                                return [
                                    'nama' => $d->nama_donatur ?? 'Hamba Allah',
                                    'nominal' => 'Rp ' . number_format($d->nominal, 0, ',', '.'),
                                    'tgl' => \Carbon\Carbon::parse($d->tanggal_input)->translatedFormat('d M Y')
                                ];
                            });
                        @endphp
                        <button onclick="intipLangsung(event, '{{ $item->nama_kegiatan }}', {{ json_encode($donaturData) }})" 
                                class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-full transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-8 py-32 text-center text-gray-300 font-black uppercase text-xs">Tidak Ada Data Agenda</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- PAGINATION --}}
    <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Menampilkan {{ $kegiatanSelesai->firstItem() ?? 0 }} - {{ $kegiatanSelesai->lastItem() ?? 0 }} dari {{ $kegiatanSelesai->total() }} Data</div>
        <div class="custom-pagination">{{ $kegiatanSelesai->links() }}</div>
    </div>
</div>

{{-- MODAL INTIP DONATUR (Anti-Muter) --}}
<div id="modalIntip" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="tutupIntip()"></div>
    <div class="relative bg-white w-full max-w-md rounded-[40px] shadow-2xl overflow-hidden animate-fadeIn">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <div>
                <h3 class="text-xl font-black text-slate-800 uppercase tracking-tighter">Riwayat Donasi</h3>
                <p id="intipJudul" class="text-[10px] text-blue-600 font-black uppercase mt-1 tracking-widest"></p>
            </div>
            <button onclick="tutupIntip()" class="w-10 h-10 flex items-center justify-center bg-white text-gray-400 rounded-full hover:text-red-500 shadow-sm transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="listDonatur" class="p-6 max-h-[450px] overflow-y-auto space-y-3 custom-scrollbar">
            {{-- Isi donatur akan di-inject lewat JS --}}
        </div>
        <div class="p-6 bg-gray-50 border-t border-gray-100 text-center">
            <button onclick="tutupIntip()" class="px-8 py-2.5 bg-white border border-gray-200 rounded-xl text-[10px] font-black uppercase tracking-widest text-gray-400">Tutup</button>
        </div>
    </div>
</div>

<script>
    let lastChecked = null;
    function pilihBaris(radioId) {
        const radio = document.getElementById(radioId);
        if (lastChecked === radio) { radio.checked = false; lastChecked = null; } 
        else { radio.checked = true; lastChecked = radio; }
        updateButtonState();
    }
    function handleRadioClick(radio, event) { event.stopPropagation(); if (lastChecked === radio) { radio.checked = false; lastChecked = null; } else { lastChecked = radio; } updateButtonState(); }
    
    function updateButtonState() {
        const btn = document.getElementById('btnCetak');
        const selected = document.querySelector('input[name="selected_kegiatan"]:checked');
        if (selected) { 
            btn.disabled = false; btn.classList.remove('bg-gray-400', 'cursor-not-allowed', 'opacity-70'); 
            btn.classList.add('bg-[#064e3b]', 'hover:bg-black', 'active:scale-95'); 
        } else { 
            btn.disabled = true; btn.classList.add('bg-gray-400', 'cursor-not-allowed', 'opacity-70'); 
            btn.classList.remove('bg-[#064e3b]', 'hover:bg-black'); 
        }
    }

    function cetakLaporanTerpilih() {
        const selected = document.querySelector('input[name="selected_kegiatan"]:checked');
        if (selected) window.location.href = `{{ url('/admin/sosial/laporan/pdf') }}/${selected.value}`;
    }

    // LOGIKA INTIP LANGSUNG (TANPA FETCH/MUTER)
    function intipLangsung(event, namaKegiatan, dataDonatur) {
        event.stopPropagation();
        document.getElementById('intipJudul').innerText = namaKegiatan;
        const container = document.getElementById('listDonatur');
        container.innerHTML = '';

        if(dataDonatur.length > 0) {
            dataDonatur.forEach(d => {
                container.innerHTML += `
                    <div class="flex justify-between items-center p-5 bg-white rounded-3xl border border-gray-100 shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2"/></svg>
                            </div>
                            <div><p class="text-[11px] font-black text-slate-800 uppercase leading-none">${d.nama}</p><p class="text-[9px] text-gray-400 font-bold mt-1 uppercase">${d.tgl}</p></div>
                        </div>
                        <p class="text-sm font-black text-green-700 tracking-tighter">${d.nominal}</p>
                    </div>`;
            });
        } else {
            container.innerHTML = '<div class="py-12 text-center text-gray-300 font-black uppercase text-[10px] tracking-widest italic">Belum ada donasi masuk</div>';
        }
        document.getElementById('modalIntip').classList.remove('hidden');
    }

    function tutupIntip() { document.getElementById('modalIntip').classList.add('hidden'); }
</script>

<style>
    .animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: scale(0.98) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
    input[type="radio"] { appearance: none; border-radius: 50%; border: 2px solid #e2e8f0; transition: 0.2s all linear; outline: none; }
    input[type="radio"]:checked { border: 7px solid #064e3b; background-color: white; }
    .custom-pagination nav { display: inline-flex; align-items: center; gap: 6px; }
    .custom-pagination nav p { display: none !important; }
    .custom-pagination flex div:first-child { display: none !important; }
    .custom-pagination a, .custom-pagination span[aria-current="page"] span, .custom-pagination span[aria-disabled="true"] span { display: flex !important; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 12px; font-size: 11px; font-weight: 800; border: 1px solid #e2e8f0; background: white; color: #64748b; text-decoration: none; }
    .custom-pagination span[aria-current="page"] span { background: #064e3b !important; color: white !important; border-color: #064e3b !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>

@endsection