@extends('admin.layout.app')

@section('content')
<div class="max-w-5xl mx-auto h-full flex flex-col justify-center">
    {{-- BREADCRUMB & HEADER RINGKAS --}}
    <div class="flex justify-between items-end mb-3">
        <div>
            <nav class="flex text-[9px] text-gray-400 font-bold uppercase tracking-widest mb-0.5">
                <a href="{{ route('admin.peserta.index') }}" class="hover:text-green-700 transition-colors">Peserta</a>
                <span class="mx-2">/</span>
                <span class="text-green-800">Edit Data</span>
            </nav>
            <h1 class="text-xl font-bold text-green-900 leading-tight">Edit Data Peserta</h1>
        </div>
        <p class="text-[10px] text-gray-400 italic">Peserta: {{ $peserta->nama }}</p>
    </div>

    <div class="bg-white rounded-[28px] shadow-lg border border-gray-100 overflow-hidden">
        <form id="updateForm" action="{{ route('admin.peserta.update', $peserta->id_pesertaarisan) }}" method="POST" class="p-6 md:p-8 space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-10 gap-y-5">
                
                {{-- KIRI: BIODATA & AKUN --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-7 h-7 bg-green-50 text-green-700 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Biodata & Akun</h3>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-[10px] font-black text-gray-400 uppercase ml-1">Nama Lengkap</label>
                        <input type="text" name="nama" value="{{ old('nama', $peserta->nama) }}" required placeholder="Sesuai KTP"
                               class="w-full px-4 py-2 bg-gray-50 border border-gray-100 rounded-xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 outline-none text-sm transition-all font-medium">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-gray-400 uppercase ml-1">Nomor WhatsApp</label>
                            <input type="text" name="no_hp" value="{{ old('no_hp', $peserta->no_hp) }}" required placeholder="0812xxxx"
                                   class="w-full px-4 py-2 bg-gray-50 border border-gray-100 rounded-xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 outline-none text-sm transition-all font-medium">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-gray-400 uppercase ml-1">Email Sistem</label>
                            <input type="email" name="email" value="{{ old('email', $peserta->user->email ?? '') }}" required placeholder="email@gmail.com"
                                   class="w-full px-4 py-2 bg-gray-50 border border-gray-100 rounded-xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 outline-none text-sm transition-all font-medium">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-[10px] font-black text-gray-400 uppercase ml-1">Alamat Domisili</label>
                        <textarea name="alamat" rows="2" required placeholder="Alamat lengkap..."
                                  class="w-full px-4 py-2 bg-gray-50 border border-gray-100 rounded-xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 outline-none text-sm transition-all font-medium resize-none">{{ old('alamat', $peserta->alamat) }}</textarea>
                    </div>
                </div>

                {{-- KANAN: SKEMA & ACTION --}}
                <div class="flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-7 h-7 bg-orange-50 text-orange-600 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Skema & Status</h3>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-gray-400 uppercase ml-1">Skema Qurban</label>
                            <select name="id_skema" required
                                    class="w-full px-4 py-2 bg-gray-50 border border-gray-100 rounded-xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 outline-none text-sm cursor-pointer font-medium transition-all appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2024%2024%22%20stroke%3D%22currentColor%22%3E%3Cpath%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%222%22%20d%3D%22M19%209l-7%207-7-7%22%20%2F%3E%3C%2Fsvg%3E')] bg-[length:1rem_1rem] bg-[right_1rem_center] bg-no-repeat">
                                @foreach($skemas as $skema)
                                    <option value="{{ $skema->id_skema }}" {{ old('id_skema', $peserta->id_skema) == $skema->id_skema ? 'selected' : '' }}>
                                        {{ $skema->nama_skema }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-gray-400 uppercase ml-1">Status Kepesertaan</label>
                            <div class="grid grid-cols-2 gap-3 p-1">
                                <label class="relative flex items-center justify-center py-2 px-4 border border-gray-100 rounded-xl cursor-pointer group hover:bg-red-50 transition-all has-[:checked]:bg-red-50 has-[:checked]:border-red-200">
                                    <input type="radio" name="status" value="nonaktif" {{ old('status', $peserta->status) == 'nonaktif' ? 'checked' : '' }} class="sr-only">
                                    <span class="text-[11px] font-black text-gray-400 group-hover:text-red-600 group-[.has-[:checked]]:text-red-600 tracking-widest">NONAKTIF</span>
                                </label>
                                <label class="relative flex items-center justify-center py-2 px-4 border border-gray-100 rounded-xl cursor-pointer group hover:bg-green-50 transition-all has-[:checked]:bg-green-50 has-[:checked]:border-green-200">
                                    <input type="radio" name="status" value="aktif" {{ old('status', $peserta->status) == 'aktif' ? 'checked' : '' }} class="sr-only">
                                    <span class="text-[11px] font-black text-gray-400 group-hover:text-green-700 group-[.has-[:checked]]:text-green-700 tracking-widest">AKTIF</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- BUTTONS ACTION --}}
                    <div class="pt-6 flex gap-3 mt-4 lg:mt-0">
                        <a href="{{ route('admin.peserta.index') }}" 
                           class="flex-1 px-4 py-2.5 bg-gray-50 text-gray-500 text-[11px] font-black uppercase tracking-widest rounded-xl text-center hover:bg-gray-100 border border-gray-100 transition-all">
                            Batal
                        </a>
                        <button type="button" onclick="confirmUpdate()"
                                class="flex-[2] bg-[#147a54] hover:bg-green-800 text-white text-[11px] font-black uppercase tracking-widest py-2.5 rounded-xl transition-all shadow-lg shadow-green-900/10 active:scale-[0.98]">
                            Update Data
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- POPUP KONFIRMASI (SWEETALERT2) --}}
<script>
    function confirmUpdate() {
        Swal.fire({
            title: 'Simpan Perubahan?',
            text: "Pastikan data peserta sudah benar sebelum diperbarui.",
            icon: 'question',
            iconColor: '#147a54',
            showCancelButton: true,
            confirmButtonColor: '#064e3b',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'Ya, Update Data',
            cancelButtonText: 'Batal',
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
                document.getElementById('updateForm').submit();
            }
        });
    }
</script>
@endsection