@extends('peserta.layout.app')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <nav class="flex text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em] mb-1">
            <a href="{{ route('peserta.dashboard') }}" class="hover:text-green-700">Dashboard</a>
            <span class="mx-2">/</span>
            <span class="text-green-800">Jadwal & Tagihan</span>
        </nav>
        <h1 class="text-2xl font-extrabold text-green-900 tracking-tight">Kalender Pembayaran Arisan</h1>
        <p class="text-xs text-gray-500 mt-1">Skema Durasi: <strong>{{ $tenor }} Bulan</strong></p>
    </div>
    
    {{-- Progress Bar --}}
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 min-w-[280px]">
        <div class="flex justify-between text-[10px] font-bold uppercase mb-2">
            <span class="text-gray-400">Total Progres</span>
            <span class="text-green-600">{{ $totalLunas }} / {{ $tenor }} Bulan Lunas</span>
        </div>
        <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
            <div class="bg-green-500 h-full transition-all duration-1000" style="width: {{ $progresPersen }}%"></div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    @foreach($daftarBulan as $item)
        @php
            $t = $item['tagihan'];
            $status = $t ? $t->status_pembayaran : 'locked'; 
        @endphp

        <div class="relative">
            <div class="h-48 rounded-[32px] p-6 border-2 transition-all duration-300 flex flex-col justify-between
                {{ $status == 'sukses' ? 'bg-green-50 border-green-100 shadow-sm' : '' }}
                {{ $status == 'pending' ? 'bg-white border-orange-300 shadow-xl shadow-orange-100 scale-[1.02] z-10' : '' }}
                {{ $status == 'locked' ? 'bg-gray-50 border-dashed border-gray-200 opacity-60' : '' }}">
                
                <div>
                    <div class="flex justify-between items-start">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black uppercase tracking-widest {{ $status == 'sukses' ? 'text-green-400' : ($status == 'pending' ? 'text-orange-400' : 'text-gray-300') }}">
                                {{ $item['short_nama'] }}
                            </span>
                            <span class="text-[9px] font-bold text-gray-400">{{ $item['tahun'] }}</span>
                        </div>
                        
                        @if($status == 'sukses')
                            <span class="bg-green-500 text-white p-1 rounded-full">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </span>
                        @elseif($status == 'pending')
                            <span class="flex h-2 w-2 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                            </span>
                        @else
                            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        @endif
                    </div>
                    <h3 class="font-bold text-gray-800 mt-1">{{ $item['bulan_nama'] }}</h3>
                </div>

                <div class="mt-auto">
                    @if($status == 'sukses')
                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-[9px] font-bold text-green-600 uppercase italic">Lunas</p>
                                <p class="text-xs font-black text-gray-700">Rp {{ number_format($t->nominal, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @elseif($status == 'pending')
                        <p class="text-[9px] font-bold text-orange-500 uppercase mb-2">Tagihan Tersedia</p>
                        {{-- Tombol diubah menjadi Link ke halaman transaksi --}}
                        <a href="{{ route('peserta.transaksi.index') }}" 
                           class="block w-full py-2.5 bg-orange-500 text-white text-[10px] text-center font-black uppercase tracking-widest rounded-xl hover:bg-orange-600 shadow-lg shadow-orange-100 transition-all active:scale-95">
                            Bayar Rp {{ number_format($t->nominal, 0, ',', '.') }}
                        </a>
                    @else
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Belum Rilis</p>
                            <div class="w-full bg-gray-200 h-1 rounded-full"></div>
                            <p class="text-[8px] text-gray-400 italic">Menunggu admin</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection