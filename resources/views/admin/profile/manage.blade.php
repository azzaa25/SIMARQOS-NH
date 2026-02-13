@extends('admin.layout.app')

@section('content')
<div class="max-w-6xl mx-auto animate-fadeIn h-[calc(100vh-140px)] flex flex-col">
    <div class="flex justify-between items-end mb-8 shrink-0">
        <div>
            <h1 class="text-2xl font-black text-green-900 leading-tight">Manajemen Admin</h1>
            <p class="text-xs text-gray-400 font-medium">Kelola hak akses dan otoritas sistem</p>
        </div>
        {{-- Tombol Kembali --}}
        <a href="{{ route('admin.profile.index') }}" class="px-5 py-2.5 bg-white text-gray-500 text-[11px] font-black uppercase tracking-widest rounded-2xl border border-gray-100 shadow-sm hover:bg-gray-50 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 flex-1 min-h-0">
        <div class="lg:col-span-4 shrink-0">
            <div class="bg-white rounded-[32px] shadow-xl border border-gray-100 p-8">
                <h3 class="font-black text-gray-800 mb-6 flex items-center gap-3 text-sm uppercase tracking-widest">
                    <span class="w-2 h-5 {{ $editAdmin ? 'bg-orange-500' : 'bg-green-600' }} rounded-full"></span>
                    {{ $editAdmin ? 'Edit Data Admin' : 'Tambah Admin' }}
                </h3>

                <form action="{{ $editAdmin ? route('admin.manage.update', $editAdmin->id_user) : route('admin.manage.store') }}" method="POST" class="space-y-5">
                    @csrf
                    @if($editAdmin) @method('PUT') @endif

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="nama" value="{{ old('nama', $editAdmin->nama ?? '') }}" required 
                            class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 focus:border-green-600 outline-none text-sm font-bold text-gray-700">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-1.5">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email', $editAdmin->email ?? '') }}" required 
                            class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 focus:border-green-600 outline-none text-sm font-bold text-gray-700">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-1.5">
                            Password {{ $editAdmin ? '(Kosongi jika tidak ganti)' : '' }}
                        </label>
                        <input type="password" name="password" {{ $editAdmin ? '' : 'required' }}
                            class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 focus:border-green-600 outline-none text-sm font-bold text-gray-700">
                    </div>

                    <div class="pt-4 flex flex-col gap-2">
                        <button type="submit" class="w-full {{ $editAdmin ? 'bg-orange-500 hover:bg-orange-600' : 'bg-[#147a54] hover:bg-green-800' }} text-white font-black py-4 rounded-2xl shadow-lg transition-all active:scale-95 text-[11px] uppercase tracking-widest">
                            {{ $editAdmin ? 'Simpan Perubahan' : 'Daftarkan Admin' }}
                        </button>
                        
                        @if($editAdmin)
                            <a href="{{ route('admin.manage.index') }}" class="w-full bg-gray-100 text-gray-500 font-black py-3 rounded-2xl text-center text-[10px] uppercase tracking-widest hover:bg-gray-200 transition-all">
                                Batal Edit
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-8 flex flex-col min-h-0">
            <div class="bg-white rounded-[32px] shadow-xl border border-gray-100 overflow-hidden flex flex-col flex-1">
                <div class="overflow-y-auto custom-scrollbar flex-1">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/50 sticky top-0 z-10 border-b border-gray-100">
                            <tr>
                                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center w-20">Avatar</th>
                                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-left">Detail Admin</th>
                                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($admins as $adm)
                            <tr class="hover:bg-gray-50/50 transition-colors group {{ request('edit') == $adm->id_user ? 'bg-orange-50/50' : '' }}">
                                <td class="px-8 py-4">
                                    <div class="w-10 h-10 mx-auto bg-green-100 text-green-700 rounded-xl flex items-center justify-center text-xs font-black shadow-sm group-hover:scale-110 transition-transform">
                                        {{ substr($adm->nama, 0, 1) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.manage.index', ['edit' => $adm->id_user]) }}" class="block group/link">
                                        <p class="text-sm font-black text-gray-800 group-hover/link:text-green-700 transition-colors">{{ $adm->nama }}</p>
                                        <p class="text-[11px] text-gray-400 font-bold tracking-tight italic">{{ $adm->email }}</p>
                                    </a>
                                </td>
                                <td class="px-8 py-4">
                                    <div class="flex justify-center items-center gap-2">
                                        {{-- Edit Link --}}
                                        <a href="{{ route('admin.manage.index', ['edit' => $adm->id_user]) }}" 
                                           class="p-2 bg-blue-50 text-blue-500 rounded-xl hover:bg-blue-500 hover:text-white transition-all" title="Edit Admin">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </a>

                                        {{-- Hapus Button (Hanya jika bukan diri sendiri) --}}
                                        @if(Auth::id() != $adm->id_user)
                                            <form action="{{ route('admin.manage.destroy', $adm->id_user) }}" method="POST" onsubmit="return confirmHapus('{{ $adm->nama }}')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-2 bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all" title="Hapus Admin">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmHapus(name) {
        return event.preventDefault(), Swal.fire({
            title: 'Hapus Admin?',
            text: `Akun "${name}" akan dihapus permanen dari akses sistem.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-[32px]' }
        }).then((result) => {
            if (result.isConfirmed) {
                event.target.submit();
            }
        }), false;
    }
</script>
@endsection