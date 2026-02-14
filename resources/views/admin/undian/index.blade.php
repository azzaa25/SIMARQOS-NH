@extends('admin.layout.app')

@section('content')
<div class="max-w-7xl mx-auto animate-fadeInUp h-[calc(100vh-140px)] flex flex-col">
    {{-- Header & Tombol Trigger Modal --}}
    <div class="flex justify-between items-end mb-8 shrink-0">
        <div>
            <h1 class="text-3xl font-black text-green-900 tracking-tight uppercase">Undian Arisan</h1>
            <p class="text-sm text-gray-400 font-medium">Manajemen otomatis urutan pemenang qurban</p>
        </div>
        <button onclick="document.getElementById('modalUndian').classList.remove('hidden')" 
            class="px-6 py-3 bg-[#b38b2d] hover:bg-[#967526] text-white rounded-2xl shadow-lg shadow-yellow-900/20 transition-all flex items-center gap-3 active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <span class="text-xs font-black uppercase tracking-widest">Lakukan Undian</span>
        </button>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-3 gap-6 mb-10 shrink-0">
        <div class="bg-white rounded-[32px] p-6 shadow-sm border border-gray-50 flex items-center gap-4">
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center shadow-inner">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Total Peserta</p>
                <p class="text-xl font-black text-gray-800">{{ $totalPeserta }}</p>
            </div>
        </div>
        </div>

    {{-- Table Section --}}
    <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 flex-1 overflow-hidden flex flex-col">
        <div class="overflow-y-auto custom-scrollbar flex-1">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50 sticky top-0 z-10">
                    <tr>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tahun Ke-</th>
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Skema</th>
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama Pemenang</th>
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal Undi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($undians as $item)
                    <tr class="hover:bg-gray-50/50 transition-all">
                        <td class="px-8 py-4 text-xs font-black text-green-700">Tahun Ke-{{ $item->tahun_ke }}</td>
                        <td class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">{{ $item->skema->nama_skema }}</td>
                        <td class="px-6 py-4 text-sm font-black text-gray-800">{{ $item->peserta->nama }}</td>
                        <td class="px-6 py-4 text-xs font-medium text-gray-400">{{ $item->tanggal_undian }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL PILIH SKEMA --}}
<div id="modalUndian" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/50 backdrop-blur-sm animate-fadeIn">
    <div class="bg-white rounded-[40px] p-10 w-full max-w-md shadow-2xl">
        <h3 class="text-2xl font-black text-gray-800 mb-2 uppercase tracking-tight">Pilih Skema</h3>
        <p class="text-xs text-gray-400 mb-8 font-medium italic">Pilih jenis qurban yang akan diundi hari ini.</p>
        
        <form action="{{ route('admin.undian.proses') }}" method="POST">
            @csrf
            <select name="id_skema" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl mb-6 text-sm font-bold text-gray-700 outline-none focus:ring-4 focus:ring-green-500/10">
                <option value="" disabled selected>-- Pilih Skema Qurban --</option>
                @foreach($skemas as $s)
                    <option value="{{ $s->id_skema }}">{{ $s->nama_skema }} ({{ $s->durasi_tahun }} Thn)</option>
                @endforeach
            </select>
            
            <div class="flex flex-col gap-3">
                <button type="submit" class="w-full bg-[#147a54] text-white py-4 rounded-full font-black text-[11px] uppercase tracking-[0.2em] shadow-lg shadow-green-900/20">Mulai Pengacakan</button>
                <button type="button" onclick="document.getElementById('modalUndian').classList.add('hidden')" class="w-full py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Batal</button>
            </div>
        </form>
    </div>
</div>
@endsection