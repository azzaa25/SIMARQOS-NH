@extends('admin.layout.app')

@section('content')
{{-- HEADER SECTION --}}
<div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-green-900 leading-tight">Manajemen Peserta Arisan</h1>
        <p class="text-sm text-gray-400 italic">Sistem Manajemen Masjid Nurul Huda</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.peserta.create') }}" class="bg-[#147a54] hover:bg-green-800 text-white px-6 py-3 rounded-2xl text-sm font-bold shadow-lg shadow-green-900/20 flex items-center gap-2 transition-all active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 4v16m8-8H4"></path></svg>
            Tambah Peserta Baru
        </a>
    </div>
</div>

{{-- CARD FILTER & SEARCH --}}
<div class="bg-white p-6 rounded-[28px] shadow-sm border border-gray-100 mb-8">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="flex-1">
            <h3 class="text-sm font-black text-green-900 uppercase tracking-widest mb-1">Pencarian Peserta</h3>
            <p class="text-[11px] text-gray-400 mb-4">Total {{ $pesertas->total() }} peserta terdaftar</p>
            
            <form id="filterForm" method="GET" action="{{ route('admin.peserta.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-8 relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari nama, alamat, atau nomor HP peserta..." 
                           class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 focus:bg-white transition-all outline-none text-sm font-medium" 
                           oninput="debounceSubmit()">
                </div>

                <div class="md:col-span-4 relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 pointer-events-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4.5h18m-18 5h18m-18 5h18m-18 5h18"></path>
                        </svg>
                    </span>
                    <select name="skema" 
                            class="w-full pl-12 pr-10 py-3.5 bg-gray-50 border border-gray-100 text-gray-600 text-sm rounded-2xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 focus:bg-white transition-all outline-none appearance-none font-medium cursor-pointer" 
                            onchange="document.getElementById('filterForm').submit()">
                        <option value="">Semua Skema Arisan</option>
                        @foreach($skemas as $skema)
                            <option value="{{ $skema->id_skema }}" {{ request('skema') == $skema->id_skema ? 'selected' : '' }}>
                                {{ $skema->nama_skema }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- DATA SECTION: GROUPED BY KELOMPOK --}}
<div class="space-y-8">
    @php
        // Mengelompokkan koleksi paginasi berdasarkan id_kelompok
        $groupedPeserta = $pesertas->getCollection()->groupBy('id_kelompok');
    @endphp

    @forelse($groupedPeserta as $idKelompok => $items)
        <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 overflow-hidden">
            {{-- HEADER KELOMPOK --}}
            <div class="px-8 py-5 bg-gray-50/50 border-b border-gray-100 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 {{ $idKelompok ? 'bg-blue-600' : 'bg-slate-400' }} text-white rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-800 uppercase tracking-tighter">
                            {{ $idKelompok ? ($items->first()->kelompok->nama_kelompok ?? 'Unknown') : 'Peserta Perorangan' }}
                        </h2>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Total Anggota: {{ $items->count() }}</p>
                    </div>
                </div>
            </div>

            {{-- TABLE DALAM GRUP --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-white text-[9px] uppercase tracking-[0.2em] text-gray-400 font-black border-b border-gray-50">
                        <tr>
                            <th class="px-8 py-4">Identitas</th>
                            <th class="px-6 py-4">Alamat & Kontak</th>
                            <th class="px-6 py-4">Peran</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-8 py-4 text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($items as $p)
                        <tr class="hover:bg-green-50/20 transition-colors group">
                            <td class="px-8 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-green-800 text-white rounded-xl flex items-center justify-center text-[10px] font-black uppercase shadow-md">
                                        {{ substr($p->nama, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800 text-xs">{{ $p->nama }}</p>
                                        <p class="text-[9px] text-gray-400 font-bold tracking-tighter">ID #{{ str_pad($p->id_pesertaarisan, 4, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-[10px] font-bold text-gray-600 mb-0.5">{{ $p->no_hp }}</p>
                                <p class="text-[9px] text-gray-400 italic line-clamp-1">"{{ $p->alamat }}"</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($idKelompok)
                                    @if($p->id_pesertaarisan == ($p->kelompok->id_ketua_peserta ?? 0))
                                        <span class="text-[10px] font-black text-green-600 uppercase tracking-tighter">★ Ketua Kelompok</span>
                                    @else
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Anggota</span>
                                    @endif
                                @else
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Individu</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 text-[9px] font-black rounded-full border-2 {{ $p->user->status == 'aktif' ? 'bg-blue-50 text-blue-600 border-blue-100' : 'bg-red-50 text-red-600 border-red-100' }}">
                                    {{ strtoupper($p->user->status) }}
                                </span>
                            </td>
                            <td class="px-8 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('admin.peserta.show', $p->id_pesertaarisan) }}" class="p-2 bg-blue-50 text-blue-500 rounded-lg hover:bg-blue-500 hover:text-white transition-all active:scale-90 shadow-sm" title="Detail">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.peserta.edit', $p->id_pesertaarisan) }}" class="p-2 bg-orange-50 text-orange-500 rounded-lg hover:bg-orange-500 hover:text-white transition-all active:scale-90 shadow-sm" title="Edit">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form id="delete-form-{{ $p->id_pesertaarisan }}" action="{{ route('admin.peserta.destroy', $p->id_pesertaarisan) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmDelete('{{ $p->id_pesertaarisan }}', '{{ $p->nama }}')" class="p-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all active:scale-90 shadow-sm" title="Hapus">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-[32px] p-20 text-center border border-dashed">
            <div class="flex flex-col items-center justify-center">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Data Peserta Tidak Ditemukan</p>
            </div>
        </div>
    @endforelse

    {{-- PAGINATION --}}
    <div class="mt-8 flex flex-col sm:flex-row justify-between items-center px-4 gap-4">
        <p class="text-[11px] text-gray-400 font-bold uppercase tracking-widest">
            Menampilkan <span class="text-green-700">{{ $pesertas->firstItem() ?? 0 }}</span> - <span class="text-green-700">{{ $pesertas->lastItem() ?? 0 }}</span> dari <span class="text-green-700">{{ $pesertas->total() }}</span> entri
        </p>
        <div>
            {{ $pesertas->withQueryString()->links() }}
        </div>
    </div>
</div>

{{-- SCRIPTS --}}
<script>
    let timeout = null;
    function debounceSubmit() {
        clearTimeout(timeout);
        timeout = setTimeout(function () {
            document.getElementById('filterForm').submit();
        }, 700);
    }

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Peserta?',
            html: `Anda akan menghapus data <b class="text-green-800">${name}</b>.<br><span class="text-xs text-gray-500">Data yang dihapus tidak dapat dipulihkan!</span>`,
            icon: 'warning',
            iconColor: '#d33',
            showCancelButton: true,
            confirmButtonColor: '#064e3b',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'Ya, Hapus Data',
            cancelButtonText: 'Batalkan',
            reverseButtons: true,
            customClass: { popup: 'rounded-[32px] border-none shadow-2xl', confirmButton: 'rounded-full px-8 py-3 text-sm font-bold shadow-lg shadow-green-900/20 ml-2', cancelButton: 'rounded-full px-8 py-3 text-sm font-bold text-gray-500 hover:bg-gray-200' },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus...',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endsection