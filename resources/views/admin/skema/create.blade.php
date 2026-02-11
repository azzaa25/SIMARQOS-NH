@extends('admin.layout.app')

@section('content')
<div class="max-w-4xl mx-auto h-full flex flex-col justify-center">
    <div class="flex justify-between items-end mb-4">
        <div>
            <nav class="flex text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">
                <a href="{{ route('admin.skema.index') }}" class="hover:text-green-700">Skema</a>
                <span class="mx-2">/</span>
                <span class="text-green-800">Tambah Baru</span>
            </nav>
            <h1 class="text-xl font-bold text-green-900 leading-none">Buat Skema Baru</h1>
        </div>
        <p class="text-[11px] text-gray-400 italic">Masjid Nurul Huda</p>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <form id="tambah-skema-form" action="{{ route('admin.skema.store') }}" method="POST" class="p-6 md:p-8 space-y-5">
            @csrf

            {{-- NAMA & IURAN --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2 space-y-1">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase ml-1">
                        Nama Paket Arisan
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </span>
                        <input id="nama_skema" name="nama_skema" type="text" required
                               placeholder="Contoh: Arisan Qurban Sapi"
                               class="w-full pl-9 pr-4 py-2 text-sm rounded-lg border border-gray-200
                                      focus:ring-2 focus:ring-green-500/10 focus:border-green-600 outline-none transition-all">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase ml-1">
                        Iuran per Bulan (Rp)
                    </label>
                    <input name="nominal_iuran" type="number" required
                           class="w-full px-4 py-2 text-sm rounded-lg border border-gray-200
                                  focus:ring-2 focus:ring-green-500/10 focus:border-green-600 outline-none
                                  font-semibold text-green-700 transition-all">
                </div>
            </div>

            {{-- DURASI & TIPE --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-gray-50">
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase ml-1">
                        Durasi Arisan
                    </label>
                    <select name="durasi_bulan" required
                            class="w-full px-4 py-2 text-sm rounded-lg border border-gray-200
                                   focus:ring-2 focus:ring-green-500/10 focus:border-green-600 outline-none">
                        <option value="">Pilih Durasi</option>
                        <option value="12">1 Tahun (12 Bulan)</option>
                        <option value="36">3 Tahun (36 Bulan)</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase ml-1">
                        Tipe Skema
                    </label>
                    <select name="tipe_skema" required
                            class="w-full px-4 py-2 text-sm rounded-lg border border-gray-200
                                   focus:ring-2 focus:ring-green-500/10 focus:border-green-600 outline-none">
                        <option value="">Pilih Tipe</option>
                        <option value="perorangan">Perorangan</option>
                        <option value="kelompok">Kelompok</option>
                    </select>
                </div>
            </div>

            {{-- DESKRIPSI --}}
            <div class="space-y-1 pt-2 border-t border-gray-50">
                <label class="block text-[10px] font-bold text-gray-500 uppercase ml-1">
                    Keterangan Tambahan
                </label>
                <textarea name="deskripsi" rows="2"
                          placeholder="Catatan singkat mengenai skema..."
                          class="w-full px-4 py-2 text-sm rounded-lg border border-gray-200
                                 focus:ring-2 focus:ring-green-500/10 focus:border-green-600 outline-none resize-none transition-all"></textarea>
            </div>

            {{-- AKSI --}}
            <div class="pt-4 flex gap-3">
                <a href="{{ route('admin.skema.index') }}"
                   class="px-6 py-2.5 bg-gray-50 text-gray-500 text-xs font-bold rounded-lg
                          hover:bg-gray-100 border flex items-center justify-center transition-all">
                    Batal
                </a>
                <button type="button" onclick="confirmSave()"
                        class="flex-1 bg-[#147a54] hover:bg-green-800 text-white
                               text-xs font-bold py-2.5 rounded-lg shadow-md active:scale-[0.98] transition-all">
                    Simpan Skema
                </button>
            </div>
        </form>
    </div>
</div>

{{-- SCRIPT ALERT --}}
<script>
    function confirmSave() {
        const nama = document.getElementById('nama_skema').value;
        
        if (!nama) {
            Swal.fire({
                icon: 'error',
                title: 'Opps...',
                text: 'Nama paket arisan tidak boleh kosong!',
                confirmButtonColor: '#147a54',
                customClass: { popup: 'rounded-[32px]' }
            });
            return;
        }

        Swal.fire({
            title: 'Simpan Skema?',
            text: `Apakah Anda yakin ingin menambahkan skema "${nama}"?`,
            icon: 'question',
            iconColor: '#147a54',
            showCancelButton: true,
            confirmButtonColor: '#064e3b',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Cek Kembali',
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
                    title: 'Memproses...',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                document.getElementById('tambah-skema-form').submit();
            }
        });
    }
</script>
@endsection