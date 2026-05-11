@extends('admin.layout.app')

@section('content')
{{-- Library SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="p-8 animate-fadeIn max-w-[1600px] mx-auto">
    {{-- 1. Header Section --}}
    <div class="mb-10 flex flex-col lg:flex-row justify-between items-start lg:items-end gap-6">
        <div class="space-y-1">
            <h1 class="text-3xl font-black text-green-strong uppercase tracking-tighter leading-none">
                Manajemen Kegiatan Sosial
            </h1>
            <div class="flex items-center gap-2">
                <span class="h-[2px] w-8 bg-[#064e3b]"></span>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] italic">
                    Masjid Nurul Huda • Transparansi & Kemanusiaan
                </p>
            </div>
        </div>
        
        <div class="flex items-center gap-3 w-full lg:w-auto">
            <button onclick="toggleModal('modalKategori')" class="flex-1 lg:flex-none flex items-center justify-center gap-2 bg-white border-2 border-green-100 text-[#064e3b] px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:border-[#064e3b] transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                + Kategori
            </button>

            <button onclick="toggleModal('modalAgenda')" class="flex-1 lg:flex-none flex items-center justify-center gap-2 bg-[#064e3b] text-white px-8 py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-green-900/20 hover:bg-black transition-all transform hover:-translate-y-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                Tambah Agenda
            </button>
        </div>
    </div>

    {{-- 2. Info Cards Section --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-12">
        <div class="bg-white p-7 rounded-[40px] border border-green-50 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-green-50 rounded-full opacity-50 group-hover:scale-150 transition-all"></div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Saldo Kas Sosial</p>
            <h3 class="text-3xl font-black text-green-strong tracking-tighter">Rp {{ number_format($saldoSosial, 0, ',', '.') }}</h3>
            <div class="mt-5 flex items-center gap-2">
                <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span></span>
                <span class="text-[9px] font-black text-green-600 uppercase tracking-widest">Ready to Use</span>
            </div>
        </div>

        <div class="bg-white p-7 rounded-[40px] border border-gray-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-red-50 rounded-full opacity-50 group-hover:scale-150 transition-all"></div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Total Penyaluran</p>
            <h3 class="text-3xl font-black text-red-900 tracking-tighter">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</h3>
            <p class="mt-5 text-[9px] font-bold text-red-400 uppercase italic tracking-widest">* Akumulasi Pengeluaran</p>
        </div>

        <div class="bg-white p-7 rounded-[40px] border border-gray-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-50 rounded-full opacity-50 group-hover:scale-150 transition-all"></div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Total Kegiatan</p>
            <h3 class="text-3xl font-black text-slate-800 tracking-tighter">{{ $totalKegiatanSemua }} <span class="text-xs text-gray-400 uppercase ml-1">Agenda</span></h3>
            <p class="mt-5 text-[9px] font-black text-blue-500 uppercase tracking-widest">Aktif & Selesai</p>
        </div>

        <div class="bg-[#064e3b] p-7 rounded-[40px] shadow-2xl shadow-slate-300 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/5 rounded-full group-hover:scale-150 transition-all"></div>
            <p class="text-[10px] font-black text-green-200/50 uppercase tracking-widest mb-2">Kategori Aktif</p>
            <h3 class="text-3xl font-black text-white tracking-tighter">{{ $kategori->count() }} <span class="text-xs text-green-200 uppercase ml-1">Tipe</span></h3>
            <div class="mt-5 flex items-center -space-x-2">
                @foreach($kategori->take(5) as $kat)
                    <div title="{{ $kat->nama_kategori }}" class="w-7 h-7 rounded-full bg-green-700 border-2 border-[#064e3b] flex items-center justify-center text-[9px] font-black text-white uppercase shadow-lg cursor-help">
                        {{ substr($kat->nama_kategori, 0, 1) }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- 3. Filter Pills --}}
    <div class="mb-8 overflow-x-auto scrollbar-hide flex items-center gap-4 pb-4">
        <div class="flex items-center gap-2 px-3 border-r border-gray-200">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Filter</p>
        </div>
        
        <a href="{{ route('admin.sosial.index') }}" 
           class="px-7 py-2.5 rounded-2xl text-[11px] font-black uppercase whitespace-nowrap transition-all shadow-sm
           {{ !request('kategori') ? 'bg-[#064e3b] text-white shadow-lg shadow-green-900/20' : 'bg-white text-slate-500 border border-gray-100 hover:bg-slate-50' }}">
            Semua
        </a>

        @foreach($kategori as $kat)
            <a href="{{ route('admin.sosial.index', ['kategori' => $kat->id_kategori]) }}" 
               class="px-7 py-2.5 rounded-2xl text-[11px] font-black uppercase whitespace-nowrap transition-all shadow-sm
               {{ request('kategori') == $kat->id_kategori ? 'bg-[#064e3b] text-white shadow-lg shadow-green-900/20' : 'bg-white text-slate-500 border border-gray-100 hover:bg-slate-50' }}">
                {{ $kat->nama_kategori }}
            </a>
        @endforeach
    </div>

    {{-- 4. Main Table Section --}}
    <div class="bg-white rounded-[45px] border border-gray-100 shadow-2xl shadow-slate-100/50 overflow-hidden">
        <div class="px-10 py-8 bg-gray-50/30 border-b border-gray-50 flex justify-between items-center">
            <h2 class="text-sm font-black text-green-900 uppercase tracking-[0.3em]">Log Aktivitas & Donasi</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white">
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-50">Informasi Kegiatan</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center border-b border-gray-50">Status</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center border-b border-gray-50">Waktu & Tempat</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center border-b border-gray-50">Progres Donasi</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center border-b border-gray-50">Berkas</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center border-b border-gray-50">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($kegiatan as $k)
                    <tr class="hover:bg-green-50/40 transition-all group">
                        <td class="px-10 py-7">
                            <div class="flex flex-col gap-1.5">
                                <span class="text-[15px] font-extrabold text-slate-900 uppercase tracking-tight">{{ $k->nama_kegiatan }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 bg-green-100 text-green-800 text-[9px] font-black uppercase rounded-lg border border-green-200">{{ $k->kategori->nama_kategori ?? 'Tanpa Kategori' }}</span>
                                    <span class="text-xs font-medium text-slate-500 italic truncate max-w-[250px]">{{ Str::limit($k->deskripsi_kegiatan, 60) }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- STATUS --}}
                        <td class="px-10 py-7 text-center">
                            @php
                                $statusClasses = [
                                    'rencana' => 'bg-blue-500/10 text-blue-700 border-blue-200/50',
                                    'berlangsung' => 'bg-orange-500/10 text-orange-700 border-orange-200/50 animate-pulse',
                                    'selesai' => 'bg-green-500/10 text-green-700 border-green-200/50'
                                ];
                                $currentClass = $statusClasses[$k->status_kegiatan] ?? 'bg-gray-50 text-gray-500 border-gray-100';
                            @endphp
                            <span class="px-5 py-2 border-2 rounded-xl text-[10px] font-black uppercase tracking-widest {{ $currentClass }}">
                                {{ $k->status_kegiatan }}
                            </span>
                        </td>

                        <td class="px-10 py-7 text-center">
                            <p class="text-[15px] font-black text-slate-800">{{ date('d M Y', strtotime($k->tanggal_kegiatan)) }}</p>
                            <p class="text-[11px] text-gray-400 uppercase font-bold mt-1 tracking-widest">{{ $k->lokasi }}</p>
                        </td>

                        <td class="px-10 py-7">
                            <div class="flex flex-col gap-2 min-w-[180px]">
                                <div class="flex justify-between text-[11px] font-black uppercase">
                                    <span class="text-green-800">Rp {{ number_format($k->total_masuk, 0, ',', '.') }}</span>
                                    <span class="text-gray-300">/ {{ number_format($k->target_donasi, 0, ',', '.') }}</span>
                                </div>
                                <div class="w-full bg-gray-100 h-3 rounded-full overflow-hidden border border-gray-50 shadow-inner">
                                    <div class="bg-green-600 h-full rounded-full transition-all duration-1000 shadow-[0_0_10px_rgba(22,101,52,0.3)]" style="width: {{ $k->persentase_donasi }}%"></div>
                                </div>
                                <span class="text-[10px] font-black text-green-700 uppercase tracking-widest">{{ $k->persentase_donasi }}% Terkumpul</span>
                            </div>
                        </td>

                        <td class="px-10 py-7 text-center">
                            @if($k->pamflet_kegiatan)
                                <a href="{{ asset('storage/'.$k->pamflet_kegiatan) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-blue-600 rounded-xl text-[10px] font-black uppercase hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all shadow-sm">
                                    Lihat Pamflet
                                </a>
                            @else
                                <span class="text-[10px] font-black text-gray-300 uppercase italic">Kosong</span>
                            @endif
                        </td>

                        {{-- TINDAKAN (AKSI) - Diselaraskan dengan Index Peserta --}}
                        <td class="px-10 py-7 text-center">
                            <div class="flex justify-center gap-2.5">
                                @if($k->status_kegiatan == 'selesai')
                                <button onclick="openModalDokumentasi('{{ $k->id_kegiatan }}', '{{ $k->nama_kegiatan }}')" class="p-2.5 bg-white border border-gray-100 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm active:scale-90" title="Dokumentasi">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-width="2.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </button>
                                @endif
                                
                                <a href="{{ route('admin.sosial.show', $k->id_kegiatan) }}" class="p-2.5 bg-white border border-gray-100 text-blue-500 rounded-xl hover:bg-blue-500 hover:text-white transition-all shadow-sm active:scale-90" title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                {{-- Hitung saldo di awal agar bisa digunakan di seluruh blok if --}}
                                @php
                                    $saldoTersedia = $k->total_masuk - $k->total_keluar;
                                @endphp

                                {{-- Tombol Cairkan Dana dengan Proteksi Status & Saldo --}}
                                @if($k->status_kegiatan !== 'rencana' && $saldoTersedia > 0)
                                    <button onclick="openModalCair('{{ $k->id_kegiatan }}', '{{ $k->nama_kegiatan }}', '{{ $saldoTersedia }}')" 
                                        class="p-2.5 bg-white border border-gray-100 text-green-600 rounded-xl hover:bg-green-600 hover:text-white transition-all shadow-sm active:scale-90" 
                                        title="Cairkan Dana">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                @else
                                    {{-- Tampilan Tombol Terkunci --}}
                                    <button type="button" 
                                        class="p-2.5 bg-gray-50 border border-gray-100 text-gray-300 rounded-xl cursor-not-allowed" 
                                        title="{{ $saldoTersedia <= 0 ? 'Saldo sudah habis' : 'Dana tidak dapat dicairkan selama status masih RENCANA' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </button>
                                @endif

                                <button onclick="openModalEdit({{ json_encode($k) }})" class="p-2.5 bg-white border border-gray-100 text-orange-500 rounded-xl hover:bg-orange-500 hover:text-white transition-all shadow-sm active:scale-90" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>

                                <form id="delete-form-{{ $k->id_kegiatan }}" action="{{ route('admin.sosial.destroy', $k->id_kegiatan) }}" method="POST" class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                                <button type="button" onclick="confirmDelete('{{ $k->id_kegiatan }}', '{{ $k->nama_kegiatan }}')" class="p-2.5 bg-white border border-gray-100 text-red-500 rounded-xl hover:bg-red-600 hover:text-white transition-all shadow-sm active:scale-90" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-10 py-32 text-center font-black text-gray-300 uppercase tracking-[0.3em] text-xs">
                            Belum Ada Agenda Terdaftar
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL SECTION --}}
{{-- Modal Kategori --}}
<div id="modalKategori" class="fixed inset-0 z-[100] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="toggleModal('modalKategori')"></div>
        <div class="relative bg-white w-full max-w-md p-8 rounded-[40px] shadow-2xl">
            <h2 class="text-xl font-black text-green-strong uppercase tracking-tighter mb-6">Tambah Kategori</h2>
            <form action="{{ route('admin.sosial.kategori.store') }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Nama Kategori</label>
                    <input type="text" name="nama_kategori" required class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#064e3b] outline-none font-bold text-slate-700 uppercase text-xs">
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="toggleModal('modalKategori')" class="flex-1 px-6 py-3 border-2 border-gray-100 rounded-2xl font-black text-[10px] uppercase text-gray-400">Batal</button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-[#064e3b] text-white rounded-2xl font-black text-[10px] uppercase shadow-lg shadow-green-900/20">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Tambah Agenda --}}
<div id="modalAgenda" class="fixed inset-0 z-[100] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 py-10">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="toggleModal('modalAgenda')"></div>
        <div class="relative bg-white w-full max-w-2xl p-10 rounded-[45px] shadow-2xl">
            <h2 class="text-2xl font-black text-green-strong uppercase tracking-tighter mb-8 text-center">Buat Agenda Open Donasi</h2>
            <form action="{{ route('admin.sosial.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-2 gap-6">
                @csrf
                <div class="col-span-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Judul Kegiatan</label>
                    <input type="text" name="nama_kegiatan" required class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-[#064e3b] outline-none font-bold text-slate-700">
                </div>
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Kategori</label>
                    <select name="id_kategori" required class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-[#064e3b] outline-none font-bold text-slate-700">
                        @foreach($kategori as $kat)
                            <option value="{{ $kat->id_kategori }}">{{ $kat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Tanggal</label>
                    <input type="date" name="tanggal_kegiatan" required class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-[#064e3b] outline-none font-bold text-slate-700">
                </div>
                <div class="col-span-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Lokasi</label>
                    <input type="text" name="lokasi" required class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-[#064e3b] outline-none font-bold text-slate-700">
                </div>
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Target Donasi (Rp)</label>
                    <input type="number" name="target_donasi" required class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-[#064e3b] outline-none font-bold text-slate-700">
                </div>
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Pamflet (JPG/PNG)</label>
                    <input type="file" name="pamflet_kegiatan" required class="w-full text-[10px] font-bold text-gray-400 file:bg-green-50 file:border-none file:rounded-lg file:px-4 file:py-2 file:mr-4">
                </div>
                <div class="col-span-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Deskripsi Singkat</label>
                    <textarea name="deskripsi_kegiatan" rows="2" class="w-full px-6 py-4 bg-gray-50 border-none rounded-3xl focus:ring-2 focus:ring-[#064e3b] outline-none font-bold text-slate-700"></textarea>
                </div>
                <div class="col-span-2 flex gap-4 mt-4">
                    <button type="button" onclick="toggleModal('modalAgenda')" class="flex-1 py-4 border-2 border-gray-100 rounded-2xl font-black text-[10px] uppercase text-gray-400">Batal</button>
                    <button type="submit" class="flex-[2] py-4 bg-[#064e3b] text-white rounded-2xl font-black text-[10px] uppercase shadow-xl shadow-green-900/20">Simpan Agenda</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit Agenda --}}
<div id="modalEdit" class="fixed inset-0 z-[100] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 py-10">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="toggleModal('modalEdit')"></div>
        <div class="relative bg-white w-full max-w-2xl p-10 rounded-[45px] shadow-2xl">
            <h2 class="text-2xl font-black text-green-strong uppercase tracking-tighter mb-8 text-center">Update Agenda</h2>
            <form id="formEdit" method="POST" enctype="multipart/form-data" class="grid grid-cols-2 gap-6">
                @csrf @method('PUT')
                <div class="col-span-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Judul Kegiatan</label>
                    <input type="text" name="nama_kegiatan" id="edit_nama" required class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-[#064e3b] outline-none font-bold text-slate-700">
                </div>
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Kategori</label>
                    <select name="id_kategori" id="edit_kategori" required class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-[#064e3b] outline-none font-bold text-slate-700">
                        @foreach($kategori as $kat)
                            <option value="{{ $kat->id_kategori }}">{{ $kat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Tanggal</label>
                    <input type="date" name="tanggal_kegiatan" id="edit_tanggal" required class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-[#064e3b] outline-none font-bold text-slate-700">
                </div>
                <div class="col-span-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Lokasi</label>
                    <input type="text" name="lokasi" id="edit_lokasi" required class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-[#064e3b] outline-none font-bold text-slate-700">
                </div>
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Target Donasi (Rp)</label>
                    <input type="number" name="target_donasi" id="edit_target" required class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-[#064e3b] outline-none font-bold text-slate-700">
                </div>
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Ganti Pamflet (Opsional)</label>
                    <input type="file" name="pamflet_kegiatan" class="w-full text-[10px] font-bold text-gray-400 file:bg-green-50 file:border-none file:rounded-lg file:px-4 file:py-2 file:mr-4">
                </div>
                <div class="col-span-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Deskripsi Singkat</label>
                    <textarea name="deskripsi_kegiatan" id="edit_deskripsi" rows="2" class="w-full px-6 py-4 bg-gray-50 border-none rounded-3xl focus:ring-2 focus:ring-[#064e3b] outline-none font-bold text-slate-700"></textarea>
                </div>
                <div class="col-span-2 flex gap-4 mt-4">
                    <button type="button" onclick="toggleModal('modalEdit')" class="flex-1 py-4 border-2 border-gray-100 rounded-2xl font-black text-[10px] uppercase text-gray-400">Batal</button>
                    <button type="submit" class="flex-[2] py-4 bg-[#064e3b] text-white rounded-2xl font-black text-[10px] uppercase shadow-xl shadow-green-900/20">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Cairkan Dana --}}
<div id="modalCairkan" class="fixed inset-0 z-[100] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="toggleModal('modalCairkan')"></div>
        <div class="relative bg-white w-full max-w-md p-8 rounded-[40px] shadow-2xl">
            <h2 class="text-xl font-black text-green-strong uppercase tracking-tighter mb-2">Pencairan Dana Donasi</h2>
            <p id="cair_judul" class="text-[10px] font-bold text-blue-500 uppercase mb-6"></p>
            
            <form id="formCairkan" method="POST">
                @csrf
                <div class="mb-6">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">
                        Nominal Cair (Tersedia: <span id="cair_saldo_text"></span>)
                    </label>
                    {{-- Tambahkan id="input_nominal" dan oninput --}}
                    <input type="number" name="nominal" id="input_nominal" oninput="cekSaldo()" required 
                        class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-green-500 outline-none font-bold text-slate-700">
                    
                    {{-- Tempat pesan error muncul --}}
                    <p id="error_pencairan" class="text-[9px] font-black text-red-600 uppercase mt-2 hidden">
                         Nominal terlalu banyak / Melebihi saldo terkumpul
                    </p>
                </div>
                
                <div class="mb-6">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Keterangan Penggunaan</label>
                    <textarea name="keterangan" required class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-green-500 outline-none font-bold text-slate-700 text-xs" placeholder="Masukkan keterangan penggunaan dana..."></textarea>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" onclick="toggleModal('modalCairkan')" class="flex-1 px-6 py-3 border-2 border-gray-100 rounded-2xl font-black text-[10px] uppercase text-gray-400">Batal</button>
                    {{-- Tambahkan id="btn_konfirmasi_cair" --}}
                    <button type="submit" id="btn_konfirmasi_cair" class="flex-[2] px-6 py-3 bg-green-600 text-white rounded-2xl font-black text-[10px] uppercase shadow-lg shadow-green-200 transition-all">Konfirmasi Cair</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Dokumentasi --}}
<div id="modalDokumentasi" class="fixed inset-0 z-[100] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-green-900/60 backdrop-blur-sm" onclick="toggleModal('modalDokumentasi')"></div>
        <div class="relative bg-white w-full max-w-md p-8 rounded-[40px] shadow-2xl">
            <h2 class="text-xl font-black text-green-strong uppercase tracking-tighter mb-2">Upload Dokumentasi</h2>
            <p id="dok_judul" class="text-[10px] font-black text-blue-600 uppercase mb-6 tracking-widest"></p>
            
            <form id="formDokumentasi" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-8">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-3">Pilih Foto (Bisa lebih dari satu)</label>
                    <input type="file" name="dokumentasi[]" multiple required 
                           class="w-full text-xs text-green-700 font-bold file:mr-4 file:py-2.5 file:px-6 file:rounded-2xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer transition-all">
                    <p class="text-[9px] text-gray-400 mt-3 italic">* Format: JPG, PNG, JPEG. Max 2MB per file.</p>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" onclick="toggleModal('modalDokumentasi')" class="flex-1 px-6 py-3 border-2 border-gray-100 rounded-2xl font-black text-[10px] uppercase text-gray-400">Batal</button>
                    <button type="submit" class="flex-[2] px-6 py-3 bg-[#064e3b] text-white rounded-2xl font-black text-[10px] uppercase shadow-xl shadow-green-900/20 hover:bg-green-900 transition-all transform active:scale-95">Simpan Dokumentasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleModal(id) {
        const modal = document.getElementById(id);
        modal.classList.toggle('hidden');
    }

    function openModalEdit(data) {
        const form = document.getElementById('formEdit');
        form.action = `/admin/sosial/update/${data.id_kegiatan}`;
        
        document.getElementById('edit_nama').value = data.nama_kegiatan;
        document.getElementById('edit_kategori').value = data.id_kategori;
        document.getElementById('edit_tanggal').value = data.tanggal_kegiatan;
        document.getElementById('edit_lokasi').value = data.lokasi;
        document.getElementById('edit_target').value = data.target_donasi;
        document.getElementById('edit_deskripsi').value = data.deskripsi_kegiatan;
        
        toggleModal('modalEdit');
    }

    // Variabel global untuk menyimpan saldo maksimal kegiatan yang dipilih
    let saldoMaksimal = 0;

    function openModalCair(id, nama, saldo) {
        const form = document.getElementById('formCairkan');
        form.action = `/admin/sosial/cairkan/${id}`;
        
        // Simpan saldo ke variabel global
        saldoMaksimal = parseFloat(saldo);
        
        document.getElementById('cair_judul').innerText = nama;
        document.getElementById('cair_saldo_text').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(saldo);
        
        // Reset input dan error saat buka modal
        document.getElementById('input_nominal').value = '';
        document.getElementById('error_pencairan').classList.add('hidden');
        document.getElementById('btn_konfirmasi_cair').disabled = false;
        document.getElementById('btn_konfirmasi_cair').classList.remove('opacity-50', 'cursor-not-allowed');

        toggleModal('modalCairkan');
    }

    function cekSaldo() {
        const inputNominal = document.getElementById('input_nominal');
        const errorPesan = document.getElementById('error_pencairan');
        const btnSubmit = document.getElementById('btn_konfirmasi_cair');
        
        let nilaiInput = parseFloat(inputNominal.value);

        // Cek jika nominal melebihi saldo terkumpul
        if (nilaiInput > saldoMaksimal) {
            errorPesan.classList.remove('hidden'); // Munculkan tulisan merah
            btnSubmit.disabled = true; // Kunci tombol
            btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            errorPesan.classList.add('hidden'); // Sembunyikan tulisan merah
            btnSubmit.disabled = false; // Buka tombol
            btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    function openModalDokumentasi(id, nama) {
        const form = document.getElementById('formDokumentasi');
        form.action = `/admin/sosial/upload-dokumentasi/${id}`;
        document.getElementById('dok_judul').innerText = 'KEGIATAN: ' + nama;
        toggleModal('modalDokumentasi');
    }

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Agenda?',
            html: `Anda akan menghapus Kegiatan <b class="text-gray-600">${name}</b>.<br><span class="text-xs text-gray-500">Data yang terhapus tidak dapat dipulihkan kembali.</span>`,
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
                title: 'text-2xl font-black text-slate-800 uppercase tracking-tighter',
                htmlContainer: 'text-sm font-medium text-gray-600',
                confirmButton: 'rounded-2xl px-8 py-3 text-[10px] font-black uppercase tracking-widest shadow-lg ml-2',
                cancelButton: 'rounded-2xl px-8 py-3 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:bg-gray-100'
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

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fadeIn { animation: fadeIn 0.5s ease-out forwards; }
    
    /* Perbaikan font tabel lebih besar & kontras tinggi */
    table tbody tr td {
        font-size: 0.95rem;
    }
    .text-green-strong {
        color: #064e3b;
    }
</style>
@endsection