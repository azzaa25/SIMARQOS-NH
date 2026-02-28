@extends('peserta.layout.app')

@section('content')
{{-- Header --}}
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-green-900 tracking-tight">Laporan Keuangan Diri</h1>
        <div class="flex items-center gap-2 mt-1">
            <p class="text-sm text-gray-500">Kontribusi iuran pada Masjid Nurul Huda</p>
            <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[9px] font-bold rounded-full uppercase tracking-wider">
                {{ $skema->tipe_skema == 'kelompok' ? 'Arisan Kelompok' : 'Tabungan Individu' }}
            </span>
        </div>
    </div>
    
    <div class="text-right">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
            Target Akhir {{ $skema->tipe_skema == 'kelompok' ? 'Kelompok' : 'Pribadi' }}
        </p>
        <p class="text-lg font-black text-green-800">Rp {{ number_format($totalTargetArisan, 0, ',', '.') }}</p>
    </div>
</div>

{{-- Statistik Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    {{-- Card 1: Total Iuran Masuk (Pribadi) --}}
    <div class="bg-[#064e3b] p-6 rounded-[2rem] text-white shadow-xl relative overflow-hidden">
        <div class="relative z-10">
            <p class="text-[10px] font-bold uppercase tracking-widest text-green-300/70 mb-1">Total Iuran Saya</p>
            <h2 class="text-2xl font-black">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</h2>
            <p class="text-[9px] text-green-300/50 mt-2 italic">Uang yang sudah Anda setorkan</p>
        </div>
        <svg class="absolute -right-4 -bottom-4 w-24 h-24 text-white/5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.82v-1.91c-1.84-.25-3.28-1.23-3.97-2.73l1.79-.77c.44 1.13 1.49 1.95 2.68 2.13v-3.72l-2.72-.68c-1.81-.46-3.13-1.55-3.13-3.41 0-1.74 1.34-3.07 3.13-3.44V4h2.82v1.94c1.39.21 2.53.94 3.19 2.05l-1.68.86c-.36-.78-1.03-1.24-1.96-1.39v3.42l2.72.68c1.81.46 3.13 1.55 3.13 3.41 0 1.76-1.34 3.12-3.13 3.44z"/></svg>
    </div>

    {{-- Card 2: Sisa Tanggung Jawab (Dinamis Berdasarkan Tipe) --}}
    <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm relative overflow-hidden">
        @if($skema->tipe_skema == 'kelompok')
            {{-- Tampilan KHUSUS Kelompok --}}
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Sisa Kas Kelompok</p>
            <h2 class="text-2xl font-black text-gray-800">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</h2>
            
            <div class="mt-4">
                <div class="flex justify-between text-[9px] font-bold mb-1">
                    <span class="text-gray-400 uppercase">Progres Dana Kelompok</span>
                    <span class="text-green-600">{{ round($persenLunas, 1) }}%</span>
                </div>
                <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-green-500 transition-all duration-1000" style="width: {{ $persenLunas }}%"></div>
                </div>
                <p class="text-[8px] text-gray-400 mt-2 italic text-center leading-tight">
                    "*Angka berkurang jika Anda atau teman sekelompok membayar."
                </p>
            </div>
        @else
            {{-- Tampilan KHUSUS Individu --}}
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Sisa Komitmen Pribadi</p>
            <h2 class="text-2xl font-black text-gray-800">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</h2>
            
            <div class="mt-4">
                <div class="flex justify-between text-[9px] font-bold mb-1">
                    <span class="text-gray-400 uppercase">Progres Tabungan Saya</span>
                    <span class="text-green-600">{{ round($persenLunas, 1) }}%</span>
                </div>
                <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-green-600 transition-all duration-1000" style="width: {{ $persenLunas }}%"></div>
                </div>
                <p class="text-[8px] text-gray-400 mt-2 italic text-center leading-tight">
                    "Selesaikan iuran Anda sesuai target tenor."
                </p>
            </div>
        @endif
    </div>

    {{-- Card 3: Tagihan Pending (Pribadi) --}}
    <div class="bg-orange-50 p-6 rounded-[2rem] border border-orange-100 relative overflow-hidden">
        <p class="text-[10px] font-bold uppercase tracking-widest text-orange-400 mb-1">Tagihan Saya (Pending)</p>
        <h2 class="text-2xl font-black text-orange-600">Rp {{ number_format($totalPending, 0, ',', '.') }}</h2>
        <a href="{{ route('peserta.transaksi.index') }}" class="inline-block text-[9px] text-orange-500 mt-2 font-bold underline hover:text-orange-700">
            Selesaikan Pembayaran Sekarang →
        </a>
    </div>
</div>

{{-- Info Box Khusus Kelompok --}}
@if($skema->tipe_skema == 'kelompok')
<div class="mb-8 p-5 bg-blue-50 border border-blue-100 rounded-3xl flex items-start gap-4">
    <div class="p-2 bg-blue-500 rounded-xl text-white shrink-0">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    </div>
    <div>
        <h4 class="text-sm font-bold text-blue-900">Catatan Arisan Kelompok</h4>
        <p class="text-xs text-blue-700 leading-relaxed mt-1">
            Sisa Kas Kelompok di atas adalah total kekurangan iuran dari <strong>seluruh anggota kelompok</strong>. Angka ini akan terus berkurang secara otomatis setiap kali ada anggota kelompok yang melakukan pembayaran sukses.
        </p>
    </div>
</div>
@endif

{{-- Tabel Riwayat --}}
<div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
        <h3 class="font-bold text-gray-800">Riwayat Transaksi Saya</h3>
        <div class="flex gap-2">
             <span class="px-3 py-1 bg-white border border-gray-200 text-gray-600 text-[10px] font-bold rounded-full uppercase">Tenor: {{ $skema->durasi_bulan }} Bln</span>
             <span class="px-3 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-full uppercase">Sukses: {{ $transaksi->where('status_pembayaran', 'sukses')->count() }}</span>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50">
                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Periode</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Metode</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nominal</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($transaksi as $t)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-gray-700">{{ \Carbon\Carbon::parse($t->bulan_iuran)->translatedFormat('F Y') }}</p>
                        <p class="text-[10px] text-gray-400 font-mono">{{ $t->id_transaksi }}</p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-[10px] text-gray-500 uppercase font-bold bg-gray-100 px-2 py-1 rounded-lg">{{ $t->metode_pembayaran ?? 'N/A' }}</span>
                    </td>
                    <td class="px-6 py-4 font-bold text-gray-700 text-sm">
                        Rp {{ number_format($t->nominal, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($t->status_pembayaran == 'sukses')
                            <span class="inline-flex items-center gap-1.5 text-green-600 text-[10px] font-black uppercase">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Lunas
                            </span>
                        @elseif($t->status_pembayaran == 'pending')
                            <span class="inline-flex items-center gap-1.5 text-orange-500 text-[10px] font-black uppercase tracking-tighter animate-pulse">
                                <span class="w-1.5 h-1.5 bg-orange-500 rounded-full"></span> Pending
                            </span>
                        @else
                            <span class="text-gray-400 text-[10px] font-black uppercase italic">Batal</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic text-sm">Belum ada riwayat transaksi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection