@extends('admin.layout.app')

@section('content')
{{-- HEADER SECTION --}}
<div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
    <div>
        <nav class="flex text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em] mb-1">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-green-700 transition-colors">Dashboard Admin</a>
            <span class="mx-2 text-gray-300">/</span>
            <span class="text-green-800">Verifikasi Akun</span>
        </nav>
        <h1 class="text-2xl font-extrabold text-green-900 leading-tight tracking-tight">Persetujuan Akun</h1>
        <p class="text-sm text-gray-400 italic mt-1">Konfirmasi pendaftaran peserta baru yang sedang menunggu verifikasi sistem</p>
    </div>
    
    {{-- Tombol Kembali ke Dashboard --}}
    <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 bg-white text-gray-500 text-[11px] font-black uppercase tracking-widest rounded-xl border border-gray-100 shadow-sm hover:bg-gray-50 hover:text-gray-700 transition-all flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        Kembali ke Dashboard
    </a>
</div>

{{-- DATA TABLE --}}
<div class="bg-white rounded-[32px] shadow-xl shadow-gray-200/40 border border-gray-100 overflow-hidden animate-fadeIn">
    <div class="p-6 border-b border-gray-50 bg-gray-50/30 flex justify-between items-center">
        <h3 class="font-bold text-green-900 flex items-center gap-3 text-sm uppercase tracking-widest">
            <span class="w-2 h-6 bg-orange-500 rounded-full"></span>
            Daftar Antrean Pendaftaran ({{ $users->count() }})
        </h3>
    </div>

    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left">
            <thead class="bg-gray-50/50 text-[10px] uppercase tracking-widest text-gray-400 font-black border-b border-gray-100">
                <tr>
                    <th class="px-8 py-6">Profil Pendaftar</th>
                    <th class="px-6 py-6">Kontak Email</th>
                    <th class="px-6 py-6">Skema Arisan</th>
                    <th class="px-6 py-6 text-center">Waktu Registrasi</th>
                    <th class="px-8 py-6 text-center">Tindakan Konfirmasi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm">
                @forelse($users as $user)
                <tr class="hover:bg-orange-50/10 transition-colors group">
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center text-xs font-bold uppercase shadow-sm transform group-hover:scale-110 transition-transform">
                                {{ substr($user->nama, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-800 leading-none mb-1.5">{{ $user->nama }}</p>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-400 animate-pulse"></span>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">Status: Menunggu</p>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5 text-center md:text-left">
                        <span class="text-gray-600 font-semibold italic">{{ $user->email }}</span>
                    </td>
                    <td class="px-6 py-5 text-center">
                        @if($user->peserta && $user->peserta->skemaArisan)
                            <span class="px-4 py-1.5 bg-green-50 text-green-700 border border-green-100 rounded-xl text-[10px] font-black uppercase tracking-wide">
                                {{ $user->peserta->skemaArisan->nama_skema }}
                            </span>
                        @else
                            <span class="text-gray-300 italic text-[10px]">Belum Memilih</span>
                        @endif
                    </td>
                    <td class="px-6 py-5 text-center">
                        <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-lg text-[10px] font-bold uppercase tracking-tighter">
                            {{ $user->created_at->diffForHumans() }}
                        </span>
                    </td>
                    <td class="px-8 py-5 text-center">
                        <div class="flex justify-center items-center gap-3">
                            {{-- Button Approve --}}
                            <form action="{{ route('admin.pending.approve', $user->id_user) }}" method="POST" id="approve-{{ $user->id_user }}">
                                @csrf
                                <button type="button" onclick="confirmApprove('{{ $user->id_user }}', '{{ $user->nama }}')"
                                    class="px-5 py-2.5 bg-[#147a54] text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-green-800 transition-all shadow-lg shadow-green-900/10 active:scale-95">
                                    Approve Akun
                                </button>
                            </form>

                            {{-- Button Reject --}}
                            <form action="{{ route('admin.pending.reject', $user->id_user) }}" method="POST" id="reject-{{ $user->id_user }}">
                                @csrf
                                <button type="button" onclick="confirmReject('{{ $user->id_user }}', '{{ $user->nama }}')"
                                    class="p-2.5 bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all active:scale-90 shadow-sm" title="Tolak Pendaftaran">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-24 text-center">
                        <div class="flex flex-col items-center justify-center opacity-30 group">
                            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <p class="font-black text-[11px] uppercase tracking-[0.3em] text-gray-500">Antrean Verifikasi Kosong</p>
                            <p class="text-[10px] text-gray-400 mt-1 italic">Semua pendaftar baru telah diproses</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fadeIn { animation: fadeIn 0.5s ease-out forwards; }
</style>

{{-- SCRIPT SWEETALERT --}}
<script>
    function confirmApprove(id, name) {
        Swal.fire({
            title: 'Setujui Pendaftaran?',
            html: `Anda akan memberikan akses login kepada <b class="text-green-800">${name}</b>.`,
            icon: 'question',
            iconColor: '#147a54',
            showCancelButton: true,
            confirmButtonColor: '#064e3b',
            confirmButtonText: 'Ya, Setujui Akun',
            cancelButtonText: 'Cek Kembali',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-[32px] border-none shadow-2xl',
                confirmButton: 'rounded-full px-8 py-3 text-sm font-bold shadow-lg shadow-green-900/20 ml-2',
                cancelButton: 'rounded-full px-8 py-3 text-sm font-bold text-gray-500 hover:bg-gray-100'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Memproses...', didOpen: () => { Swal.showLoading(); } });
                document.getElementById('approve-' + id).submit();
            }
        });
    }

    function confirmReject(id, name) {
        Swal.fire({
            title: 'Tolak Pendaftar?',
            text: `Akun atas nama ${name} akan ditolak dan status diubah menjadi nonaktif.`,
            icon: 'warning',
            iconColor: '#ef4444',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Ya, Tolak',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-[32px] border-none shadow-2xl',
                confirmButton: 'rounded-full px-8 py-3 text-sm font-bold shadow-lg shadow-red-900/20 ml-2',
                cancelButton: 'rounded-full px-8 py-3 text-sm font-bold text-gray-500'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('reject-' + id).submit();
            }
        });
    }

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Terselesaikan!',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false,
            customClass: { popup: 'rounded-[32px]' }
        });
    @endif
</script>
@endsection