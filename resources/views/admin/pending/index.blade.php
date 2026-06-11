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
                    <td class="px-6 py-5">
                        <span class="text-gray-600 font-semibold italic">{{ $user->email }}</span>
                    </td>
                    <td class="px-6 py-5">
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
                            {{-- Tombol Approve: memicu global modal --}}
                            <button type="button"
                                onclick="openApproveModal('{{ $user->id_user }}')"
                                class="px-5 py-2.5 bg-[#147a54] text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-green-800 transition-all shadow-lg shadow-green-900/10 active:scale-95">
                                Approve Akun
                            </button>

                            {{-- Tombol Reject --}}
                            <form action="{{ route('admin.pending.reject', $user->id_user) }}" method="POST" id="reject-{{ $user->id_user }}">
                                @csrf
                                <button type="button" onclick="confirmReject('{{ $user->id_user }}', '{{ addslashes($user->nama) }}')"
                                    class="p-2.5 bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all active:scale-90 shadow-sm" title="Tolak Pendaftaran">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-24 text-center">
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

{{-- ============================================================ --}}
{{-- GLOBAL APPROVE MODALS — Di luar tabel, fixed overlay per user --}}
{{-- ============================================================ --}}
@foreach($users as $user)
<div
    id="modal-approve-{{ $user->id_user }}"
    style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(15,23,42,0.55); backdrop-filter:blur(4px); -webkit-backdrop-filter:blur(4px); align-items:center; justify-content:center; padding:1rem;"
    onclick="handleOverlayClick(event, '{{ $user->id_user }}')"
>
    <div style="background:#ffffff; border-radius:24px; border:1px solid rgba(0,0,0,0.07); padding:2rem; width:100%; max-width:440px; box-sizing:border-box; position:relative; animation: modalIn 0.2s ease-out;">

        {{-- Header --}}
        <div style="display:flex; align-items:flex-start; gap:14px; margin-bottom:1.25rem;">
            <div style="width:44px; height:44px; border-radius:50%; background:#dcfce7; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg style="width:22px;height:22px;" fill="none" stroke="#166534" viewBox="0 0 24 24" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div style="flex:1;">
                <p style="font-size:16px; font-weight:700; color:#111827; margin:0 0 4px;">Setujui &amp; tentukan periode</p>
                <p style="font-size:13px; color:#6b7280; line-height:1.55; margin:0;">
                    Aktifkan akun pendaftar dan pilih tahun pelaksanaan qurban untuk generate iuran otomatis.
                </p>
            </div>
            {{-- Tombol tutup (X) --}}
            <button type="button" onclick="closeApproveModal('{{ $user->id_user }}')"
                style="width:30px; height:30px; border-radius:50%; background:#f3f4f6; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-left:4px;">
                <svg style="width:14px;height:14px;" fill="none" stroke="#6b7280" viewBox="0 0 24 24" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- User badge --}}
        <div style="display:flex; align-items:center; gap:10px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:14px; padding:10px 14px; margin-bottom:1.25rem;">
            <div style="width:36px; height:36px; border-radius:10px; background:#dcfce7; color:#166534; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:14px; flex-shrink:0; text-transform:uppercase;">
                {{ substr($user->nama, 0, 1) }}
            </div>
            <div>
                <p style="font-size:13px; font-weight:700; color:#111827; margin:0;">{{ $user->nama }}</p>
                <p style="font-size:11px; color:#9ca3af; margin:3px 0 0; display:flex; align-items:center; gap:5px;">
                    <span style="width:6px;height:6px;background:#fb923c;border-radius:50%;display:inline-block;"></span>
                    Menunggu verifikasi
                    @if($user->peserta && $user->peserta->skemaArisan)
                        &nbsp;·&nbsp;{{ $user->peserta->skemaArisan->nama_skema }}
                    @endif
                </p>
            </div>
        </div>

        <hr style="border:none; border-top:1px solid #f3f4f6; margin:0 0 1.25rem;">

        {{-- Form --}}
        <form action="{{ route('admin.pending.approve', $user->id_user) }}" method="POST" onsubmit="showLoadingScreen()">
            @csrf
            <label style="display:block; font-size:10px; font-weight:800; color:#9ca3af; text-transform:uppercase; letter-spacing:0.1em; margin-bottom:7px;">
                Tahun periode qurban
            </label>
            <div style="position:relative; margin-bottom:1.5rem;">
                <select name="tahun_periode" required
                    style="width:100%; font-size:13px; font-weight:600; border-radius:12px; border:1px solid #d1d5db; padding:11px 40px 11px 14px; background:#f9fafb; color:#111827; appearance:none; -webkit-appearance:none; cursor:pointer; box-sizing:border-box;">
                    <option value="">-- Pilih periode pelaksanaan --</option>
                    @php $tahunDepan = (int)date('Y') + 1; @endphp
                    @for ($i = 0; $i < 4; $i++)
                        <option value="{{ $tahunDepan + $i }}">Tahun Pelaksanaan {{ $tahunDepan + $i }}</option>
                    @endfor
                </select>
                {{-- Chevron icon --}}
                <svg style="position:absolute;right:13px;top:50%;transform:translateY(-50%);width:15px;height:15px;pointer-events:none;" fill="none" stroke="#9ca3af" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 9l-7 7-7-7"/></svg>
            </div>

            <div style="display:flex; gap:8px; justify-content:flex-end; align-items:center;">
                <button type="button" onclick="closeApproveModal('{{ $user->id_user }}')"
                    style="padding:10px 22px; font-size:12px; font-weight:700; color:#6b7280; background:transparent; border:1px solid #e5e7eb; border-radius:999px; cursor:pointer; transition:background 0.15s;">
                    Batal
                </button>
                <button type="submit"
                    style="padding:10px 22px; font-size:12px; font-weight:700; color:#ffffff; background:#064e3b; border:none; border-radius:999px; cursor:pointer; display:inline-flex; align-items:center; gap:7px; transition:background 0.15s;">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Ya, Aktifkan Akun
                </button>
            </div>
        </form>
    </div>
</div>
@endforeach

{{-- ======================== --}}
{{-- STYLES                   --}}
{{-- ======================== --}}
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn { animation: fadeIn 0.5s ease-out forwards; }

    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.95) translateY(8px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }
</style>

{{-- ======================== --}}
{{-- JAVASCRIPT CONTROLLER    --}}
{{-- ======================== --}}
<script>
    // Buka modal (set display flex agar centering bekerja)
    function openApproveModal(id) {
        const modal = document.getElementById('modal-approve-' + id);
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden'; // cegah scroll background
        }
    }

    // Tutup modal
    function closeApproveModal(id) {
        const modal = document.getElementById('modal-approve-' + id);
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = ''; // kembalikan scroll
        }
    }

    // Klik di luar modal card = tutup (klik overlay)
    function handleOverlayClick(event, id) {
        if (event.target === event.currentTarget) {
            closeApproveModal(id);
        }
    }

    // Tekan ESC untuk tutup semua modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('[id^="modal-approve-"]').forEach(function(modal) {
                modal.style.display = 'none';
            });
            document.body.style.overflow = '';
        }
    });

    // Loading screen saat form disubmit
    function showLoadingScreen() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Memproses...',
                didOpen: () => { Swal.showLoading(); }
            });
        }
    }

    // Konfirmasi penolakan akun
    function confirmReject(id, name) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Tolak Pendaftar?',
                text: `Akun atas nama ${name} akan ditolak dan dihapus dari antrean sistem.`,
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
        } else {
            if (confirm(`Apakah Anda yakin ingin menolak pendaftaran ${name}?`)) {
                document.getElementById('reject-' + id).submit();
            }
        }
    }

    // Notifikasi sukses dari session flash
    @if(session('success'))
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false,
                customClass: { popup: 'rounded-[32px]' }
            });
        }
    @endif
</script>
@endsection