@extends('admin.layout.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="max-w-7xl mx-auto animate-fadeInUp flex flex-col gap-8">
    
    {{-- ================= HEADER SECTION ================= --}}
    <div class="flex justify-between items-end shrink-0">
        <div>
            <h1 class="text-2xl font-bold text-green-900 leading-tight">Sistem Undian</h1>
            <p class="text-sm text-gray-400 font-medium italic">Manajemen otomatis urutan pemenang qurban Masjid Nurul Huda</p>
        </div>
        <button onclick="document.getElementById('modalUndian').classList.remove('hidden')" 
            class="px-8 py-4 bg-[#b38b2d] hover:bg-[#967526] text-white rounded-[20px] shadow-lg shadow-yellow-900/20 transition-all flex items-center gap-3 active:scale-95 group">
            <svg class="w-5 h-5 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <span class="text-xs font-black uppercase tracking-[0.1em]">Lakukan Undian Baru</span>
        </button>
    </div>

    {{-- ================= STATS SECTION ================= --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 shrink-0">
        <div class="bg-white rounded-[32px] p-6 shadow-sm border-l-8 border-green-600 flex items-center gap-5 transition-all hover:shadow-md">
            <div class="w-14 h-14 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center shadow-inner">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Total Anggota</p>
                <p class="text-2xl font-black text-slate-800">{{ $totalPeserta }} <span class="text-xs font-bold text-gray-300 italic">Peserta</span></p>
            </div>
        </div>

        <div class="bg-white rounded-[32px] p-6 shadow-sm border-l-8 border-orange-500 flex items-center gap-5 transition-all hover:shadow-md">
            <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center shadow-inner">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Belum Menang</p>
                <p class="text-2xl font-black text-slate-800">{{ $menunggu }} <span class="text-xs font-bold text-gray-300 italic">Antrean</span></p>
            </div>
        </div>

        <div class="bg-white rounded-[32px] p-6 shadow-sm border-l-8 border-[#147a54] flex items-center gap-5 transition-all hover:shadow-md">
            <div class="w-14 h-14 bg-green-50 text-[#147a54] rounded-2xl flex items-center justify-center shadow-inner">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Sudah Menang</p>
                <p class="text-2xl font-black text-slate-800">{{ $selesai }} <span class="text-xs font-bold text-gray-300 italic">Total</span></p>
            </div>
        </div>
    </div>

    {{-- ================= TAB NAVIGATION ================= --}}
    <div class="flex gap-4 border-b border-gray-100 shrink-0">
        <button onclick="switchTab('pemenang')" id="btn-pemenang" class="pb-4 px-6 text-xs font-black uppercase tracking-widest border-b-4 border-green-700 text-green-700 transition-all">Daftar Pemenang</button>
        <button onclick="switchTab('antrean')" id="btn-antrean" class="pb-4 px-6 text-xs font-black uppercase tracking-widest border-b-4 border-transparent text-gray-400 hover:text-slate-600 transition-all">Belum Menang (Antrean)</button>
    </div>

    {{-- ================= TAB PEMENANG (GROUPED BY YEAR & CATEGORY) ================= --}}
    <div id="tab-pemenang" class="space-y-12 mb-10">
        @forelse($undians->groupBy('tahun_pelaksanaan') as $tahun => $dataTahun)
            <div class="animate-fadeIn">
                {{-- YEAR SEPARATOR --}}
                <div class="flex items-center gap-4 mb-6 ml-4">
                    <span class="px-5 py-2 bg-[#147a54] text-white text-[12px] font-black rounded-2xl uppercase tracking-[0.2em] shadow-lg shadow-green-900/20">
                        Periode Qurban Tahun {{ $tahun }}
                    </span>
                    <div class="h-[2px] flex-1 bg-gradient-to-r from-green-100 to-transparent"></div>
                </div>

                <div class="space-y-6">
                    @php
                        // Pisahkan data pemenang berdasarkan kategori
                        $pemenangKelompok = $dataTahun->filter(fn($item) => $item->peserta->id_kelompok != null);
                        $pemenangIndividu = $dataTahun->filter(fn($item) => $item->peserta->id_kelompok == null);
                    @endphp

                    {{-- --- KATEGORI KELOMPOK --- --}}
                    @if($pemenangKelompok->count() > 0)
                        <div class="bg-white rounded-[32px] shadow-sm border border-blue-100 overflow-hidden">
                            <div class="px-8 py-4 bg-blue-50/50 border-b border-blue-100 flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-blue-600 text-white rounded-lg flex items-center justify-center shadow-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xs font-black text-blue-900 uppercase tracking-widest">Kategori Kelompok</h3>
                                        <p class="text-[9px] text-blue-400 font-bold uppercase tracking-tighter">Total {{ $pemenangKelompok->count() }} Realisasi</p>
                                    </div>
                                </div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead class="bg-white text-[9px] uppercase tracking-[0.2em] text-gray-400 font-black border-b border-gray-50">
                                        <tr>
                                            <th class="px-8 py-4">Urutan</th>
                                            <th class="px-6 py-4">Identitas Pemenang</th>
                                            <th class="px-6 py-4 text-center">Detail Kelompok</th>
                                            <th class="px-6 py-4 text-center">Skema</th>
                                            <th class="px-8 py-4 text-right">Waktu Undi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @foreach($pemenangKelompok as $item)
                                            <tr class="hover:bg-blue-50/30 transition-colors group">
                                                <td class="px-8 py-4">
                                                    <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-black group-hover:bg-blue-600 group-hover:text-white transition-all shadow-sm">
                                                        {{ sprintf("%02d", $item->urutan_pemenang) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <p class="font-bold text-gray-800 text-xs uppercase">{{ $item->peserta->nama }}</p>
                                                    <p class="text-[9px] text-gray-400 font-bold tracking-tighter italic">{{ $item->peserta->alamat }}</p>
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-[9px] font-black rounded-full border border-blue-200 uppercase italic">
                                                        Grup: {{ $item->peserta->kelompok->kode_kelompok }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    <span class="px-3 py-1 bg-green-50 text-green-700 text-[9px] font-black rounded-full border border-green-100 uppercase">
                                                        {{ $item->skema->nama_skema }}
                                                    </span>
                                                </td>
                                                <td class="px-8 py-4 text-right">
                                                    <p class="text-[10px] font-bold text-slate-600 leading-none">{{ \Carbon\Carbon::parse($item->tanggal_undian)->translatedFormat('d M Y') }}</p>
                                                    <p class="text-[8px] text-gray-400 font-medium italic mt-1">{{ \Carbon\Carbon::parse($item->tanggal_undian)->format('H:i') }} WIB</p>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    {{-- --- KATEGORI PERORANGAN --- --}}
                    @if($pemenangIndividu->count() > 0)
                        <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-8 py-4 bg-gray-50/50 border-b border-gray-100 flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-slate-400 text-white rounded-lg flex items-center justify-center shadow-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xs font-black text-slate-700 uppercase tracking-widest">Kategori Perorangan</h3>
                                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter">Total {{ $pemenangIndividu->count() }} Realisasi</p>
                                    </div>
                                </div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead class="bg-white text-[9px] uppercase tracking-[0.2em] text-gray-400 font-black border-b border-gray-50">
                                        <tr>
                                            <th class="px-8 py-4">Urutan</th>
                                            <th class="px-6 py-4">Identitas Pemenang</th>
                                            <th class="px-6 py-4 text-center">Jenis</th>
                                            <th class="px-6 py-4 text-center">Skema</th>
                                            <th class="px-8 py-4 text-right">Waktu Undi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @foreach($pemenangIndividu as $item)
                                            <tr class="hover:bg-green-50/20 transition-colors group">
                                                <td class="px-8 py-4">
                                                    <span class="w-8 h-8 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center text-xs font-black group-hover:bg-[#147a54] group-hover:text-white transition-all shadow-sm">
                                                        {{ sprintf("%02d", $item->urutan_pemenang) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <p class="font-bold text-gray-800 text-xs uppercase">{{ $item->peserta->nama }}</p>
                                                    <p class="text-[9px] text-gray-400 font-bold tracking-tighter italic">{{ $item->peserta->alamat }}</p>
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">Individu</span>
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    <span class="px-3 py-1 bg-green-50 text-green-700 text-[9px] font-black rounded-full border border-green-100 uppercase">
                                                        {{ $item->skema->nama_skema }}
                                                    </span>
                                                </td>
                                                <td class="px-8 py-4 text-right">
                                                    <p class="text-[10px] font-bold text-slate-600 leading-none">{{ \Carbon\Carbon::parse($item->tanggal_undian)->translatedFormat('d M Y') }}</p>
                                                    <p class="text-[8px] text-gray-400 font-medium italic mt-1">{{ \Carbon\Carbon::parse($item->tanggal_undian)->format('H:i') }} WIB</p>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="py-24 bg-white rounded-[40px] border border-dashed border-gray-200 text-center">
                <p class="text-gray-400 font-black uppercase text-xs tracking-widest">Belum ada riwayat pengundian</p>
            </div>
        @endforelse
    </div>

    {{-- ================= TAB ANTREAN (GROUPED BY KELOMPOK) ================= --}}
    <div id="tab-antrean" class="hidden space-y-8 mb-10 animate-fadeIn">
        @php
            $sudahMenangIds = $undians->pluck('id_pesertaarisan')->toArray();
            $antreanGrouped = \App\Models\PesertaArisan::with(['kelompok', 'skemaArisan'])
                        ->whereHas('user', function($q){ $q->where('status', 'aktif'); })
                        ->whereNotIn('id_pesertaarisan', $sudahMenangIds)
                        ->get()
                        ->groupBy(function($item) {
                            return $item->id_kelompok ? 'kelompok-' . $item->id_kelompok : 'perorangan';
                        });
        @endphp

        @forelse($antreanGrouped as $groupKey => $members)
            <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-5 bg-gray-50/50 border-b border-gray-100 flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 {{ str_contains($groupKey, 'kelompok') ? 'bg-blue-600' : 'bg-slate-400' }} text-white rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-tighter">
                                {{ str_contains($groupKey, 'kelompok') ? 'Kelompok: ' . $members->first()->kelompok->kode_kelompok : 'Peserta Perorangan' }}
                            </h3>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Antrean: {{ $members->count() }} Anggota Aktif</p>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-white text-[9px] uppercase tracking-[0.2em] text-gray-400 font-black border-b border-gray-50">
                            <tr>
                                <th class="px-8 py-4">Nama Peserta</th>
                                <th class="px-6 py-4">Kontak & Alamat</th>
                                <th class="px-6 py-4 text-center">Skema Terpilih</th>
                                <th class="px-8 py-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($members as $a)
                            <tr class="hover:bg-orange-50/20 transition-all">
                                <td class="px-8 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 bg-slate-100 text-slate-400 rounded-xl flex items-center justify-center text-[10px] font-black uppercase">
                                            {{ substr($a->nama, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800 text-xs uppercase">{{ $a->nama }}</p>
                                            <p class="text-[9px] text-gray-400 font-bold tracking-widest uppercase italic">Pendaftar #{{ $a->id_pesertaarisan }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-[10px] font-bold text-gray-600 mb-0.5">{{ $a->no_hp }}</p>
                                    <p class="text-[9px] text-gray-400 italic line-clamp-1">{{ $a->alamat }}</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-[10px] font-black text-[#147a54] uppercase tracking-tighter bg-green-50 px-3 py-1 rounded-full border border-green-100">
                                        {{ $a->skemaArisan->nama_skema ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <span class="px-3 py-1 bg-orange-100 text-orange-600 text-[9px] font-black rounded-full uppercase tracking-tighter border border-orange-200">Antrean</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="py-24 bg-white rounded-[40px] border border-dashed border-gray-200 text-center">
                <p class="text-gray-400 font-black uppercase text-xs tracking-widest">Semua peserta aktif sudah mendapatkan undian</p>
            </div>
        @endforelse
    </div>
</div>

{{-- ================= MODAL PILIH SKEMA ================= --}}
<div id="modalUndian" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
    <div class="bg-white rounded-[40px] p-10 w-full max-w-md shadow-2xl animate-zoomIn relative">
        <button onclick="document.getElementById('modalUndian').classList.add('hidden')" class="absolute top-6 right-6 text-gray-300 hover:text-red-500 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-yellow-50 text-yellow-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <h3 class="text-2xl font-black text-gray-800 uppercase tracking-tight">Mulai Undian</h3>
            <p class="text-[11px] text-gray-400 font-bold uppercase tracking-widest mt-1">Sistem akan mengacak pemenang untuk periode {{ date('Y') }}</p>
        </div>
        
        <form id="formProsesUndian" action="{{ route('admin.undian.proses') }}" method="POST">
            @csrf
            <div class="space-y-4 mb-8">
                <label class="text-[10px] font-black text-gray-400 uppercase ml-1 tracking-[0.2em]">Pilih Paket Arisan</label>
                <div class="relative">
                    <select name="id_skema" id="id_skema" required class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-black text-gray-700 outline-none focus:ring-4 focus:ring-green-500/10 focus:border-green-600 appearance-none cursor-pointer">
                        <option value="" disabled selected>-- DAFTAR PAKET AKTIF --</option>
                        @foreach($skemas as $s)
                            <option value="{{ $s->id_skema }}">{{ strtoupper($s->nama_skema) }} ({{ $s->durasi_bulan }} BULAN)</option>
                        @endforeach
                    </select>
                    <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>
            
            <button type="button" onclick="triggerUndianAnimation()" 
                class="w-full bg-[#147a54] hover:bg-[#064e3b] text-white py-5 rounded-[20px] font-black text-[11px] uppercase tracking-[0.2em] shadow-xl shadow-green-900/20 transition-all active:scale-95">
                Acak Pemenang Baru
            </button>
        </form>
    </div>
</div>

<script>
    function switchTab(tab) {
        const tabPemenang = document.getElementById('tab-pemenang');
        const tabAntrean = document.getElementById('tab-antrean');
        const btnPemenang = document.getElementById('btn-pemenang');
        const btnAntrean = document.getElementById('btn-antrean');

        if (tab === 'pemenang') {
            tabPemenang.classList.remove('hidden');
            tabAntrean.classList.add('hidden');
            btnPemenang.classList.add('border-green-700', 'text-green-700');
            btnPemenang.classList.remove('border-transparent', 'text-gray-400');
            btnAntrean.classList.remove('border-green-700', 'text-green-700');
            btnAntrean.classList.add('border-transparent', 'text-gray-400');
        } else {
            tabPemenang.classList.add('hidden');
            tabAntrean.classList.remove('hidden');
            btnAntrean.classList.add('border-green-700', 'text-green-700');
            btnAntrean.classList.remove('border-transparent', 'text-gray-400');
            btnPemenang.classList.remove('border-green-700', 'text-green-700');
            btnPemenang.classList.add('border-transparent', 'text-gray-400');
        }
    }

    function triggerUndianAnimation() {
        const skemaId = document.getElementById('id_skema').value;
        if(!skemaId) {
            Swal.fire({ icon: 'error', title: 'Pilih Skema!', text: 'Silakan pilih skema qurban terlebih dahulu.' });
            return;
        }

        Swal.fire({
            title: 'Sedang Mengundi...',
            html: 'Sistem sedang memilih pemenang secara acak.',
            timer: 2000,
            timerProgressBar: true,
            didOpen: () => { Swal.showLoading(); },
            willClose: () => { document.getElementById('formProsesUndian').submit(); },
            customClass: { popup: 'rounded-[32px] p-10', title: 'text-2xl font-black uppercase text-slate-800' }
        });
    }
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .animate-fadeIn { animation: fadeIn 0.4s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-zoomIn { animation: zoomIn 0.3s ease-out; }
    @keyframes zoomIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
</style>
{{-- Letakkan di bawah @section('content') --}}
@if(session('error_pembayaran'))
<script>
    Swal.fire({
        icon: 'warning',
        title: '{{ session("error_pembayaran")["title"] }}',
        html: '<div class="text-left"><p class="mb-2">{{ session("error_pembayaran")["message"] }}</p>' +
              '<p class="text-xs text-red-500 font-bold">Peserta belum lunas: </p>' +
              '<p class="text-xs italic text-gray-500">{{ session("error_pembayaran")["detail"] }}</p></div>',
        confirmButtonColor: '#147a54',
        customClass: {
            popup: 'rounded-[32px]'
        }
    });
</script>
@endif

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session("success") }}',
        timer: 3000,
        showConfirmButton: false,
        customClass: { popup: 'rounded-[32px]' }
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: '{{ session("error") }}',
        customClass: { popup: 'rounded-[32px]' }
    });
</script>
@endif
@endsection