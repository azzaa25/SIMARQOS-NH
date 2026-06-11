@extends('admin.layout.app')

@section('content')
<div class="max-w-2xl mx-auto py-8 px-4 animate-fadeIn">
    {{-- Header Section --}}
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 text-green-700 rounded-2xl flex items-center justify-center shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-black text-green-900 leading-tight">Tambah Anggota</h1>
                <p class="text-sm text-gray-500 font-medium">{{ $kelompok->nama_kelompok }}</p>
            </div>
        </div>
        <a href="{{ route('admin.peserta.index') }}" class="text-gray-400 hover:text-red-500 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
        </a>
    </div>

    {{-- Info Card --}}
    <div class="bg-gradient-to-br from-green-700 to-green-800 rounded-[28px] p-6 mb-8 text-white shadow-xl shadow-green-900/20 relative overflow-hidden">
        <svg class="absolute -right-4 -bottom-4 w-32 h-32 text-white/10" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-4">
                <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-[10px] font-black uppercase tracking-widest">Informasi Kelompok</span>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-green-200 text-[10px] font-bold uppercase tracking-wider">Skema Arisan</p>
                    <p class="font-bold text-sm">{{ $ketua->skemaArisan->nama_skema }}</p>
                </div>
                <div>
                    <p class="text-green-200 text-[10px] font-bold uppercase tracking-wider">Alamat Domisili</p>
                    <p class="font-bold text-sm truncate">{{ $ketua->alamat }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 p-8">
        <form action="{{ url('admin/peserta/kelompok/'.$kelompok->id_kelompok.'/tambah-anggota') }}" method="POST" class="space-y-6">
            @csrf
            
            {{-- Input Nama --}}
            <div class="space-y-2">
                <label class="flex items-center gap-2 text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Nama Lengkap Anggota
                </label>
                <div class="relative group">
                    <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Masukkan nama lengkap" 
                        class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-[20px] text-sm font-bold text-slate-700 outline-none transition-all focus:bg-white focus:ring-4 focus:ring-green-500/10 focus:border-green-600 group-hover:border-gray-300">
                </div>
                @error('nama') <p class="text-[10px] text-red-500 font-bold mt-1 ml-2 flex items-center gap-1"><span class="w-1 h-1 bg-red-500 rounded-full"></span> {{ $message }}</p> @enderror
            </div>

            {{-- Input WhatsApp --}}
            <div class="space-y-2">
                <label class="flex items-center gap-2 text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    Nomor WhatsApp Aktif
                </label>
                <div class="relative group">
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}" required placeholder="Contoh: 085336391316" 
                        class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-[20px] text-sm font-bold text-slate-700 outline-none transition-all focus:bg-white focus:ring-4 focus:ring-green-500/10 focus:border-green-600 group-hover:border-gray-300">
                </div>
                @error('no_hp') <p class="text-[10px] text-red-500 font-bold mt-1 ml-2 flex items-center gap-1"><span class="w-1 h-1 bg-red-500 rounded-full"></span> {{ $message }}</p> @enderror
            </div>

            {{-- Action Buttons --}}
            <div class="pt-4 flex flex-col sm:flex-row gap-3">
                <a href="{{ route('admin.peserta.index') }}" 
                    class="flex-1 order-2 sm:order-1 text-center py-4 bg-gray-100 text-gray-500 text-xs font-black uppercase tracking-[0.1em] rounded-2xl hover:bg-gray-200 transition-all active:scale-95">
                    Batalkan
                </a>
                <button type="submit" 
                    class="flex-1 order-1 sm:order-2 py-4 bg-green-700 text-white text-xs font-black uppercase tracking-[0.2em] rounded-2xl shadow-lg shadow-green-900/20 hover:bg-green-800 hover:shadow-green-900/30 transition-all active:scale-95">
                    Simpan Anggota
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .animate-fadeIn {
        animation: fadeIn 0.5s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    /* Hide arrow on number input */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>
@endsection