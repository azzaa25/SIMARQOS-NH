@extends('admin.layout.app')

@section('content')
{{-- Container Utama dibatasi agar muat tanpa scroll --}}
<div class="max-w-5xl mx-auto h-[calc(100vh-140px)] flex flex-col justify-center animate-fadeIn">
    
    {{-- HEADER SECTION --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 gap-4 shrink-0">
        <div>
            <nav class="flex text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em] mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-green-700 transition-colors">Dashboard</a>
                <span class="mx-2 text-gray-300">/</span>
                <span class="text-green-800 uppercase">Profil Administrator</span>
            </nav>
            <h1 class="text-3xl font-black text-green-900 tracking-tight leading-none">Profil Saya</h1>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 bg-white text-gray-500 text-[11px] font-black uppercase tracking-widest rounded-2xl border border-gray-100 shadow-sm hover:bg-gray-50 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
            
            <a href="{{ route('admin.manage.index') }}" class="px-5 py-2.5 bg-[#147a54] text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-green-900/20 hover:bg-green-800 transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                Kelola Admin
            </a>
        </div>
    </div>

    {{-- CARD PROFIL UTAMA --}}
    <div class="bg-white rounded-[40px] shadow-[0_20px_60px_rgba(0,0,0,0.03)] border border-gray-50 overflow-hidden flex flex-col min-h-0">
        
        {{-- Elegant Header / Banner --}}
        <div class="h-32 bg-gradient-to-r from-[#147a54] to-[#1e4d3a] relative shrink-0">
            <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
            <div class="absolute -bottom-1 left-0 w-full h-16 bg-gradient-to-t from-white to-transparent"></div>
        </div>
        
        <div class="px-10 pb-10 flex-1 flex flex-col">
            {{-- Identitas Utama --}}
            <div class="relative flex items-end gap-6 -mt-16 mb-12 shrink-0">
                <div class="w-36 h-36 bg-white p-2 rounded-[48px] shadow-2xl shadow-green-900/10">
                    <div class="w-full h-full bg-[#147a54] text-white rounded-[40px] flex items-center justify-center text-5xl font-black shadow-inner border-4 border-green-50 uppercase tracking-tighter">
                        {{ substr($admin->nama, 0, 2) }}
                    </div>
                </div>
                <div class="mb-4">
                    <div class="flex items-center gap-3 mb-1">
                        <h2 class="text-3xl font-black text-gray-800 tracking-tight uppercase">{{ $admin->nama }}</h2>
                        <span class="px-3 py-1 bg-green-50 text-green-700 rounded-full text-[10px] font-black uppercase tracking-widest border border-green-100">
                            Verified Account
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-[0.3em]">{{ $admin->role }} System</p>
                    </div>
                </div>
            </div>

            {{-- Grid Detail Informasi --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-gray-50 pt-10">
                
                <div class="group">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Email Sistem</label>
                    <div class="flex items-center gap-4 p-5 bg-gray-50/50 border border-gray-100 rounded-[24px] group-hover:bg-white group-hover:border-green-200 transition-all group-hover:shadow-md group-hover:shadow-green-900/5">
                        <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-green-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke-width="2"/></svg>
                        </div>
                        <p class="text-[15px] font-bold text-gray-700 truncate">{{ $admin->email }}</p>
                    </div>
                </div>

                <div class="group">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Member Sejak</label>
                    <div class="flex items-center gap-4 p-5 bg-gray-50/50 border border-gray-100 rounded-[24px] group-hover:bg-white group-hover:border-orange-200 transition-all group-hover:shadow-md group-hover:shadow-orange-900/5">
                        <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-orange-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2"/></svg>
                        </div>
                        <p class="text-[15px] font-bold text-gray-700">{{ $admin->created_at->format('d F Y') }}</p>
                    </div>
                </div>

                <div class="group">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Status Otoritas</label>
                    <div class="flex items-center gap-4 p-5 bg-gray-50/50 border border-gray-100 rounded-[24px] group-hover:bg-white group-hover:border-blue-200 transition-all group-hover:shadow-md group-hover:shadow-blue-900/5">
                        <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-width="2"/></svg>
                        </div>
                        <p class="text-[15px] font-bold text-gray-700 tracking-tight">System Administrator</p>
                    </div>
                </div>

                <div class="group">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">ID Kepegawaian</label>
                    <div class="flex items-center gap-4 p-5 bg-gray-50/50 border border-gray-100 rounded-[24px] group-hover:bg-white group-hover:border-gray-300 transition-all group-hover:shadow-md group-hover:shadow-gray-900/5">
                        <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" stroke-width="2.5"/></svg>
                        </div>
                        <p class="text-[15px] font-mono font-black text-gray-700 uppercase tracking-tighter">ADM-{{ str_pad($admin->id_user, 4, '0', STR_PAD_LEFT) }}</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fadeIn { animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
@endsection