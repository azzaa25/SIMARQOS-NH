@extends('admin.layout.app')

@section('content')
<div class="max-w-5xl mx-auto h-full flex flex-col justify-center animate-fadeIn">
    {{-- BREADCRUMB & HEADER RINGKAS --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-4 gap-2">
        <div>
            <nav class="flex text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em] mb-1">
                <a href="{{ route('admin.peserta.index') }}" class="hover:text-green-700 transition-colors">Manajemen Peserta</a>
                <span class="mx-2 text-gray-300">/</span>
                <span class="text-green-800">Registrasi Baru</span>
            </nav>
            <h1 class="text-2xl font-extrabold text-green-900 leading-tight tracking-tight">Pendaftaran Peserta</h1>
        </div>
        <div class="hidden md:block text-right">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest leading-none mb-1">Masjid Nurul Huda</p>
            <p class="text-[11px] text-gray-300 italic leading-none">Sistem Manajemen Arisan Qurban</p>
        </div>
    </div>

    <div class="bg-white rounded-[32px] shadow-[0_20px_50px_rgba(0,0,0,0.05)] border border-gray-50 overflow-hidden">
        {{-- ID FORM DITAMBAHKAN UNTUK SCRIPT --}}
        <form id="createPesertaForm" action="{{ route('admin.peserta.store') }}" method="POST" class="p-8 md:p-10 space-y-6">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                
                {{-- KIRI: BIODATA & KEAMANAN --}}
                <div class="lg:col-span-7 space-y-6">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-9 h-9 bg-green-50 text-green-700 rounded-2xl flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-gray-800 uppercase tracking-wider leading-none">Biodata & Keamanan</h3>
                            <p class="text-[10px] text-gray-400 mt-1">Lengkapi informasi dasar dan akun sistem</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Lengkap Sesuai KTP</label>
                            <input type="text" name="nama" id="input_nama" value="{{ old('nama') }}" required placeholder="Masukkan nama lengkap..."
                                   class="w-full px-5 py-3 bg-gray-50/50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 focus:bg-white transition-all outline-none text-sm font-semibold text-gray-700 placeholder:text-gray-300">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Email Peserta</label>
                                <input type="email" name="email" value="{{ old('email') }}" required placeholder="email@contoh.com"
                                       class="w-full px-5 py-3 bg-gray-50/50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 focus:bg-white transition-all outline-none text-sm font-semibold text-gray-700 placeholder:text-gray-300">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Password Default</label>
                                <input type="password" name="password" required placeholder="Min. 8 Karakter"
                                       class="w-full px-5 py-3 bg-gray-50/50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 focus:bg-white transition-all outline-none text-sm font-semibold text-gray-700 placeholder:text-gray-300">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Nomor WhatsApp Aktif</label>
                            <input type="text" name="no_hp" value="{{ old('no_hp') }}" required placeholder="Contoh: 081234567890"
                                   class="w-full px-5 py-3 bg-gray-50/50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 focus:bg-white transition-all outline-none text-sm font-semibold text-gray-700 placeholder:text-gray-300">
                        </div>
                    </div>
                </div>

                {{-- KANAN: SKEMA & DOMISILI --}}
                <div class="lg:col-span-5 flex flex-col space-y-6">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-9 h-9 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-gray-800 uppercase tracking-wider leading-none">Skema & Domisili</h3>
                            <p class="text-[10px] text-gray-400 mt-1">Pengaturan paket dan alamat</p>
                        </div>
                    </div>

                    <div class="space-y-4 flex-1">
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Alamat Domisili</label>
                            <textarea name="alamat" rows="2" required placeholder="Masukkan alamat rumah lengkap..."
                                      class="w-full px-5 py-3 bg-gray-50/50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 focus:bg-white transition-all outline-none text-sm font-semibold text-gray-700 resize-none placeholder:text-gray-300 leading-relaxed">{{ old('alamat') }}</textarea>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Pilih Paket Arisan</label>
                            <select name="id_skema" required
                                    class="w-full px-5 py-3 bg-gray-50/50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 outline-none text-sm cursor-pointer font-bold text-green-800 transition-all appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2024%2024%22%20stroke%3D%22currentColor%22%3E%3Cpath%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%222%22%20d%3D%22M19%209l-7%207-7-7%22%20%2F%3E%3C%2Fsvg%3E')] bg-[length:1.2rem_1.2rem] bg-[right_1.2rem_center] bg-no-repeat">
                                <option value="">-- Cari Skema Aktif --</option>
                                @foreach($skemas as $skema)
                                    <option value="{{ $skema->id_skema }}" {{ old('id_skema') == $skema->id_skema ? 'selected' : '' }}>{{ $skema->nama_skema }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1 text-center">Status Aktivasi</label>
                            <div class="grid grid-cols-2 gap-3 p-1.5 bg-gray-50/50 border border-gray-100 rounded-[20px]">
                                <label class="relative flex items-center justify-center py-2.5 px-4 rounded-[14px] cursor-pointer group transition-all duration-300 has-[:checked]:bg-white has-[:checked]:shadow-md has-[:checked]:border-orange-100 border border-transparent">
                                    <input type="radio" name="status" value="pending" checked class="sr-only">
                                    <span class="text-[10px] font-black text-gray-400 group-hover:text-orange-500 group-[.has-[:checked]]:text-orange-600 tracking-widest uppercase transition-colors">Pending</span>
                                </label>
                                <label class="relative flex items-center justify-center py-2.5 px-4 rounded-[14px] cursor-pointer group transition-all duration-300 has-[:checked]:bg-white has-[:checked]:shadow-md has-[:checked]:border-green-100 border border-transparent">
                                    <input type="radio" name="status" value="aktif" class="sr-only">
                                    <span class="text-[10px] font-black text-gray-400 group-hover:text-green-600 group-[.has-[:checked]]:text-green-700 tracking-widest uppercase transition-colors">Aktif</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- BUTTONS ACTION --}}
                    <div class="pt-8 flex gap-3">
                        <a href="{{ route('admin.peserta.index') }}" 
                           class="flex-1 px-4 py-3.5 bg-white text-gray-400 text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl text-center hover:bg-gray-50 border border-gray-100 transition-all active:scale-95 shadow-sm">
                            Batal
                        </a>
                        {{-- TYPE DIGANTI KE BUTTON UNTUK TRIGGER KONFIRMASI --}}
                        <button type="button" onclick="confirmStore()"
                                class="flex-[2.5] bg-[#147a54] hover:bg-green-800 text-white text-[11px] font-black uppercase tracking-[0.2em] py-3.5 rounded-2xl transition-all shadow-xl shadow-green-900/10 active:scale-[0.98] transform hover:-translate-y-0.5">
                            Daftarkan Peserta
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- SCRIPT KONFIRMASI --}}
<script>
    function confirmStore() {
        const nama = document.getElementById('input_nama').value;
        
        // Cek dasar validasi sisi klien (opsional, tapi bagus untuk UX)
        if (!nama) {
            Swal.fire({
                icon: 'error',
                title: 'Data Belum Lengkap',
                text: 'Nama lengkap harus diisi sebelum mendaftarkan peserta.',
                confirmButtonColor: '#147a54',
                customClass: { popup: 'rounded-[32px]' }
            });
            return;
        }

        Swal.fire({
            title: 'Daftarkan Peserta?',
            text: `Anda akan mendaftarkan "${nama}" ke dalam sistem arisan qurban.`,
            icon: 'question',
            iconColor: '#147a54',
            showCancelButton: true,
            confirmButtonColor: '#064e3b',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'Ya, Daftarkan!',
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
                // Tampilkan loading saat proses simpan
                Swal.fire({
                    title: 'Sedang Memproses...',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                // Submit form secara manual
                document.getElementById('createPesertaForm').submit();
            }
        });
    }
</script>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn { animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
@endsection