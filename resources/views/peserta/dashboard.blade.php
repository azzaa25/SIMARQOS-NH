@extends('peserta.layout.app')

@section('content')
<div class="max-w-7xl mx-auto animate-fadeInUp">
    
    {{-- Header Selamat Datang --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight leading-tight">Dashboard Arisan</h1>
            <p class="text-sm text-gray-400 font-medium italic leading-none">Assalamu'alaikum, {{ Auth::user()->nama }} </p>
        </div>
        <div class="flex items-center gap-2 px-4 py-2 bg-green-50 border border-green-100 rounded-2xl">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-600"></span>
            </span>
            <span class="text-[10px] font-black text-[#147a54] uppercase tracking-widest">Akun Terverifikasi</span>
        </div>
    </div>

    {{-- Widget Info Kelompok --}}
    @if($peserta->id_kelompok && $peserta->kelompok)
    <div class="bg-white border border-green-100 p-6 rounded-[32px] mb-8 flex flex-col md:flex-row items-center justify-between gap-6 shadow-sm">
        <div class="flex items-center gap-5">
            <div class="w-14 h-14 bg-[#064e3b] text-white rounded-2xl flex items-center justify-center shadow-lg shadow-green-900/20">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-green-600 uppercase tracking-[0.2em] mb-1">Informasi Kelompok</p>
                <h4 class="text-xl font-black text-slate-800 uppercase tracking-tight">{{ $peserta->kelompok->nama_kelompok }}</h4>
                <p class="text-xs text-gray-400 font-medium italic">Anda tergabung dalam kelompok dengan {{ $anggotaKelompok ? $anggotaKelompok->count() : 0 }} orang anggota.</p>
            </div>
        </div>
        <a href="{{ route('peserta.kelompok.index') }}" class="px-8 py-4 bg-green-50 text-[#147a54] text-[10px] font-black uppercase rounded-2xl hover:bg-[#147a54] hover:text-white transition-all tracking-widest border border-green-100">
            Detail Kelompok
        </a>
    </div>
    @endif

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        {{-- Skema --}}
        <div class="bg-white rounded-[32px] p-6 shadow-sm border border-gray-50 flex flex-col gap-4">
            <div class="w-12 h-12 bg-green-50 text-[#147a54] rounded-2xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Skema Diikuti</p>
                <p class="text-xl font-black text-slate-800 uppercase leading-none mb-1">{{ $peserta->skemaArisan->nama_skema ?? '-' }}</p>
                <p class="text-[10px] text-gray-400 font-medium">Durasi: {{ $peserta->skemaArisan->durasi_tahun ?? '0' }} Tahun</p>
            </div>
        </div>

        {{-- Iuran / Bulan (DILENGKAPI SAFETY CHECK PEMBAGIAN NOL) --}}
        <div class="bg-white rounded-[32px] p-6 shadow-sm border border-gray-50 flex flex-col gap-4">
            <div class="w-12 h-12 bg-green-50 text-[#147a54] rounded-2xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Iuran /Bulan</p>
                <p class="text-xl font-black text-[#147a54]">
                    @php
                        $biaya = $peserta->skemaArisan->biaya_skema ?? 0;
                        $durasi = $peserta->skemaArisan->durasi_tahun ?? 0;
                        // Mencegah Division by Zero
                        $iuranBulan = ($durasi > 0) ? ($biaya / ($durasi * 12)) : 0;
                    @endphp
                    Rp {{ number_format($iuranBulan, 0, ',', '.') }}
                </p>
                <p class="text-[10px] text-gray-400 font-medium tracking-tight uppercase">Tagihan Rutin</p>
            </div>
        </div>

        <div class="bg-white rounded-[32px] p-6 shadow-sm border border-gray-100 flex flex-col gap-4">
            <div class="w-12 h-12 bg-gray-50 text-gray-400 rounded-2xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Status Iuran</p>
                <p class="text-xl font-black text-slate-800">Lancar</p>
                <p class="text-[10px] text-green-600 font-black tracking-widest uppercase italic">Semua Terbayar</p>
            </div>
        </div>

        <div class="bg-[#064e3b] rounded-[32px] p-6 shadow-xl shadow-green-900/20 flex flex-col gap-4 relative overflow-hidden">
            <svg class="absolute -right-4 -bottom-4 w-24 h-24 text-white opacity-5 rotate-12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2z"/></svg>
            <div class="w-12 h-12 bg-white/10 text-white rounded-2xl flex items-center justify-center backdrop-blur-md">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-green-300/60 uppercase tracking-widest mb-1 text-left leading-none">Status Undian</p>
                @if($hasilUndian)
                    <p class="text-lg font-black text-white uppercase leading-tight">TAHUN KE-{{ $hasilUndian->tahun_ke }}</p>
                    <p class="text-[9px] text-green-200/80 font-bold uppercase tracking-widest italic">Pemenang Qurban</p>
                @else
                    <p class="text-lg font-black text-white leading-tight uppercase">Menunggu</p>
                    <p class="text-[9px] text-green-200/80 font-bold uppercase tracking-widest italic">Antrean Undian</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] ml-2 leading-none">Pilihan Skema Qurban</h3>
            <div class="bg-white rounded-[40px] p-10 shadow-sm border border-gray-50 relative overflow-hidden">
                <div class="flex flex-col md:flex-row justify-between gap-10">
                    <div class="space-y-6 flex-1">
                        <div>
                            <h4 class="text-3xl font-black text-[#064e3b] tracking-tighter leading-none">{{ $peserta->skemaArisan->nama_skema ?? 'Skema Belum Dipilih' }}</h4>
                            <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] mt-2">Masjid Nurul Huda Digital</p>
                        </div>
                        <div class="grid grid-cols-2 gap-y-4 gap-x-12">
                            <div>
                                <p class="text-[9px] font-black text-gray-300 uppercase tracking-widest mb-1 leading-none">Total Biaya</p>
                                <p class="text-base font-black text-slate-800 tracking-tight">Rp {{ number_format($biaya, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-300 uppercase tracking-widest mb-1 leading-none">Harga /Tahun</p>
                                <p class="text-base font-black text-[#147a54] tracking-tight">
                                    Rp {{ number_format(($durasi > 0) ? ($biaya / $durasi) : 0, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="md:w-56 flex flex-col gap-3 justify-center">
                        <button class="w-full py-5 bg-[#147a54] text-white rounded-[24px] font-black text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-green-900/20 hover:scale-105 transition-all">
                            Bayar Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="space-y-6">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] ml-2 leading-none">Riwayat Transaksi</h3>
            <div class="bg-white rounded-[40px] p-8 shadow-sm border border-gray-100">
                <p class="text-[10px] font-bold text-gray-300 italic text-center py-10 uppercase tracking-widest leading-relaxed">Belum ada riwayat transaksi pembayaran bulan ini.</p>
            </div>
        </div>
    </div>
</div>
@endsection