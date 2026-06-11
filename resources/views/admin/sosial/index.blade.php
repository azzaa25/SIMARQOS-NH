@extends('admin.layout.app')

@section('content')
{{-- Library SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="p-6 lg:p-10 animate-fadeIn max-w-[1600px] mx-auto bg-[#f8fafc] min-h-screen">
    
    {{-- 1. HEADER SECTION --}}
    <div class="flex flex-col md:flex-row justify-between items-center gap-6 mb-10">
        <div class="text-center md:text-left">
            <h1 class="text-3xl font-black text-slate-800 uppercase tracking-tighter leading-none">
                Manajemen Sosial
            </h1>
            <div class="flex items-center justify-center md:justify-start gap-2 mt-2">
                <span class="h-[2px] w-6 bg-green-600"></span>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">
                    Masjid Nurul Huda • Transparansi & Kemanusiaan
                </p>
            </div>
        </div>
        
        <div class="flex items-center gap-3 w-full md:w-auto">
            <button onclick="toggleModal('modalKategori')" class="flex-1 md:flex-none px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm">
                + Kategori
            </button>
            <button onclick="toggleModal('modalAgenda')" class="flex-1 md:flex-none px-8 py-3 bg-[#064e3b] text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-green-900/20 hover:bg-black transition-all transform hover:-translate-y-1">
                Tambah Agenda
            </button>
        </div>
    </div>

    {{-- 2. STATISTIC CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-7 rounded-[32px] border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-green-50 rounded-full opacity-50 group-hover:scale-150 transition-all"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Saldo Kas Sosial</p>
            <h3 class="text-2xl font-black text-[#064e3b] tracking-tighter">Rp {{ number_format($saldoSosial, 0, ',', '.') }}</h3>
        </div>

        <div class="bg-white p-7 rounded-[32px] border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-red-50 rounded-full opacity-50 group-hover:scale-150 transition-all"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Total Penyaluran</p>
            <h3 class="text-2xl font-black text-red-600 tracking-tighter">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</h3>
        </div>

        <div class="bg-white p-7 rounded-[32px] border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-blue-50 rounded-full opacity-50 group-hover:scale-150 transition-all"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Total Agenda</p>
            <h3 class="text-2xl font-black text-slate-800 tracking-tighter">{{ $totalKegiatanSemua }} <span class="text-xs text-slate-400 uppercase ml-1">Kegiatan</span></h3>
        </div>
    </div>

    {{-- 3. FILTER & TAB SECTION --}}
    <div class="flex flex-col lg:flex-row justify-between items-center gap-6 mb-8">
        {{-- Status Tabs --}}
        <div class="flex bg-white p-1.5 rounded-2xl border border-slate-200 shadow-sm w-full lg:w-auto overflow-x-auto">
            @php $currentStatus = request('status'); @endphp
            <a href="{{ route('admin.sosial.index') }}" 
               class="flex-1 lg:flex-none text-center px-6 py-2.5 rounded-xl text-[10px] font-black uppercase transition-all {{ !$currentStatus ? 'bg-[#064e3b] text-white shadow-md' : 'text-slate-500 hover:bg-slate-50' }}">
                Semua
            </a>
            <a href="{{ route('admin.sosial.index', ['status' => 'rencana']) }}" 
               class="flex-1 lg:flex-none text-center px-6 py-2.5 rounded-xl text-[10px] font-black uppercase transition-all {{ $currentStatus == 'rencana' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-500 hover:bg-slate-50' }}">
                Rencana
            </a>
            <a href="{{ route('admin.sosial.index', ['status' => 'berlangsung']) }}" 
               class="flex-1 lg:flex-none text-center px-6 py-2.5 rounded-xl text-[10px] font-black uppercase transition-all {{ $currentStatus == 'berlangsung' ? 'bg-orange-500 text-white shadow-md' : 'text-slate-500 hover:bg-slate-50' }}">
                Berlangsung
            </a>
            <a href="{{ route('admin.sosial.index', ['status' => 'selesai']) }}" 
               class="flex-1 lg:flex-none text-center px-6 py-2.5 rounded-xl text-[10px] font-black uppercase transition-all {{ $currentStatus == 'selesai' ? 'bg-green-600 text-white shadow-md' : 'text-slate-500 hover:bg-slate-50' }}">
                Selesai
            </a>
        </div>

        {{-- Dropdown Kategori --}}
        <form action="{{ route('admin.sosial.index') }}" method="GET" class="w-full lg:w-64">
            <select name="kategori" onchange="this.form.submit()" class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-3 text-[10px] font-black uppercase tracking-widest outline-none focus:ring-2 focus:ring-green-500 transition-all cursor-pointer">
                <option value="">Semua Kategori</option>
                @foreach($kategori as $kat)
                    <option value="{{ $kat->id_kategori }}" {{ request('kategori') == $kat->id_kategori ? 'selected' : '' }}>{{ $kat->nama_kategori }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- 4. MAIN TABLE SECTION --}}
    <div class="bg-white rounded-[40px] border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">Informasi Agenda</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center border-b border-slate-100">Status</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center border-b border-slate-100">Donasi</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right border-b border-slate-100">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($kegiatan as $k)
                    <tr class="hover:bg-slate-50/30 transition-all group">
                        <td class="px-8 py-6">
                            <div class="flex flex-col gap-1">
                                <span class="text-sm font-bold text-slate-800 uppercase tracking-tight">{{ $k->nama_kegiatan }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 bg-green-50 text-green-700 text-[8px] font-black uppercase rounded border border-green-100">
                                        {{ $k->kategori->nama_kategori ?? 'Umum' }}
                                    </span>
                                    <span class="text-[10px] font-medium text-slate-400 uppercase tracking-tight">
                                        {{ date('d M Y', strtotime($k->tanggal_kegiatan)) }} • {{ $k->lokasi }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-6 text-center">
                            @php
                                $statusClasses = [
                                    'rencana' => 'bg-blue-50 text-blue-600 border-blue-100',
                                    'berlangsung' => 'bg-orange-50 text-orange-600 border-orange-100 animate-pulse',
                                    'selesai' => 'bg-green-50 text-green-700 border-green-100'
                                ];
                                $currentClass = $statusClasses[$k->status_kegiatan] ?? 'bg-slate-50 text-slate-500 border-slate-100';
                            @endphp
                            <span class="px-4 py-1.5 border rounded-xl text-[9px] font-black uppercase tracking-widest {{ $currentClass }}">
                                {{ $k->status_kegiatan }}
                            </span>
                        </td>

                        {{-- PERBAIKAN KOLOM DONASI --}}
                        <td class="px-6 py-6">
                            <div class="flex flex-col gap-2 min-w-[160px]">
                                {{-- Progress Bar --}}
                                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden border border-slate-50">
                                    <div class="bg-green-600 h-full rounded-full transition-all duration-1000 shadow-[0_0_8px_rgba(22,101,52,0.2)]" 
                                        style="width: {{ $k->persentase_donasi > 100 ? 100 : $k->persentase_donasi }}%">
                                    </div>
                                </div>
                                
                                {{-- Detail Nominal & Info Saldo --}}
                                <div class="flex flex-col leading-tight">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-[10px] font-black text-slate-700">
                                            Rp {{ number_format($k->total_masuk, 0, ',', '.') }}
                                        </span>
                                        <span class="text-[9px] font-bold text-slate-300">
                                            Goal: {{ number_format($k->target_donasi, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    @php 
                                        $sisaSiapCair = $k->total_masuk - $k->total_keluar; 
                                    @endphp
                                    
                                    @if($k->total_masuk == 0)
                                        {{-- Kondisi 1: Benar-benar belum ada donasi masuk --}}
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">
                                            ⚪ Belum Ada Donasi
                                        </span>
                                    @elseif($sisaSiapCair > 0)
                                        {{-- Kondisi 2: Ada uang masuk dan masih ada sisa yang belum ditarik --}}
                                        <div class="flex items-center gap-1">
                                            <span class="w-1 h-1 rounded-full bg-orange-500 animate-ping"></span>
                                            <span class="text-[9px] font-black text-orange-500 uppercase tracking-tighter">
                                                Rp {{ number_format($sisaSiapCair, 0, ',', '.') }} Siap Cair
                                            </span>
                                        </div>
                                    @else
                                        {{-- Kondisi 3: Sudah ada donasi masuk, tapi sudah ditarik semua --}}
                                        <span class="text-[9px] font-black text-emerald-500 uppercase tracking-tighter">
                                            ✓ Saldo Terpakai Semua
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td class="px-8 py-6">
                            <div class="flex justify-end items-center gap-1">
                                
                                {{-- SLOT 1: CAIRKAN DANA (Aktif jika ada saldo DAN mulai H-7 kegiatan) --}}
                                <div class="w-10 h-10 flex items-center justify-center">
                                    @php 
                                        $sisaSiapCair = $k->total_masuk - $k->total_keluar;
                                        
                                        // Ambil objek tanggal kegiatan
                                        $tanggalKegiatan = \Carbon\Carbon::parse($k->tanggal_kegiatan);
                                        // Cek apakah hari ini sudah masuk rentang 7 hari sebelum kegiatan
                                        // (Hari ini >= Tanggal Kegiatan dikurangi 7 hari)
                                        $sudahBisaCair = \Carbon\Carbon::now()->greaterThanOrEqualTo($tanggalKegiatan->copy()->subDays(7));
                                    @endphp
                                    
                                    {{-- Syarat: Ada saldo masuk DAN sudah memasuki H-7 kegiatan --}}
                                    @if($k->total_masuk > 0 && $sisaSiapCair > 0 && $sudahBisaCair)
                                        <button onclick="openModalCair('{{ $k->id_kegiatan }}', '{{ $k->nama_kegiatan }}', '{{ $sisaSiapCair }}')" 
                                                class="p-2 text-emerald-500 hover:bg-emerald-50 rounded-full transition-all" 
                                                title="Cairkan Dana (Tersedia Rp {{ number_format($sisaSiapCair, 0, ',', '.') }})">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                    @else
                                        {{-- Tampilan Abu-abu (Disabled) dengan info spesifik di Tooltip --}}
                                        @php
                                            $pesanTooltip = "";
                                            if ($k->total_masuk == 0) {
                                                $pesanTooltip = "Belum ada donasi yang masuk";
                                            } elseif ($sisaSiapCair <= 0) {
                                                $pesanTooltip = "Semua donasi sudah dicairkan";
                                            } elseif (!$sudahBisaCair) {
                                                $pesanTooltip = "Pencairan baru dibuka pada " . $tanggalKegiatan->copy()->subDays(7)->translatedFormat('d F Y');
                                            }
                                        @endphp

                                        <span class="p-2 text-slate-200 cursor-help" title="{{ $pesanTooltip }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </span>
                                    @endif
                                </div>

                                {{-- SLOT 2: DOKUMENTASI (Disabled jika belum Selesai) --}}
                                <div class="w-10 h-10 flex items-center justify-center">
                                    @if($k->status_kegiatan == 'selesai')
                                        <button onclick="openModalDokumentasi('{{ $k->id_kegiatan }}', '{{ $k->nama_kegiatan }}')" 
                                                class="p-2 text-indigo-500 hover:bg-indigo-50 rounded-full transition-all" title="Upload Dokumentasi">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                <path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </button>
                                    @else
                                        <span class="p-2 text-slate-200 cursor-help" title="Dokumentasi tersedia setelah kegiatan selesai">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                <path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </span>
                                    @endif
                                </div>

                                {{-- SLOT 3: EDIT (Selalu Aktif) --}}
                                <div class="w-10 h-10 flex items-center justify-center">
                                    <button onclick="openModalEdit({{ json_encode($k) }})" 
                                            class="p-2 text-amber-500 hover:bg-amber-50 rounded-full transition-all" title="Edit Agenda">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                </div>

                                {{-- SLOT 4: HAPUS (Disabled/Abu jika sudah ada donasi) --}}
                                <div class="w-10 h-10 flex items-center justify-center">
                                    @if($k->total_masuk > 0)
                                        <span class="p-2 text-slate-200 cursor-help" title="Tidak dapat dihapus karena sudah ada donasi masuk">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </span>
                                    @else
                                        <button onclick="confirmDelete('{{ $k->id_kegiatan }}', '{{ $k->nama_kegiatan }}')" 
                                                class="p-2 text-rose-500 hover:bg-rose-50 rounded-full transition-all" title="Hapus Agenda">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-32 text-center font-black text-slate-300 uppercase tracking-[0.3em] text-xs">
                            Data Tidak Ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- 5. PAGINATION SECTION --}}
        {{-- FOOTER TABEL: PAGINATION --}}
        <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
            {{-- Info Data --}}
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                Menampilkan {{ $kegiatan->firstItem() ?? 0 }} - {{ $kegiatan->lastItem() ?? 0 }} 
                dari {{ $kegiatan->total() }} Data
            </div>
            
            {{-- Navigasi Halaman --}}
            <div class="custom-pagination">
                {{ $kegiatan->appends(request()->query())->links() }}
            </div>
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
    <div class="flex items-center justify-center min-h-screen px-4 py-6">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="toggleModal('modalAgenda')"></div>
        <div class="relative bg-white w-full max-w-xl p-8 rounded-[32px] shadow-2xl">
            <h2 class="text-xl font-black text-slate-800 uppercase tracking-tight mb-6">Buat Agenda Baru</h2>
            <form action="{{ route('admin.sosial.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-2 gap-4">
                @csrf
                <div class="col-span-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Judul Kegiatan</label>
                    <input type="text" name="nama_kegiatan" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-green-600 outline-none font-bold text-slate-700 text-sm">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Kategori</label>
                    <select name="id_kategori" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-green-600 outline-none font-bold text-slate-700 text-sm">
                        @foreach($kategori as $kat)
                            <option value="{{ $kat->id_kategori }}">{{ $kat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Tanggal</label>
                    <input type="date" name="tanggal_kegiatan" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-green-600 outline-none font-bold text-slate-700 text-sm">
                </div>
                <div class="col-span-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Lokasi</label>
                    <input type="text" name="lokasi" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-green-600 outline-none font-bold text-slate-700 text-sm">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Target Donasi (Rp)</label>
                    <input type="text" id="add_target_display" oninput="formatSeparator(this, 'add_target_asli')" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-green-600 outline-none font-bold text-slate-700 text-sm" placeholder="0">
                    <input type="hidden" name="target_donasi" id="add_target_asli">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Pamflet</label>
                    <input type="file" name="pamflet_kegiatan" required class="w-full text-[10px] font-bold text-slate-400 file:bg-green-50 file:border-none file:rounded-lg file:px-3 file:py-2 file:mr-3 cursor-pointer">
                </div>
                <div class="col-span-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Deskripsi</label>
                    <textarea name="deskripsi_kegiatan" rows="2" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-green-600 outline-none font-bold text-slate-700 text-sm"></textarea>
                </div>
                <div class="col-span-2 flex gap-3 mt-2">
                    <button type="button" onclick="toggleModal('modalAgenda')" class="flex-1 py-3 border border-slate-200 rounded-xl font-black text-[10px] uppercase text-slate-400 hover:bg-slate-50 transition-all">Batal</button>
                    <button type="submit" class="flex-[2] py-3 bg-[#064e3b] text-white rounded-xl font-black text-[10px] uppercase shadow-lg shadow-green-900/20 hover:bg-black transition-all">Simpan Agenda</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit Agenda --}}
<div id="modalEdit" class="fixed inset-0 z-[100] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 py-6">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="toggleModal('modalEdit')"></div>
        <div class="relative bg-white w-full max-w-xl p-8 rounded-[32px] shadow-2xl">
            <h2 class="text-xl font-black text-slate-800 uppercase tracking-tight mb-6">Update Agenda</h2>
            <form id="formEdit" method="POST" enctype="multipart/form-data" class="grid grid-cols-2 gap-4">
                @csrf @method('PUT')
                <div class="col-span-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Judul Kegiatan</label>
                    <input type="text" name="nama_kegiatan" id="edit_nama" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-green-600 outline-none font-bold text-slate-700 text-sm">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Kategori</label>
                    <select name="id_kategori" id="edit_kategori" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-green-600 outline-none font-bold text-slate-700 text-sm">
                        @foreach($kategori as $kat)
                            <option value="{{ $kat->id_kategori }}">{{ $kat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Tanggal (Biarkan jika tidak berubah)</label>
                    <input type="date" name="tanggal_kegiatan" id="edit_tanggal" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-green-600 outline-none font-bold text-slate-700 text-sm">
                </div>
                <div class="col-span-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Lokasi</label>
                    <input type="text" name="lokasi" id="edit_lokasi" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-green-600 outline-none font-bold text-slate-700 text-sm">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Target Donasi (Rp)</label>
                    <input type="text" id="edit_target_display" oninput="formatSeparator(this, 'edit_target_asli')" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-green-600 outline-none font-bold text-slate-700 text-sm">
                    <input type="hidden" name="target_donasi" id="edit_target_asli">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Ganti Pamflet</label>
                    <input type="file" name="pamflet_kegiatan" class="w-full text-[10px] font-bold text-slate-400 file:bg-green-50 file:border-none file:rounded-lg file:px-3 file:py-2 file:mr-3 cursor-pointer">
                </div>
                <div class="col-span-2 flex gap-3 mt-4">
                    <button type="button" onclick="toggleModal('modalEdit')" class="flex-1 py-3 border border-slate-200 rounded-xl font-black text-[10px] uppercase text-slate-400">Batal</button>
                    <button type="submit" class="flex-[2] py-3 bg-amber-500 text-white rounded-xl font-black text-[10px] uppercase shadow-lg shadow-amber-500/20 hover:bg-amber-600 transition-all">Simpan Perubahan</button>
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
                    
                    {{-- Input text agar bisa diformat dengan titik separator --}}
                    <div class="relative">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 font-black text-slate-400 text-xs">Rp</span>
                        <input type="text" id="input_nominal_format" oninput="formatDanCekSaldo(this)" required 
                            class="w-full pl-12 pr-5 py-3 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-green-500 outline-none font-bold text-slate-700"
                            placeholder="0">
                    </div>

                    {{-- Input hidden untuk mengirim angka murni ke database --}}
                    <input type="hidden" name="nominal" id="input_nominal_asli">
                    
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
        
        // Set Target Donasi dengan Format Separator
        const targetVal = data.target_donasi;
        document.getElementById('edit_target_asli').value = targetVal;
        document.getElementById('edit_target_display').value = new Intl.NumberFormat('id-ID').format(targetVal);
        
        document.getElementById('edit_deskripsi').value = data.deskripsi_kegiatan;
        
        toggleModal('modalEdit');
    }

    // Variabel global untuk menyimpan saldo maksimal kegiatan yang dipilih
    let saldoMaksimal = 0;

    function openModalCair(id, nama, saldo) {
        const form = document.getElementById('formCairkan');
        form.action = `/admin/sosial/cairkan/${id}`;
        
        saldoMaksimal = parseFloat(saldo);
        
        document.getElementById('cair_judul').innerText = nama;
        document.getElementById('cair_saldo_text').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(saldo);
        
        // Reset data
        document.getElementById('input_nominal_format').value = '';
        document.getElementById('input_nominal_asli').value = '';
        document.getElementById('error_pencairan').classList.add('hidden');
        
        toggleModal('modalCairkan');
    }
    function formatSeparator(el, hiddenId) {
        let val = el.value.replace(/[^0-9]/g, '');
        if (val !== "") {
            el.value = new Intl.NumberFormat('id-ID').format(val);
        } else {
            el.value = "";
        }
        document.getElementById(hiddenId).value = val;
    }

    function formatDanCekSaldo(el) {
        let val = el.value.replace(/[^0-9]/g, '');
        el.value = val !== "" ? new Intl.NumberFormat('id-ID').format(val) : "";
        
        const nominalAsli = val === "" ? 0 : parseFloat(val);
        document.getElementById('input_nominal_asli').value = nominalAsli;

        const errorPesan = document.getElementById('error_pencairan');
        const btnSubmit = document.getElementById('btn_konfirmasi_cair');

        if (nominalAsli > saldoMaksimal) {
            errorPesan.classList.remove('hidden');
            btnSubmit.disabled = true;
            btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            errorPesan.classList.add('hidden');
            btnSubmit.disabled = false;
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
    .animate-fadeIn { animation: fadeIn 0.5s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    
    /* Custom Scrollbar for Tabs */
    .overflow-x-auto::-webkit-scrollbar { height: 0px; }
    
    /* Container Navigasi */
    .custom-pagination nav {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Sembunyikan elemen bawaan Laravel yang tidak diperlukan */
    .custom-pagination nav p {
        display: none !important;
    }

    .custom-pagination flex div:first-child {
        display: none !important;
    }

    /* Styling Utama Tombol & Angka */
    .custom-pagination a, 
    .custom-pagination span[aria-current="page"] span,
    .custom-pagination span[aria-disabled="true"] span {
        display: flex !important;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 12px !important;
        font-size: 11px !important;
        font-weight: 800 !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #e2e8f0 !important;
        background: white !important;
        color: #64748b !important;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        text-decoration: none !important;
    }

    /* Status Halaman Aktif */
    .custom-pagination span[aria-current="page"] span {
        background: #064e3b !important; /* Hijau Tua Khas Masjid */
        color: white !important;
        border-color: #064e3b !important;
        box-shadow: 0 4px 12px rgba(6, 78, 59, 0.2);
    }

    /* Efek Hover untuk Link */
    .custom-pagination a:hover {
        border-color: #064e3b !important;
        color: #064e3b !important;
        background: #f0fdf4 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Styling Ikon Panah */
    .custom-pagination svg {
        width: 16px !important;
        height: 16px !important;
        stroke-width: 3;
    }
</style>
@endsection