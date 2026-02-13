@extends('admin.layout.app')

@section('content')
<div class="max-w-5xl mx-auto h-full flex flex-col justify-center animate-fadeIn">
    {{-- BREADCRUMB & HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-4 gap-2">
        <div>
            <nav class="flex text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em] mb-1">
                <a href="{{ route('admin.peserta.index') }}" class="hover:text-green-700 transition-colors">Manajemen Peserta</a>
                <span class="mx-2 text-gray-300">/</span>
                <span class="text-green-800">Profil Detail</span>
            </nav>
            <h1 class="text-2xl font-extrabold text-green-900 leading-tight tracking-tight">Detail Informasi Peserta</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.peserta.index') }}" class="px-4 py-2 bg-gray-100 text-gray-500 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-200 transition-all">
                Kembali
            </a>
            <a href="{{ route('admin.peserta.edit', $peserta->id_pesertaarisan) }}" class="px-4 py-2 bg-orange-50 text-orange-600 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-orange-100 transition-all border border-orange-100">
                Edit Data
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        {{-- KIRI: CARD PROFIL UTAMA (4 Kolom) --}}
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-[32px] shadow-xl shadow-gray-200/40 border border-gray-50 p-8 text-center relative overflow-hidden">
                {{-- Decorative Background --}}
                <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-br from-green-800 to-green-600"></div>
                
                <div class="relative mt-8">
                    {{-- Avatar Inisial --}}
                    <div class="w-24 h-24 bg-white p-2 rounded-[32px] mx-auto shadow-xl">
                        <div class="w-full h-full bg-green-100 text-green-800 rounded-[24px] flex items-center justify-center text-2xl font-black uppercase shadow-inner">
                            {{ substr($peserta->nama, 0, 1) }}{{ strpos($peserta->nama, ' ') !== false ? substr(strrchr($peserta->nama, ' '), 1, 1) : '' }}
                        </div>
                    </div>
                    
                    <h2 class="mt-4 text-xl font-black text-gray-800 leading-tight">{{ $peserta->nama }}</h2>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em] mt-1">ID Peserta: #{{ str_pad($peserta->id_pesertaarisan, 4, '0', STR_PAD_LEFT) }}</p>
                    
                    <div class="mt-6">
                        @if($peserta->user->status == 'aktif')
                            <span class="px-6 py-2 text-[10px] font-black rounded-full tracking-widest border-2 bg-blue-50 text-blue-600 border-blue-100 shadow-sm shadow-blue-900/10">
                                AKTIF
                            </span>
                        @else
                            <span class="px-6 py-2 text-[10px] font-black rounded-full tracking-widest border-2 bg-red-50 text-red-600 border-red-100 shadow-sm shadow-red-900/10">
                                NONAKTIF
                            </span>
                        @endif
                    </div>
                </div>

                <div class="mt-8 pt-8 border-t border-gray-50 flex justify-around">
                    <div class="text-center">
                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter">Total Iuran</p>
                        <p class="text-sm font-black text-green-700">0x</p>
                    </div>
                    <div class="w-px h-8 bg-gray-100"></div>
                    <div class="text-center">
                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter">Kehadiran</p>
                        <p class="text-sm font-black text-gray-800">100%</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- KANAN: DETAIL DATA (8 Kolom) --}}
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white rounded-[32px] shadow-xl shadow-gray-200/40 border border-gray-50 overflow-hidden">
                <div class="p-6 border-b border-gray-50 bg-gray-50/30 flex items-center gap-3">
                    <div class="w-8 h-8 bg-green-800 text-white rounded-xl flex items-center justify-center shadow-lg shadow-green-900/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-5m-4 0V5a2 2 0 012-2h2a2 2 0 012 2v1m-4 0h4"/></svg>
                    </div>
                    <h3 class="text-sm font-black text-green-900 uppercase tracking-widest">Informasi Lengkap</h3>
                </div>

                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                    {{-- Row 1 --}}
                    <div class="space-y-1">
                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest leading-none">Email Terdaftar</p>
                        <p class="text-sm font-bold text-gray-700 break-all">{{ $peserta->user->email ?? 'Tidak terhubung ke sistem' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest leading-none">Nomor WhatsApp</p>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-gray-700">{{ $peserta->no_hp }}</span>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $peserta->no_hp) }}" target="_blank" class="p-1 bg-green-100 text-green-600 rounded-md hover:bg-green-600 hover:text-white transition-all">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </a>
                        </div>
                    </div>

                    {{-- Row 2 --}}
                    <div class="space-y-1">
                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest leading-none">Skema Arisan</p>
                        <p class="text-sm font-bold text-green-700">{{ $peserta->skemaArisan->nama_skema ?? 'Belum memilih skema' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest leading-none">Tanggal Bergabung</p>
                        <p class="text-sm font-bold text-gray-700">{{ $peserta->created_at->format('d F Y') }}</p>
                    </div>

                    {{-- Row 3 Full --}}
                    <div class="md:col-span-2 space-y-1 pt-4 border-t border-gray-50">
                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest leading-none">Alamat Domisili Lengkap</p>
                        <p class="text-sm font-medium text-gray-600 leading-relaxed italic">"{{ $peserta->alamat }}"</p>
                    </div>
                </div>
            </div>

            {{-- FOOTER INFO --}}
            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 flex items-start gap-4">
                <div class="w-8 h-8 bg-blue-500 text-white rounded-lg flex items-center justify-center shrink-0 shadow-md shadow-blue-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h4 class="text-xs font-black text-blue-900 uppercase tracking-widest leading-none mb-1">Catatan Admin</h4>
                    <p class="text-[11px] text-blue-800/70 leading-relaxed font-medium italic">Data ini terhubung langsung dengan akun pengguna sistem. Perubahan nama pada halaman ini akan merubah identitas login peserta.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn { animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
@endsection