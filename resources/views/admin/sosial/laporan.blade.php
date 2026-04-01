@extends('admin.layout.app')

@section('content')

{{-- HEADER HALAMAN --}}
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-green-900 leading-tight">Laporan Kegiatan Sosial</h1>
        <p class="text-sm text-gray-500 italic">Pilih satu kegiatan di bawah untuk mencetak laporan spesifik.</p>
    </div>
    <div class="flex items-center gap-3">
        {{-- Tombol Cetak Utama --}}
        <button onclick="cetakLaporanTerpilih()" id="btnCetak" disabled
            class="bg-gray-400 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg flex items-center gap-2 transition-all cursor-not-allowed opacity-70">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Cetak Laporan Terpilih
        </button>
    </div>
</div>

{{-- STATISTIK SINGKAT --}}
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 md:gap-6 mb-8">
    {{-- Dana Masuk --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 transition-all hover:shadow-md">
        <div class="w-12 h-12 bg-green-50 text-green-700 rounded-xl flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        </div>
        <div>
            <p class="text-xl font-extrabold text-green-700">Rp {{ number_format($totalDanaMasuk, 0, ',', '.') }}</p>
            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Total Dana Masuk</p>
        </div>
    </div>

    {{-- Dana Keluar --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 transition-all hover:shadow-md">
        <div class="w-12 h-12 bg-red-50 text-red-600 rounded-xl flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <p class="text-xl font-extrabold text-red-600">Rp {{ number_format($totalDanaKeluar, 0, ',', '.') }}</p>
            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Dana Keluar</p>
        </div>
    </div>

    {{-- Saldo --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 transition-all hover:shadow-md border-b-4 border-b-green-600">
        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
        </div>
        <div>
            <p class="text-xl font-extrabold text-blue-700">Rp {{ number_format($totalDanaMasuk - $totalDanaKeluar, 0, ',', '.') }}</p>
            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Sisa Kas Sosial</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- TABEL DATA RIWAYAT --}}
    <div class="lg:col-span-2 bg-white rounded-[24px] shadow-xl shadow-gray-200/40 border border-gray-100 overflow-hidden h-fit">
        <div class="p-6 border-b border-gray-50 bg-gray-50/30">
            <h2 class="text-lg font-bold text-green-900 leading-none">Riwayat Kegiatan Selesai</h2>
            <p class="text-xs text-gray-400 mt-1 italic">Klik baris untuk pilih/batal pilih kegiatan</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-[10px] uppercase tracking-widest text-gray-400 font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-center w-12">Pilih</th>
                        <th class="px-6 py-4">Informasi Kegiatan</th>
                        <th class="px-6 py-4">Tanggal Pelaksanaan</th>
                        <th class="px-6 py-4 text-right">Dana Masuk</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @forelse($kegiatanSelesai as $index => $item)
                    <tr class="hover:bg-green-50/50 transition-colors cursor-pointer group" onclick="pilihBaris('radio_{{ $item->id_kegiatan }}')">
                        <td class="px-6 py-4 text-center">
                            <input type="radio" name="selected_kegiatan" id="radio_{{ $item->id_kegiatan }}" 
                                value="{{ $item->id_kegiatan }}" 
                                class="w-5 h-5 text-green-600 border-gray-300 focus:ring-green-500 cursor-pointer"
                                onclick="handleRadioClick(this, event)">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-800 leading-none mb-1 group-hover:text-green-700 transition-colors">{{ $item->nama_kegiatan }}</span>
                                <span class="text-[10px] text-green-600 font-bold uppercase tracking-tighter bg-green-50 w-fit px-2 py-0.5 rounded italic border border-green-100">
                                    {{ $item->kategori->nama_kategori ?? 'Umum' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-600">
                            {{ \Carbon\Carbon::parse($item->tanggal_kegiatan)->translatedFormat('d F Y') }}
                        </td>
                        <td class="px-6 py-4 text-right font-extrabold text-green-700">
                            Rp {{ number_format($item->total_masuk, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-300 italic">
                            Belum ada riwayat kegiatan selesai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- STATISTIK KATEGORI --}}
    <div class="bg-white rounded-[24px] shadow-xl shadow-gray-200/40 border border-gray-100 p-6 h-fit">
        <h3 class="text-sm font-black text-green-900 uppercase tracking-widest mb-6 flex items-center gap-2">
            <div class="w-1.5 h-4 bg-green-600 rounded-full"></div>
            Distribusi Kategori
        </h3>
        
        <div class="space-y-6">
            @php $totalSelesai = $kegiatanSelesai->count(); @endphp
            
            @foreach($laporanKategori as $kat)
            <div>
                <div class="flex justify-between items-end mb-2">
                    <span class="text-sm font-bold text-gray-700">{{ $kat->nama_kategori }}</span>
                    <span class="text-[10px] font-black text-green-600 bg-green-50 px-2 py-0.5 rounded-lg border border-green-100">
                        {{ $kat->kegiatan_count }} Kali
                    </span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-green-600 h-full rounded-full transition-all duration-700" 
                         style="width: {{ $totalSelesai > 0 ? ($kat->kegiatan_count / $totalSelesai) * 100 : 0 }}%">
                    </div>
                </div>
            </div>
            @endforeach

            @if($totalSelesai > 0)
            <div class="mt-8 pt-6 border-t border-dashed border-gray-200">
                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">Total Pelaksanaan</p>
                <p class="text-3xl font-black text-green-900">{{ $totalSelesai }} <span class="text-xs font-normal text-gray-500 tracking-normal">Kegiatan</span></p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- JAVASCRIPT LOGIC --}}
<script>
    let lastChecked = null;

    // Fungsi saat baris tabel diklik
    function pilihBaris(radioId) {
        const radio = document.getElementById(radioId);
        
        if (lastChecked === radio) {
            radio.checked = false;
            lastChecked = null;
        } else {
            radio.checked = true;
            lastChecked = radio;
        }
        updateButtonState();
    }

    // Fungsi saat input radio diklik langsung (mencegah double trigger dari TR)
    function handleRadioClick(radio, event) {
        event.stopPropagation();
        if (lastChecked === radio) {
            radio.checked = false;
            lastChecked = null;
        } else {
            lastChecked = radio;
        }
        updateButtonState();
    }

    function updateButtonState() {
        const btn = document.getElementById('btnCetak');
        const selected = document.querySelector('input[name="selected_kegiatan"]:checked');
        
        if (selected) {
            btn.disabled = false;
            btn.classList.remove('bg-gray-400', 'cursor-not-allowed', 'opacity-70');
            btn.classList.add('bg-[#147a54]', 'hover:bg-green-800', 'active:scale-95');
        } else {
            btn.disabled = true;
            btn.classList.add('bg-gray-400', 'cursor-not-allowed', 'opacity-70');
            btn.classList.remove('bg-[#147a54]', 'hover:bg-green-800', 'active:scale-95');
        }
    }

    function cetakLaporanTerpilih() {
        const selected = document.querySelector('input[name="selected_kegiatan"]:checked');
        if (selected) {
            const id = selected.value;
            window.location.href = `{{ url('/admin/sosial/laporan/pdf') }}/${id}`;
        }
    }
</script>

@endsection