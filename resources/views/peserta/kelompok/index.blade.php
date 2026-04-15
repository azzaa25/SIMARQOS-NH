@extends('peserta.layout.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="max-w-5xl mx-auto animate-fadeInUp">

    {{-- ================= HEADER ================= --}}
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-green-900 tracking-tight">Manajemen Kelompok</h1>
            <p class="text-sm text-gray-400 font-medium italic">
                Kode Kelompok: 
                <span class="text-[#147a54] font-bold">{{ $kelompok->kode_kelompok ?? 'Belum Terbentuk' }}</span>
            </p>
        </div>

        <div class="px-6 py-3 bg-white border border-gray-100 rounded-2xl shadow-sm text-center">
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Status Kelompok</p>
            <p class="text-xs font-black uppercase {{ $sisaKuota <= 0 ? 'text-green-600' : 'text-orange-500' }}">
                {{ $sisaKuota <= 0 ? 'Lengkap' : 'Belum Lengkap' }}
            </p>
        </div>
    </div>

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
            customClass: { popup: 'rounded-[24px]' }
        });
    </script>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        {{-- ================= LEFT SIDE: INFO & FORM ================= --}}
        <div class="space-y-6 sticky top-8"> {{-- Tambahkan sticky agar form tetap terlihat saat scroll --}}
            <div class="bg-[#064e3b] rounded-[32px] p-8 text-white shadow-xl shadow-green-900/20 relative overflow-hidden transition-all hover:scale-[1.02]">
                <svg class="absolute -right-4 -bottom-4 w-24 h-24 text-white opacity-5 rotate-12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2z"/></svg>
                <p class="text-[10px] font-black text-green-300/60 uppercase tracking-[0.2em] mb-1">Sisa Kuota Anggota</p>
                <p class="text-6xl font-black leading-none">{{ $sisaKuota }}</p>
                <p class="text-[10px] text-green-200/80 mt-4 font-medium italic leading-relaxed uppercase tracking-tighter">
                    Skema: {{ $skema->nama_skema ?? 'Kelompok' }}
                </p>
            </div>

            @if($isKetua && $sisaKuota > 0)
            <div class="bg-white rounded-[32px] p-8 shadow-sm border border-gray-100 animate-fadeIn">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 bg-green-50 text-green-600 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest leading-none">Tambah Anggota</h3>
                </div>
                <form action="{{ route('peserta.kelompok.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="text" name="nama" placeholder="Nama Lengkap" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-xl outline-none focus:border-[#147a54] text-sm font-semibold transition-all">
                    <input type="text" name="no_hp" placeholder="Nomor WhatsApp" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-xl outline-none focus:border-[#147a54] text-sm font-semibold transition-all">
                    <button type="submit" class="w-full py-5 bg-[#147a54] text-white rounded-xl font-black text-[10px] uppercase tracking-[0.2em] shadow-lg hover:bg-[#064e3b] transition-all transform active:scale-95 shadow-green-900/10">
                        Simpan Anggota Baru
                    </button>
                </form>
            </div>
            @elseif(!$isKetua)
            <div class="bg-blue-50 border border-blue-100 rounded-[32px] p-8 text-center">
                <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-2">Informasi</p>
                <p class="text-xs font-bold text-blue-800 leading-relaxed">Anda terdaftar sebagai anggota. Manajemen anggota hanya dapat dilakukan oleh Ketua Kelompok.</p>
            </div>
            @endif
        </div>

        {{-- ================= RIGHT SIDE: LIST ANGGOTA (SCROLLABLE SECTION) ================= --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-[40px] shadow-sm border border-gray-50 overflow-hidden flex flex-col">
                <div class="p-8 border-b border-gray-50 flex justify-between items-center bg-gray-50/30 shrink-0">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest leading-none">Daftar Anggota</h3>
                    <span class="px-3 py-1 bg-white border border-gray-100 rounded-full text-[10px] font-black text-gray-400 uppercase tracking-tighter">{{ $anggota->count() }} / 7</span>
                </div>

                {{-- PERBAIKAN: Kontainer Scroll Internal --}}
                <div class="p-4 space-y-3 max-h-[580px] overflow-y-auto custom-scrollbar">
                    @forelse($anggota as $index => $person)
                    <div class="flex items-center justify-between p-6 bg-gray-50/50 rounded-[32px] border border-transparent hover:border-green-100 hover:bg-white transition-all group">
                        <div class="flex items-start gap-5">
                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center font-black text-xs text-[#147a54] shadow-sm group-hover:bg-[#147a54] group-hover:text-white transition-all shrink-0 mt-1">
                                {{ sprintf("%02d", $index + 1) }}
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm font-black text-slate-800 uppercase leading-tight">{{ $person->nama }}</p>
                                    <p class="text-[9px] font-bold uppercase tracking-widest mt-1">
                                        @if($kelompok && $person->id_pesertaarisan == $kelompok->id_ketua_peserta)
                                            <span class="text-[#147a54]">Ketua Kelompok</span>
                                        @else
                                            <span class="text-gray-400">Anggota Kelompok</span>
                                        @endif
                                        
                                        @if($person->id_user == auth()->id())
                                            <span class="text-orange-500 ml-1">(Anda)</span>
                                        @endif
                                    </p>
                                </div>
                                
                                {{-- INFO LOGIN --}}
                                <div class="bg-white/50 border border-gray-100 rounded-2xl p-3 space-y-2">
                                    <div class="flex items-center gap-3">
                                        <div class="w-7 h-7 bg-blue-50 text-blue-500 rounded-lg flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        </div>
                                        <div class="flex flex-col">
                                            <p class="text-[8px] text-gray-400 uppercase font-black leading-none mb-0.5">Email Login</p>
                                            <p class="text-[10px] font-bold text-slate-600 leading-none">{{ $person->user->email ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="w-7 h-7 bg-orange-50 text-orange-500 rounded-lg flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        </div>
                                        <div class="flex flex-col">
                                            <p class="text-[8px] text-gray-400 uppercase font-black leading-none mb-0.5">Password</p>
                                            <p class="text-[10px] font-bold text-orange-600 leading-none">
                                                @if($isKetua || $person->id_user == auth()->id()) 12345678 @else ******** @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            @if($isKetua && $kelompok && $person->id_pesertaarisan != $kelompok->id_ketua_peserta)
                            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                <button onclick="openEditModal('{{ $person->id_pesertaarisan }}', '{{ $person->nama }}', '{{ $person->no_hp }}')" 
                                        class="p-2.5 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm active:scale-90">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>

                                <button onclick="confirmDelete('{{ $person->id_pesertaarisan }}', '{{ $person->nama }}')" 
                                        class="p-2.5 bg-red-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition-all shadow-sm active:scale-90">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>

                                <form id="delete-form-{{ $person->id_pesertaarisan }}" action="{{ route('peserta.kelompok.destroy', $person->id_pesertaarisan) }}" method="POST" class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                            @endif

                            <div class="text-right min-w-[100px]">
                                <p class="text-[11px] font-black text-[#147a54] uppercase tracking-tighter">{{ $person->no_hp }}</p>
                                <p class="text-[8px] font-bold text-gray-300 uppercase tracking-widest mt-0.5">WhatsApp</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="py-20 text-center text-gray-300 italic font-medium">Belum ada anggota.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tambahkan CSS khusus untuk scrollbar agar lebih cantik --}}
<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 5px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1;
    }
</style>

{{-- MODAL EDIT TETAP SAMA --}}
@if($isKetua)
<div id="editModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md p-8 rounded-[32px] shadow-2xl animate-zoomIn">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight">Edit Anggota</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-red-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="editForm" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <input type="text" name="nama" id="editNama" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-xl outline-none focus:border-[#147a54] text-sm font-semibold">
            <input type="text" name="no_hp" id="editNoHp" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-xl outline-none focus:border-[#147a54] text-sm font-semibold">
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeEditModal()" class="flex-1 py-4 bg-gray-100 text-gray-500 rounded-xl font-black text-[10px] uppercase tracking-widest">Batal</button>
                <button type="submit" class="flex-1 py-4 bg-[#147a54] text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-green-900/20">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    function openEditModal(id, nama, no_hp) {
        document.getElementById('editModal').classList.remove('hidden');
        document.getElementById('editForm').action = "{{ url('/peserta/kelompok/anggota') }}/" + id;
        document.getElementById('editNama').value = nama;
        document.getElementById('editNoHp').value = no_hp;
    }
    function closeEditModal() { document.getElementById('editModal').classList.add('hidden'); }
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Anggota?',
            html: `Anda akan menghapus <b class="text-green-800">${name}</b> dari kelompok.<br><span class="text-xs text-gray-500 italic">Akun login anggota ini juga akan dihapus.</span>`,
            icon: 'warning',
            iconColor: '#d33',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-[32px]', confirmButton: 'rounded-full px-8 py-3 bg-red-600 text-white font-bold ml-2', cancelButton: 'rounded-full px-8 py-3 bg-gray-200 text-gray-500 font-bold' },
            buttonsStyling: false,
        }).then((result) => { if (result.isConfirmed) { document.getElementById('delete-form-' + id).submit(); } });
    }
</script>
@endsection