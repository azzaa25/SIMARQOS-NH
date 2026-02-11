@extends('admin.layout.app')

@section('content')
<div class="space-y-8">
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-bold text-green-900 leading-tight">Dashboard</h1>
            <p class="text-sm text-gray-500">Selamat datang kembali, Admin</p>
        </div>
        </div>

    <div class="space-y-4">
        <h2 class="text-lg font-bold text-green-900 leading-none">Dashboard Utama</h2>
        <p class="text-xs text-gray-400">Ringkasan data dan aktivitas terkini sistem arisan qurban dan kegiatan sosial</p>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white p-5 rounded-[24px] shadow-sm border border-gray-50 relative overflow-hidden group hover:shadow-md transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 bg-green-50 text-green-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <span class="text-[10px] font-bold text-green-500 flex items-center">↑ 12%</span>
                </div>
                <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Total Peserta Arisan</p>
                <h3 class="text-3xl font-black text-gray-800 mt-1">248</h3>
            </div>

            <div class="bg-white p-5 rounded-[24px] shadow-sm border border-gray-50 group hover:shadow-md transition-all text-center lg:text-left">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 bg-orange-50 text-orange-500 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <span class="text-[10px] font-bold text-green-500 flex items-center">↑ 8%</span>
                </div>
                <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Total Skema Arisan</p>
                <h3 class="text-3xl font-black text-gray-800 mt-1">12</h3>
            </div>

            <div class="bg-white p-5 rounded-[24px] shadow-sm border border-gray-50 group hover:shadow-md transition-all lg:col-span-1 sm:col-span-2 lg:col-span-1">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 bg-green-50 text-green-700 rounded-xl flex items-center justify-center font-bold">$</div>
                    <span class="text-[10px] font-bold text-green-500 flex items-center">↑ 24%</span>
                </div>
                <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest leading-tight">Total Iuran Bulan Ini</p>
                <h3 class="text-2xl font-black text-gray-800 mt-1">Rp 45.750.000</h3>
            </div>

            <div class="bg-white p-5 rounded-[24px] shadow-sm border border-gray-50 group hover:shadow-md transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 bg-cyan-50 text-cyan-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <span class="text-[10px] font-bold text-green-500 flex items-center">↑ 18%</span>
                </div>
                <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Pembayaran Lunas</p>
                <h3 class="text-3xl font-black text-gray-800 mt-1">196</h3>
            </div>

            <div class="bg-white p-5 rounded-[24px] shadow-sm border border-gray-50 group hover:shadow-md transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 bg-yellow-50 text-yellow-600 rounded-xl flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <span class="text-[10px] font-bold text-green-500 flex items-center">↑ 5%</span>
                </div>
                <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Kegiatan Sosial Aktif</p>
                <h3 class="text-3xl font-black text-gray-800 mt-1">8</h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-white rounded-[32px] shadow-sm border border-gray-50 overflow-hidden flex flex-col">
            <div class="p-6 flex justify-between items-center border-b border-gray-50">
                <h3 class="font-bold text-green-900 flex items-center gap-2">
                    <span class="text-gray-400 font-bold">$</span> Transaksi Pembayaran Terbaru
                </h3>
                <a href="#" class="text-[10px] font-bold text-gray-400 hover:text-green-700 uppercase tracking-widest flex items-center gap-1">
                    Lihat Semua <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
            <div class="p-2 space-y-1">
                @php
                    $transaksi = [
                        ['nama' => 'Ahmad Fauzi', 'paket' => 'Skema Qurban Premium', 'tanggal' => '5 Feb 2024', 'nominal' => '2.500.000', 'status' => 'LUNAS', 'color' => 'bg-green-100 text-green-600'],
                        ['nama' => 'Siti Aisyah', 'paket' => 'Skema Qurban Standar', 'tanggal' => '5 Feb 2024', 'nominal' => '1.800.000', 'status' => 'LUNAS', 'color' => 'bg-green-100 text-green-600'],
                        ['nama' => 'Muhammad Rizki', 'paket' => 'Skema Qurban Premium', 'tanggal' => '4 Feb 2024', 'nominal' => '2.500.000', 'status' => 'TERTUNDA', 'color' => 'bg-orange-100 text-orange-600'],
                        ['nama' => 'Fatimah Zahra', 'paket' => 'Skema Qurban Ekonomis', 'tanggal' => '4 Feb 2024', 'nominal' => '1.200.000', 'status' => 'LUNAS', 'color' => 'bg-green-100 text-green-600'],
                        ['nama' => 'Abdul Rahman', 'paket' => 'Skema Qurban Standar', 'tanggal' => '3 Feb 2024', 'nominal' => '1.800.000', 'status' => 'LUNAS', 'color' => 'bg-green-100 text-green-600'],
                    ];
                @endphp
                @foreach($transaksi as $t)
                <div class="flex items-center justify-between p-4 rounded-2xl hover:bg-gray-50 transition-colors border-b last:border-0 border-gray-50">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-green-800 text-white rounded-xl flex items-center justify-center text-xs font-bold uppercase">
                            {{ substr($t['nama'], 0, 1) }}{{ substr(strrchr($t['nama'], ' '), 1, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800 leading-none">{{ $t['nama'] }}</p>
                            <p class="text-[10px] text-gray-400 mt-1 uppercase font-bold tracking-tighter">{{ $t['paket'] }} • {{ $t['tanggal'] }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-black text-gray-800">Rp {{ $t['nominal'] }}</p>
                        <span class="px-3 py-1 text-[9px] font-black rounded-full uppercase {{ $t['color'] }}">{{ $t['status'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 overflow-hidden">
            <div class="p-6 flex items-center gap-2 border-b border-gray-50">
                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="font-bold text-green-900 leading-none">Undian Arisan Mendatang</h3>
            </div>
            <div class="p-6 space-y-4">
                @php
                    $undian = [
                        ['nama' => 'Skema Premium 2024', 'tanggal' => '10 Feb 2024', 'peserta' => '48 Peserta'],
                        ['nama' => 'Skema Standar 2024', 'tanggal' => '15 Feb 2024', 'peserta' => '36 Peserta'],
                        ['nama' => 'Skema Ekonomis 2024', 'tanggal' => '20 Feb 2024', 'peserta' => '24 Peserta'],
                    ];
                @endphp
                @foreach($undian as $u)
                <div class="p-4 rounded-[24px] border border-gray-100 flex flex-col gap-3 group hover:border-green-200 transition-all">
                    <div class="flex justify-between items-center leading-none">
                        <h4 class="text-sm font-bold text-gray-800">{{ $u['nama'] }}</h4>
                        <span class="text-[9px] font-black text-orange-600 bg-orange-50 px-2 py-1 rounded-md uppercase">TERJADWAL</span>
                    </div>
                    <div class="flex items-center gap-4 text-[10px] text-gray-400 font-bold uppercase tracking-tighter">
                        <div class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> {{ $u['tanggal'] }}
                        </div>
                        <div class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg> {{ $u['peserta'] }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="space-y-4">
        <div class="flex items-center gap-2">
            <div class="w-6 h-6 bg-green-50 text-green-700 rounded-full flex items-center justify-center"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg></div>
            <h3 class="font-bold text-green-900 leading-none uppercase text-sm tracking-widest">Aksi Cepat</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('admin.skema.create') }}" class="flex items-center justify-between p-5 bg-white rounded-2xl border border-gray-50 shadow-sm hover:shadow-md hover:border-green-200 transition-all group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-gray-50 text-gray-500 rounded-xl flex items-center justify-center group-hover:bg-green-50 group-hover:text-green-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <span class="text-sm font-bold text-gray-600 group-hover:text-green-900 transition-colors tracking-tight">Tambah Skema Arisan</span>
                </div>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
            </a>
            
            <a href="#" class="flex items-center justify-between p-5 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-green-200 transition-all group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-gray-50 text-gray-500 rounded-xl flex items-center justify-center group-hover:bg-green-50 group-hover:text-green-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    </div>
                    <span class="text-sm font-bold text-gray-600 group-hover:text-green-900 transition-colors tracking-tight">Tambah Peserta</span>
                </div>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>
    </div>
</div>
@endsection