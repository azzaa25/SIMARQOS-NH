@extends('admin.layout.app')

@section('content')
{{-- HEADER SECTION --}}
<div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-green-900 leading-tight">Kelola Peserta Arisan</h1>
        <p class="text-sm text-gray-500 italic">Sistem Manajemen Masjid Nurul Huda</p>
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
                {{-- Input Pencarian --}}
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

                {{-- Filter Skema --}}
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

{{-- DATA TABLE SECTION --}}
<div class="bg-white rounded-[32px] shadow-xl shadow-gray-200/40 border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full min-w-[1000px] text-left">
            <thead class="bg-gray-50/50 text-[10px] uppercase tracking-widest text-gray-400 font-black border-b border-gray-100">
                <tr>
                    <th class="px-8 py-6">Identitas Peserta</th>
                    <th class="px-6 py-6">Alamat Domisili</th>
                    <th class="px-6 py-6 text-center">Kontak</th>
                    <th class="px-6 py-6">Skema Dipilih</th>
                    <th class="px-6 py-6 text-center">Status</th>
                    <th class="px-8 py-6 text-center">Tindakan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm">
                @forelse($pesertas as $p)
                <tr class="hover:bg-green-50/30 transition-colors group">
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 bg-green-800 text-white rounded-2xl flex items-center justify-center text-xs font-bold shadow-lg shadow-green-900/20 uppercase transform group-hover:scale-110 transition-transform">
                                {{ substr($p->nama, 0, 1) }}{{ strpos($p->nama, ' ') !== false ? substr(strrchr($p->nama, ' '), 1, 1) : '' }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-800 leading-none mb-1.5">{{ $p->nama }}</p>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">ID #{{ str_pad($p->id_pesertaarisan, 4, '0', STR_PAD_LEFT) }}</p>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        <p class="text-xs text-gray-500 max-w-[180px] leading-relaxed line-clamp-2 italic">"{{ $p->alamat }}"</p>
                    </td>
                    <td class="px-6 py-5 text-center">
                        <span class="inline-flex items-center gap-1.5 bg-gray-100 px-3 py-1.5 rounded-xl text-[11px] font-bold text-gray-600">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            {{ $p->no_hp }}
                        </span>
                    </td>
                    <td class="px-6 py-5">
                        <p class="font-black text-green-700 text-xs">{{ $p->skemaArisan->nama_skema ?? '-' }}</p>
                        <p class="text-[9px] text-gray-400 uppercase tracking-widest font-bold">Paket Arisan</p>
                    </td>
                    <td class="px-6 py-5 text-center uppercase">
                        @if(strtolower($p->status) == 'aktif')
                            <span class="px-4 py-1.5 text-[9px] font-black rounded-full tracking-widest border-2 bg-blue-50 text-blue-600 border-blue-100">
                                AKTIF
                            </span>
                        @else
                            <span class="px-4 py-1.5 text-[9px] font-black rounded-full tracking-widest border-2 bg-red-50 text-red-600 border-red-100">
                                NONAKTIF
                            </span>
                        @endif
                    </td>
                    <td class="px-8 py-5 text-center">
                        <div class="flex justify-center items-center gap-2">
                            <a href="{{ route('admin.peserta.show', $p->id_pesertaarisan) }}" 
                               class="p-2.5 bg-blue-50 text-blue-500 rounded-xl hover:bg-blue-500 hover:text-white transition-all shadow-sm active:scale-90" title="Lihat Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="{{ route('admin.peserta.edit', $p->id_pesertaarisan) }}" 
                               class="p-2.5 bg-orange-50 text-orange-500 rounded-xl hover:bg-orange-500 hover:text-white transition-all shadow-sm active:scale-90" title="Edit Peserta">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form id="delete-form-{{ $p->id_pesertaarisan }}" action="{{ route('admin.peserta.destroy', $p->id_pesertaarisan) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete('{{ $p->id_pesertaarisan }}', '{{ $p->nama }}')"
                                        class="p-2.5 bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm active:scale-90" title="Hapus Peserta">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M10 4v3m4-3v3m-5-3h4"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-20 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            <p class="text-gray-400 font-bold tracking-widest uppercase text-xs">Data Peserta Kosong</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
        <p class="text-[11px] text-gray-400 font-bold uppercase tracking-widest">
            Menampilkan <span class="text-green-700">{{ $pesertas->firstItem() ?? 0 }}</span> - <span class="text-green-700">{{ $pesertas->lastItem() ?? 0 }}</span> dari <span class="text-green-700">{{ $pesertas->total() }}</span> entri
        </p>
        <div class="flex items-center">
            {{ $pesertas->withQueryString()->links() }}
        </div>
    </div>
</div>

{{-- SCRIPTS --}}
<script>
    // Debounce Submit untuk Pencarian
    let timeout = null;
    function debounceSubmit() {
        clearTimeout(timeout);
        timeout = setTimeout(function () {
            document.getElementById('filterForm').submit();
        }, 700);
    }

    // SweetAlert Hapus
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
            customClass: {
                popup: 'rounded-[32px] border-none shadow-2xl',
                confirmButton: 'rounded-full px-8 py-3 text-sm font-bold shadow-lg shadow-green-900/20 ml-2',
                cancelButton: 'rounded-full px-8 py-3 text-sm font-bold text-gray-500 hover:bg-gray-200'
            },
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