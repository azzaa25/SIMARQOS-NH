@extends('admin.layout.app')

@section('content')
<div class="max-w-5xl mx-auto h-full flex flex-col justify-center animate-fadeIn">
    <div class="flex justify-between items-end mb-3">
        <div>
            <nav class="flex text-[9px] text-gray-400 font-bold uppercase tracking-widest mb-0.5">
                <a href="{{ route('admin.peserta.index') }}" class="hover:text-green-700 transition-colors">Peserta</a>
                <span class="mx-2">/</span>
                <span class="text-green-800">Edit Akses & Status</span>
            </nav>
            <h1 class="text-xl font-bold text-green-900 leading-tight">Edit Akses Akun Peserta</h1>
        </div>
        <p class="text-[10px] text-gray-400 italic">Peserta: {{ $peserta->nama }}</p>
    </div>

    <div class="bg-white rounded-[28px] shadow-lg border border-gray-100 overflow-hidden">
        <form id="updateForm" action="{{ route('admin.peserta.update', $peserta->id_pesertaarisan) }}" method="POST" class="p-6 md:p-8 space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-10 gap-y-5">
                
                {{-- KIRI: BIODATA (READONLY) --}}
                <div class="space-y-4 opacity-70">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-7 h-7 bg-gray-100 text-gray-700 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Biodata (Terkunci)</h3>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-[10px] font-black text-gray-400 uppercase ml-1">Nama Lengkap</label>
                        <input type="text" value="{{ $peserta->nama }}" readonly class="w-full px-4 py-2 bg-gray-100 border border-gray-200 rounded-xl outline-none text-sm font-medium text-gray-500 cursor-not-allowed">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-gray-400 uppercase ml-1">Nomor WhatsApp</label>
                            <input type="text" value="{{ $peserta->no_hp }}" readonly class="w-full px-4 py-2 bg-gray-100 border border-gray-200 rounded-xl outline-none text-sm font-medium text-gray-500 cursor-not-allowed">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-gray-400 uppercase ml-1">Email Sistem</label>
                            <input type="email" value="{{ $peserta->user->email ?? '' }}" readonly class="w-full px-4 py-2 bg-gray-100 border border-gray-200 rounded-xl outline-none text-sm font-medium text-gray-500 cursor-not-allowed">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-[10px] font-black text-gray-400 uppercase ml-1">Alamat Domisili</label>
                        <textarea rows="2" readonly class="w-full px-4 py-2 bg-gray-100 border border-gray-200 rounded-xl outline-none text-sm font-medium resize-none text-gray-500 cursor-not-allowed">{{ $peserta->alamat }}</textarea>
                    </div>
                </div>

                {{-- KANAN: STATUS & PASSWORD (DAPAT DIEDIT ADMIN) --}}
                <div class="flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-7 h-7 bg-green-50 text-green-600 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Kontrol Status & Password</h3>
                        </div>

                        {{-- INPUT STATUS --}}
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-gray-400 uppercase ml-1">Status Kepesertaan</label>
                            <div class="grid grid-cols-2 gap-3 p-1">
                                <label class="relative flex items-center justify-center py-2 px-4 border border-gray-100 rounded-xl cursor-pointer group hover:bg-red-100 transition-all has-[:checked]:bg-red-100 has-[:checked]:border-red-200">
                                    <input type="radio" name="status" value="nonaktif" {{ old('status', $peserta->user->status ?? 'nonaktif') == 'nonaktif' ? 'checked' : '' }} class="sr-only">
                                    <span class="text-[11px] font-black text-gray-400 group-hover:text-red-600 group-[.has-[:checked]]:text-red-600 tracking-widest">NONAKTIF</span>
                                </label>

                                <label class="relative flex items-center justify-center py-2 px-4 border border-gray-100 rounded-xl cursor-pointer group hover:bg-green-200 transition-all has-[:checked]:bg-green-200 has-[:checked]:border-green-200">
                                    <input type="radio" name="status" value="aktif" {{ old('status', $peserta->user->status ?? 'aktif') == 'aktif' ? 'checked' : '' }} class="sr-only">
                                    <span class="text-[11px] font-black text-gray-400 group-hover:text-green-700 group-[.has-[:checked]]:text-green-700 tracking-widest">AKTIF</span>
                                </label>
                            </div>
                            @error('status') <p class="text-[10px] text-red-500 font-bold ml-1 uppercase">{{ $message }}</p> @enderror
                        </div>

                        {{-- INPUT PASSWORD BARU --}}
                        <div class="space-y-1 pt-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase ml-1">Ganti Password (Opsional)</label>
                            <input type="password" name="password" placeholder="Isi hanya jika ingin mengganti password" class="w-full px-4 py-2 bg-gray-50 border @error('password') border-red-400 @else border-gray-100 @enderror rounded-xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 outline-none text-sm transition-all font-medium text-gray-700">
                            <small class="text-[10px] text-gray-400 block mt-1 leading-tight ml-1">*Kosongkan jika tidak ingin mengubah sandi lama peserta.</small>
                            @error('password') <p class="text-[10px] text-red-500 font-bold ml-1 uppercase">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- BUTTONS ACTION --}}
                    <div class="pt-6 flex gap-3 mt-4 lg:mt-0">
                        <a href="{{ route('admin.peserta.index') }}" class="flex-1 px-4 py-2.5 bg-gray-50 text-gray-500 text-[11px] font-black uppercase tracking-widest rounded-xl text-center hover:bg-gray-100 border border-gray-100 transition-all">
                            Batal
                        </a>
                        <button type="button" onclick="confirmUpdate()" class="flex-[2] bg-[#147a54] hover:bg-green-800 text-white text-[11px] font-black uppercase tracking-widest py-2.5 rounded-xl transition-all shadow-lg shadow-green-900/10 active:scale-[0.98]">
                            Simpan Pembaruan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function confirmUpdate() {
        Swal.fire({
            title: 'Simpan Perubahan?',
            text: "Status atau password baru akan langsung diterapkan pada akun peserta.",
            icon: 'question',
            iconColor: '#147a54',
            showCancelButton: true,
            confirmButtonColor: '#064e3b',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'Ya, Simpan',
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
                document.getElementById('updateForm').submit();
            }
        });
    }
</script>
@endsection