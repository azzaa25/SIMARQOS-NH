@extends('admin.layout.app')

@section('content')
{{-- STYLING KUSTOM PAGINATION --}}
<style>
    /* Sembunyikan elemen bawaan Laravel yang tidak diperlukan */
    .custom-pagination nav p {
        display: none !important;
    }

    .custom-pagination nav > div:first-child {
        display: none !important;
    }

    .custom-pagination nav div:last-child {
        flex-direction: row !important;
        gap: 0.25rem;
    }

    /* Styling Utama Tombol & Angka */
    .custom-pagination a, 
    .custom-pagination span[aria-current="page"] span,
    .custom-pagination span[aria-disabled="true"] span {
        display: flex !important;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 12px !important;
        font-size: 11px !important;
        font-weight: 800 !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #e2e8f0 !important;
        background: white !important;
        color: #64748b !important;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        text-decoration: none !important;
    }

    /* Status Halaman Aktif */
    .custom-pagination span[aria-current="page"] span {
        background: #064e3b !important; /* Hijau Tua Khas Masjid */
        color: white !important;
        border-color: #064e3b !important;
        box-shadow: 0 4px 12px rgba(6, 78, 59, 0.2);
    }

    /* Efek Hover untuk Link */
    .custom-pagination a:hover {
        border-color: #064e3b !important;
        color: #064e3b !important;
        background: #f0fdf4 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Styling Ikon Panah */
    .custom-pagination svg {
        width: 16px !important;
        height: 16px !important;
        stroke-width: 3;
    }
    .row-ketua { background-color: #fffbeb !important; }
    .row-ketua:hover { background-color: #fef3c7 !important; }

    /* Styling Khusus Nonaktif agar terlihat seperti arsip */
    .opacity-nonaktif {
        opacity: 0.65;
        filter: grayscale(0.4);
        transition: all 0.3s;
    }
    .opacity-nonaktif:hover {
        opacity: 1;
        filter: grayscale(0);
    }
</style>

{{-- HEADER SECTION --}}
<div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-green-900 leading-tight">Manajemen Peserta Arisan</h1>
        <p class="text-sm text-gray-400 italic">Daftar Peserta Arisan Qurban Masjid Nurul Huda</p>
    </div>
    <a href="{{ route('admin.peserta.create') }}" class="bg-[#147a54] hover:bg-green-800 text-white px-6 py-3 rounded-2xl text-sm font-bold shadow-lg flex items-center gap-2 transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 4v16m8-8H4"></path></svg>
        Tambah Peserta
    </a>
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

{{-- DATA SECTION --}}
<div class="space-y-10">
    @php 
        $allPeserta = $pesertas->getCollection();
        // Pisahkan data di level Blade agar bisa ditaruh di bawah
        $pesertaAktif = $allPeserta->filter(fn($p) => $p->user->status == 'aktif')->groupBy('id_kelompok');
        $pesertaNonaktif = $allPeserta->filter(fn($p) => $p->user->status == 'nonaktif')->groupBy('id_kelompok');
    @endphp

    {{-- --- LOOP DATA AKTIF --- --}}
    @foreach($pesertaAktif as $idKelompok => $items)
        <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 overflow-hidden transition-all hover:shadow-md">
            {{-- HEADER GRUP --}}
            <div class="px-8 py-5 {{ $idKelompok ? 'bg-blue-50/50' : 'bg-slate-50/50' }} border-b border-gray-100 flex flex-wrap justify-between items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 {{ $idKelompok ? 'bg-blue-600' : 'bg-slate-400' }} text-white rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-black text-slate-800 uppercase tracking-tight">
                            {{ $idKelompok ? ($items->first()->kelompok->nama_kelompok ?? 'Grup Tanpa Nama') : 'Peserta Perorangan' }}
                        </h2>
                        @if($idKelompok)
                            @php $totalAnggotaReal = \App\Models\PesertaArisan::where('id_kelompok', $idKelompok)->count(); @endphp
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-xs font-bold text-blue-700 bg-blue-100 px-2 py-0.5 rounded-lg">Kuota {{ $totalAnggotaReal }}/7</span>
                            </div>
                        @endif
                    </div>
                </div>

                @if($idKelompok && $totalAnggotaReal < 7)
                    <a href="{{ url('admin/peserta/kelompok/'.$idKelompok.'/tambah-anggota') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 4v16m8-8H4"></path></svg>
                        Tambah Anggota
                    </a>
                @endif
            </div>

            {{-- TABLE DATA --}}
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-white text-[10px] uppercase tracking-[0.15em] text-slate-400 font-black border-b border-gray-100">
                            <th class="px-8 py-4 text-left"> Nama Peserta Arisan</th>
                            <th class="px-6 py-4 text-left">Kontak & Alamat</th>
                            <th class="px-6 py-4 text-left">Posisi</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-8 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($items as $p)
                        @php $isKetua = $idKelompok && ($p->id_pesertaarisan == ($p->kelompok->id_ketua_peserta ?? 0)); @endphp
                        <tr class="transition-colors {{ $isKetua ? 'row-ketua' : 'hover:bg-slate-50' }}">
                            <td class="px-8 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 {{ $isKetua ? 'bg-amber-500' : 'bg-green-800' }} text-white rounded-xl flex items-center justify-center font-black shadow-sm">
                                        {{ substr($p->nama, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 text-sm">{{ $p->nama }}</p>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-0.5">ID: #{{ $p->id_pesertaarisan }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-slate-600">
                                <div class="flex items-center gap-1.5 mb-1 font-bold text-slate-800">
                                    <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path></svg>
                                    {{ $p->no_hp }}
                                </div>
                                <span class="text-slate-400 block truncate max-w-[180px]">{{ $p->alamat }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($isKetua)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-700 border border-amber-200 uppercase tracking-tighter">Ketua</span>
                                @else
                                    <span class="text-[10px] font-bold text-slate-400 uppercase">{{ $idKelompok ? 'Anggota' : 'Perorangan' }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 text-[10px] font-black rounded-lg bg-green-100 text-green-700 uppercase">aktif</span>
                            </td>
                            <td class="px-8 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('admin.peserta.show', $p->id_pesertaarisan) }}" class="p-2 bg-white border border-gray-200 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a>
                                    <a href="{{ route('admin.peserta.edit', $p->id_pesertaarisan) }}" class="p-2 bg-white border border-gray-200 text-orange-500 rounded-lg hover:bg-orange-500 hover:text-white transition-all"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                                    <form id="delete-form-{{ $p->id_pesertaarisan }}" action="{{ route('admin.peserta.destroy', $p->id_pesertaarisan) }}" method="POST" style="display: none;">@csrf @method('DELETE')</form>
                                    <button type="button" onclick="confirmDelete('{{ $p->id_pesertaarisan }}', '{{ $p->nama }}')" class="p-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    {{-- --- PEMISAH NONAKTIF (Tampilkan jika ada) --- --}}
    @if($pesertaNonaktif->count() > 0)
        <div class="relative py-4">
            <div class="absolute inset-0 flex items-center" aria-hidden="true"><div class="w-full border-t border-dashed border-gray-300"></div></div>
            <div class="relative flex justify-center"><span class="bg-gray-100 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest rounded-full py-1">Arsip / Peserta Nonaktif</span></div>
        </div>
    @endif

    {{-- --- LOOP DATA NONAKTIF --- --}}
    @foreach($pesertaNonaktif as $idKelompok => $items)
        <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 overflow-hidden opacity-nonaktif">
            {{-- HEADER GRUP --}}
            <div class="px-8 py-5 {{ $idKelompok ? 'bg-blue-50/50' : 'bg-slate-50/50' }} border-b border-gray-100 flex flex-wrap justify-between items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-slate-300 text-white rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-black text-slate-500 uppercase tracking-tight">
                            {{ $idKelompok ? ($items->first()->kelompok->nama_kelompok ?? 'Grup Tanpa Nama') : 'Peserta Perorangan' }}
                        </h2>
                    </div>
                </div>
            </div>

            {{-- TABLE DATA NONAKTIF --}}
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-white text-[10px] uppercase tracking-[0.15em] text-slate-400 font-black border-b border-gray-100">
                            <th class="px-8 py-4 text-left"> Nama Peserta Arisan</th>
                            <th class="px-6 py-4 text-left">Kontak & Alamat</th>
                            <th class="px-6 py-4 text-left">Posisi</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-8 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($items as $p)
                        <tr class="hover:bg-slate-50">
                            <td class="px-8 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-slate-400 text-white rounded-xl flex items-center justify-center font-black">
                                        {{ substr($p->nama, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-500 text-sm">{{ $p->nama }}</p>
                                        <p class="text-[10px] font-black text-slate-400 uppercase">ID: #{{ $p->id_pesertaarisan }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-400">
                                <div class="mb-1">{{ $p->no_hp }}</div>
                                <span class="truncate max-w-[180px] block">{{ $p->alamat }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[10px] font-bold text-slate-400 uppercase">{{ $idKelompok ? 'Anggota' : 'Perorangan' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 text-[10px] font-black rounded-lg bg-red-100 text-red-700 uppercase">nonaktif</span>
                            </td>
                            <td class="px-8 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('admin.peserta.edit', $p->id_pesertaarisan) }}" class="p-2 bg-white border border-gray-200 text-orange-500 rounded-lg hover:bg-orange-500 hover:text-white transition-all"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                                    <form id="delete-form-{{ $p->id_pesertaarisan }}" action="{{ route('admin.peserta.destroy', $p->id_pesertaarisan) }}" method="POST" style="display: none;">@csrf @method('DELETE')</form>
                                    <button type="button" onclick="confirmDelete('{{ $p->id_pesertaarisan }}', '{{ $p->nama }}')" class="p-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    @if($allPeserta->isEmpty())
        <div class="bg-white rounded-[32px] p-20 text-center border border-dashed border-gray-300">
            <p class="text-gray-400 font-black uppercase tracking-widest text-xs">Peserta Arisan Qurban tidak ditemukan</p>
        </div>
    @endif

    {{-- FOOTER PAGINATION --}}
    <div class="px-8 py-6 bg-white border border-gray-100 rounded-[24px] flex flex-col md:flex-row items-center justify-between gap-4 shadow-sm">
        <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
            Peserta {{ $pesertas->firstItem() ?? 0 }} - {{ $pesertas->lastItem() ?? 0 }} dari {{ $pesertas->total() }}
        </div>
        <div class="custom-pagination">
            {{ $pesertas->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<script>
    let timeout = null;
    function debounceSubmit() {
        clearTimeout(timeout);
        timeout = setTimeout(() => { document.getElementById('filterForm').submit(); }, 700);
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