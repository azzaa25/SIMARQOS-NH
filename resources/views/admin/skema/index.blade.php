@extends('admin.layout.app')

@section('content')

{{-- FLASH MESSAGE SUKSES --}}
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        iconColor: '#147a54',
        customClass: {
            popup: 'rounded-[24px]',
        }
    });
</script>
@endif

{{-- HEADER HALAMAN --}}
<div class="mb-8">
    <h1 class="text-2xl font-bold text-green-900 leading-tight">Kelola Skema Arisan</h1>
    <p class="text-sm text-gray-500 italic">Sistem Manajemen Masjid Nurul Huda</p>
</div>

{{-- STATISTIK SINGKAT --}}
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 md:gap-6 mb-8">
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 transition-all hover:shadow-md">
        <div class="w-12 h-12 bg-green-50 text-green-700 rounded-xl flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-800">{{ $skemas->count() }}</p>
            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Total Skema</p>
        </div>
    </div>
</div>

{{-- TABEL DATA --}}
<div class="bg-white rounded-[24px] shadow-xl shadow-gray-200/40 border border-gray-100 overflow-hidden">
    <div class="p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-50 bg-gray-50/30">
        <div>
            <h2 class="text-lg font-bold text-green-900 leading-none">Data Skema Arisan</h2>
            <p class="text-xs text-gray-400 mt-1 italic">Daftar paket aktif saat ini</p>
        </div>

        <a href="{{ route('admin.skema.create') }}"
           class="bg-[#147a54] hover:bg-green-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-green-900/20 flex items-center gap-2 transition-all active:scale-95 text-center w-full sm:w-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Skema
        </a>
    </div>

    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full min-w-[900px] text-left">
            <thead class="bg-gray-50 text-[10px] uppercase tracking-widest text-gray-400 font-bold border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">Informasi Skema</th>
                    <th class="px-6 py-4">Nominal Iuran</th>
                    <th class="px-6 py-4">Durasi Paket</th>
                    <th class="px-6 py-4 text-center">Tipe</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-50 text-sm">
                @forelse($skemas as $s)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-green-50 text-green-700 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800 leading-none mb-1">{{ $s->nama_skema }}</p>
                                <p class="text-[10px] text-gray-400 tracking-tight italic">Qurban Masjid</p>
                            </div>
                        </div>
                    </td>

                    <td class="px-6 py-4 font-extrabold text-green-700">
                        Rp {{ number_format($s->nominal_iuran, 0, ',', '.') }}
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-bold text-gray-700">{{ $s->durasi_bulan }} Bulan</span>
                            <span class="text-[10px] text-gray-400 italic">Estimasi {{ $s->durasi_bulan == 12 ? '1 Tahun' : '3 Tahun' }}</span>
                        </div>
                    </td>

                    <td class="px-6 py-4 text-center uppercase tracking-tighter">
                        <span class="px-3 py-1 text-[9px] font-black rounded-lg
                            {{ $s->tipe_skema == 'kelompok' ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-green-50 text-green-600 border border-green-100' }}">
                            {{ $s->tipe_skema }}
                        </span>
                    </td>

                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-[9px] font-black rounded-full uppercase">
                            Aktif
                        </span>
                    </td>

                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center items-center gap-2">
                            <a href="{{ route('admin.skema.edit', $s->id_skema) }}"
                               class="p-2 bg-orange-50 text-orange-500 rounded-lg hover:bg-orange-500 hover:text-white transition-all shadow-sm"
                               title="Edit Skema">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>

                            <form id="delete-form-{{ $s->id_skema }}" action="{{ route('admin.skema.destroy', $s->id_skema) }}" method="POST" class="inline">
                                @csrf 
                                @method('DELETE')
                                <button type="button" 
                                    onclick="confirmDelete('{{ $s->id_skema }}', '{{ $s->nama_skema }}')"
                                    class="p-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all shadow-sm"
                                    title="Hapus Skema">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-300">
                        <p class="font-medium italic">Belum ada data skema arisan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- SCRIPT SWEETALERT2 --}}
<script>
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Skema?',
            html: `Anda akan menghapus paket <b class="text-green-800">${name}</b>.<br><span class="text-xs text-gray-500">Data yang terhapus tidak dapat dipulihkan kembali.</span>`,
            icon: 'warning',
            iconColor: '#147a54',
            showCancelButton: true,
            confirmButtonColor: '#064e3b',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'Ya, Hapus Data',
            cancelButtonText: 'Batalkan',
            reverseButtons: true,
            focusCancel: true,
            customClass: {
                popup: 'rounded-[32px] border-none shadow-2xl',
                title: 'text-2xl font-bold text-gray-800',
                htmlContainer: 'text-sm font-medium text-gray-600',
                confirmButton: 'rounded-full px-8 py-3 text-sm font-bold shadow-lg shadow-green-900/20 ml-2',
                cancelButton: 'rounded-full px-8 py-3 text-sm font-bold text-gray-500 hover:bg-gray-200'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
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