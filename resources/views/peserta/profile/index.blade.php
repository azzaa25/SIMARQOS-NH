@extends('peserta.layout.app')

@section('content')
<div class="max-w-5xl mx-auto animate-fadeInUp h-full">
    
    {{-- Header Section --}}
    <div class="mb-5 flex justify-between items-end">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight uppercase leading-none mb-1">Pengaturan Profil</h1>
            <p class="text-[11px] text-gray-400 font-medium italic uppercase tracking-wider">Update informasi diri & keamanan akun</p>
        </div>
        <div class="px-4 py-2 bg-white border border-gray-100 rounded-xl shadow-sm">
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">ID Peserta</p>
            <p class="text-xs font-bold text-green-700">#{{ str_pad($user->id_user, 4, '0', STR_PAD_LEFT) }}</p>
        </div>
    </div>

    {{-- ALERT SUKSES --}}
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: '<span class="text-lg font-black uppercase tracking-tight">Profil Diperbarui!</span>',
                html: '<p class="text-sm text-gray-500 font-medium">{{ session("success") }}</p>',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                iconColor: '#147a54',
                customClass: { popup: 'rounded-[32px] p-8 shadow-2xl' }
            });
        });
    </script>
    @endif

    <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 overflow-hidden">
        <form id="formUpdateProfil" action="{{ route('peserta.profile.update') }}" method="POST" class="p-6 md:p-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6">
                
                {{-- Kolom Kiri: Data Pribadi --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-6 h-6 bg-green-50 text-green-600 rounded-lg flex items-center justify-center border border-green-100">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <h3 class="text-[10px] font-black text-green-700 uppercase tracking-[0.2em]">Informasi Pribadi</h3>
                    </div>
                    
                    <div class="space-y-1">
                        <label class="text-[9px] font-black text-gray-400 uppercase ml-1">Nama Lengkap</label>
                        <input type="text" name="nama" value="{{ old('nama', $peserta->nama) }}" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl outline-none focus:border-green-600 font-bold text-slate-700 text-xs transition-all">
                    </div>

                    <div class="space-y-1">
                        <label class="text-[9px] font-black text-gray-400 uppercase ml-1">Nomor WhatsApp</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp', $peserta->no_hp) }}" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl outline-none focus:border-green-600 font-bold text-slate-700 text-xs transition-all">
                    </div>

                    <div class="space-y-1">
                        <label class="text-[9px] font-black text-gray-400 uppercase ml-1">Alamat Lengkap</label>
                        <textarea name="alamat" rows="2" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl outline-none focus:border-green-600 font-bold text-slate-700 text-xs transition-all resize-none">{{ old('alamat', $peserta->alamat) }}</textarea>
                    </div>
                </div>

                {{-- Kolom Kanan: Akses Akun --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-6 h-6 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center border border-blue-100">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <h3 class="text-[10px] font-black text-blue-700 uppercase tracking-[0.2em]">Kredensial Akun</h3>
                    </div>
                    
                    <div class="space-y-1">
                        <label class="text-[9px] font-black text-gray-400 uppercase ml-1">Email Aktif</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl outline-none focus:border-green-600 font-bold text-slate-700 text-xs transition-all">
                    </div>

                    <div class="p-5 bg-blue-50/40 border border-blue-50 rounded-2xl space-y-3">
                        <p class="text-[8px] font-black text-blue-400 uppercase tracking-widest text-center">Ubah Password (Opsional)</p>
                        
                        <div class="grid grid-cols-2 gap-3">
                            {{-- Input Password Baru --}}
                            <div class="space-y-1 relative">
                                <input type="password" name="password" id="pass_baru" placeholder="Pass Baru" 
                                    class="w-full px-4 py-2.5 bg-white border border-blue-100 rounded-lg outline-none focus:border-blue-400 text-[10px] font-bold pr-8">
                                <button type="button" onclick="toggleVisibility('pass_baru', 'eye_1')" class="absolute right-2.5 bottom-2.5 text-blue-300 hover:text-blue-500 transition-colors">
                                    <svg id="eye_1" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            {{-- Input Konfirmasi --}}
                            <div class="space-y-1 relative">
                                <input type="password" name="password_confirmation" id="pass_konfirm" placeholder="Konfirmasi" 
                                    class="w-full px-4 py-2.5 bg-white border border-blue-100 rounded-lg outline-none focus:border-blue-400 text-[10px] font-bold pr-8">
                                <button type="button" onclick="toggleVisibility('pass_konfirm', 'eye_2')" class="absolute right-2.5 bottom-2.5 text-blue-300 hover:text-blue-500 transition-colors">
                                    <svg id="eye_2" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <p class="text-[7px] text-blue-400/70 italic text-center">*Kosongkan jika tidak ada perubahan</p>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-8 flex items-center gap-4">
                <button type="button" onclick="confirmUpdate()"
                    class="flex-1 bg-[#147a54] hover:bg-[#064e3b] text-white py-4 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-lg shadow-green-900/10 transition-all transform active:scale-95 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Simpan Perubahan
                </button>
                <a href="{{ route('peserta.dashboard') }}" 
                    class="px-8 py-4 bg-gray-50 hover:bg-gray-100 text-gray-400 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] transition-all border border-gray-100 flex items-center justify-center gap-2">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    // Fungsi Toggle Password
    function toggleVisibility(inputId, eyeId) {
        const input = document.getElementById(inputId);
        const eye = document.getElementById(eyeId);
        
        if (input.type === 'password') {
            input.type = 'text';
            eye.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />`;
        } else {
            input.type = 'password';
            eye.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
        }
    }

    function confirmUpdate() {
        Swal.fire({
            title: '<span class="text-xl font-black uppercase tracking-tight text-slate-800">Simpan Perubahan?</span>',
            html: '<p class="text-sm text-gray-500 font-medium">Pastikan data yang Anda masukkan sudah valid dan benar.</p>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#147a54',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-[32px] p-10 shadow-2xl',
                confirmButton: 'rounded-full px-10 py-3.5 text-sm font-black uppercase tracking-widest transition-all hover:scale-105',
                cancelButton: 'rounded-full px-10 py-3.5 text-sm font-black uppercase tracking-widest text-gray-400 transition-all'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                document.getElementById('formUpdateProfil').submit();
            }
        })
    }
</script>

<style>
    .animate-fadeInUp { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection