@extends('peserta.layout.app')

@section('content')
<div class="max-w-7xl mx-auto animate-fadeInUp flex flex-col gap-8">
    
    {{-- ================= HEADER SECTION ================= --}}
    <div class="px-4 md:px-0">
        <h1 class="text-2xl font-extrabold text-green-900 tracking-tight">Kegiatan Sosial</h1>
        <p class="text-sm text-gray-400 font-medium italic mb-6">Salurkan infaq terbaik Anda untuk program kemaslahatan umat</p>
        
        <div class="flex flex-wrap gap-2 pb-4 border-b border-slate-100">
            <button onclick="filterKategori('all', this)" class="cat-filter active px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all border border-slate-200 bg-white text-slate-600">Semua</button>
            @foreach($kategori as $kat)
                <button onclick="filterKategori('{{ Str::slug($kat->nama_kategori) }}', this)" class="cat-filter px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all border border-slate-200 bg-white text-slate-600">{{ $kat->nama_kategori }}</button>
            @endforeach
        </div>
    </div>

    {{-- ================= GRID DAFTAR AGENDA ================= --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-10 px-4 md:px-0">
        @forelse($agendas as $item)
        @php
            $isSelesai = ($item->status_kegiatan == 'selesai');
            
            // Ambil list donatur untuk dikirim ke JS
            $listDonatur = \App\Models\DanaSosial::where('id_kegiatan', $item->id_kegiatan)
                        ->where('tipe_dana', 'masuk')
                        ->whereIn('status_pembayaran', ['success', 'settlement', 'sukses'])
                        ->orderBy('id_dana', 'desc')
                        ->get();

            $danaMasuk = $listDonatur->sum('nominal');
            $persentase = $item->target_donasi > 0 ? ($danaMasuk / $item->target_donasi) * 100 : 0;
            if($persentase > 100) $persentase = 100;
            $slugKategori = Str::slug($item->kategori->nama_kategori ?? 'umum');
        @endphp

        <div class="agenda-card bg-white rounded-[40px] overflow-hidden border border-slate-100 shadow-sm hover:shadow-xl transition-all flex flex-col group {{ $isSelesai ? 'border-b-4 border-b-slate-400' : 'border-b-4 border-b-[#147a54]' }}" 
             data-category="{{ $slugKategori }}">
            
            <div class="relative h-52 overflow-hidden">
                <img src="{{ $item->pamflet_kegiatan ? asset('storage/'.$item->pamflet_kegiatan) : 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=800' }}" 
                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 {{ $isSelesai ? 'grayscale' : '' }}">
                <div class="absolute top-5 left-5 flex gap-2">
                    <span class="px-3 py-1 bg-white/90 backdrop-blur-md text-[#147a54] text-[9px] font-black rounded-lg uppercase shadow-sm">{{ $item->kategori->nama_kategori ?? 'Umum' }}</span>
                    @if($isSelesai) <span class="px-3 py-1 bg-slate-800 text-white text-[9px] font-black rounded-lg uppercase">Selesai</span> @endif
                </div>
            </div>
            
            <div class="p-8 flex-1 flex flex-col">
                <h3 class="text-lg font-black text-slate-800 mb-2 line-clamp-1 uppercase tracking-tight">{{ $item->nama_kegiatan }}</h3>
                
                <div class="mb-4">
                    <p class="text-[9px] font-black text-gray-300 uppercase leading-none mb-1 text-left">Terkumpul</p>
                    <p class="text-base font-black text-[#147a54] text-left">Rp {{ number_format($danaMasuk, 0, ',', '.') }}</p>
                </div>

                <div class="mt-auto space-y-5">
                    <div>
                        <div class="flex justify-between text-[9px] font-black uppercase text-slate-400 mb-2">
                            <span>Target Rp{{ number_format($item->target_donasi, 0, ',', '.') }}</span>
                            <span>{{ round($persentase) }}%</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full {{ $isSelesai ? 'bg-slate-400' : 'bg-[#147a54]' }} rounded-full" style="width: {{ $persentase }}%"></div>
                        </div>
                    </div>

                    <div class="flex gap-2 pt-2">
                        {{-- Data listDonatur dikirim sebagai JSON ke fungsi JS --}}
                        <button onclick='loadDetail(@json($item), "{{ number_format($danaMasuk, 0, ",", ".") }}", @json($listDonatur))' 
                                class="flex-1 py-3 bg-slate-50 text-slate-600 text-[10px] font-black uppercase rounded-2xl border border-slate-100 tracking-widest hover:bg-slate-100 transition-all">
                            Detail
                        </button>
                        
                        @if($isSelesai)
                            <button onclick='loadDetail(@json($item), "{{ number_format($danaMasuk, 0, ",", ".") }}", @json($listDonatur))'
                                    class="flex-1 py-3 bg-slate-800 text-white text-[10px] font-black rounded-2xl uppercase tracking-widest">
                                Dokumentasi
                            </button>
                        @else
                            <button onclick="openDonasiModal('{{ $item->id_kegiatan }}', '{{ $item->nama_kegiatan }}')" 
                                    class="flex-1 py-3 bg-[#147a54] text-white text-[10px] font-black rounded-2xl shadow-lg shadow-green-900/20 active:scale-95 uppercase tracking-widest">
                                Donasi
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-32 bg-white rounded-[50px] border border-dashed border-slate-200 text-center">
            <p class="text-slate-400 font-black uppercase text-xs tracking-[0.2em]">Belum ada kegiatan sosial</p>
        </div>
        @endforelse
    </div>
</div>

{{-- ================= MODAL DETAIL CARD (PREMIUM & TIDAK KEPOTONG) ================= --}}
<div id="modalDetail" class="fixed inset-0 z-[110] hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md animate-fadeIn" onclick="closeDetailModal()"></div>
    
    <div class="bg-white relative w-full max-w-5xl h-[85vh] md:h-[80vh] overflow-hidden rounded-[40px] shadow-2xl animate-zoomIn flex flex-col md:flex-row">
        
        {{-- Sisi Kiri: Pamflet & Deskripsi --}}
        <div class="md:w-5/12 bg-slate-50 p-6 md:p-10 flex flex-col overflow-y-auto custom-scrollbar border-r border-slate-100">
            <div class="relative rounded-[30px] overflow-hidden shadow-lg mb-6 shrink-0 aspect-[3/4]">
                <img id="detail_img" src="" class="w-full h-full object-cover">
            </div>
            
            <div class="space-y-4 text-left">
                <span id="detail_kategori" class="px-3 py-1.5 bg-white text-[#147a54] text-[9px] font-black rounded-lg uppercase tracking-widest shadow-sm inline-block"></span>
                <h2 id="detail_nama" class="text-2xl font-black text-slate-900 uppercase tracking-tight leading-tight"></h2>
                <div class="p-6 bg-white rounded-[25px] border border-slate-100 shadow-sm">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 text-left">Deskripsi Kegiatan</p>
                    <p id="detail_deskripsi" class="text-sm text-slate-600 leading-relaxed italic whitespace-pre-line text-left"></p>
                </div>

                <div id="section_dokumentasi" class="hidden space-y-4 pt-4 text-left">
                    <h4 class="text-[10px] font-black text-slate-800 uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Dokumentasi Hasil
                    </h4>
                    <img id="detail_dok" src="" class="rounded-3xl w-full object-cover border-4 border-white shadow-md">
                </div>
            </div>
        </div>

        {{-- Sisi Kanan: List Donatur --}}
        <div class="md:w-7/12 p-6 md:p-10 flex flex-col bg-white relative h-full">
            <button onclick="closeDetailModal()" class="absolute top-6 right-6 text-slate-300 hover:text-red-500 transition-colors z-20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <div class="mb-8 text-left">
                <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Status Donasi</p>
                <div class="flex items-baseline gap-2">
                    <h3 id="detail_terkumpul" class="text-4xl font-black text-[#147a54]"></h3>
                    <span id="detail_target" class="text-sm font-bold text-slate-300 uppercase tracking-tighter"></span>
                </div>
                {{-- Persentase Dinamis di Modal --}}
                <div class="mt-4 w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div id="detail_progress_bar" class="h-full bg-[#147a54] rounded-full transition-all duration-700"></div>
                </div>
            </div>

            <div class="flex-1 flex flex-col min-h-0 text-left">
                <h4 class="text-[10px] font-black text-slate-800 uppercase tracking-widest mb-4 flex items-center gap-2 shrink-0">
                    <svg class="w-4 h-4 text-orange-400" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path></svg>
                    Daftar Dermawan
                </h4>
                
                <div id="donatur_list" class="space-y-3 overflow-y-auto pr-2 custom-scrollbar flex-1 bg-slate-50/50 rounded-[30px] p-5 border border-slate-50">
                    {{-- Diisi via JS --}}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DONASI --}}
<div id="modalDonasi" class="fixed inset-0 z-[120] hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeDonasiModal()"></div>
    <div class="bg-white relative w-full max-w-md p-10 rounded-[50px] shadow-2xl animate-zoomIn">
        <div class="text-center mb-8">
            <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Infaq Terbaik</h3>
            <p id="namaKegiatanModal" class="text-[10px] text-[#147a54] font-black uppercase tracking-widest mt-2"></p>
        </div>
        <form id="donasiForm" class="space-y-5">
            <input type="hidden" id="modal_id_kegiatan_hidden">
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 block mb-2 ml-1 text-left">Donatur</label>
                <input type="text" value="{{ Auth::user()->nama }}" readonly class="w-full px-6 py-4 rounded-2xl bg-slate-50 border border-slate-100 font-black text-slate-400 cursor-not-allowed text-sm uppercase">
            </div>
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 block mb-2 ml-1 text-left">Nominal (Rp)</label>
                <input type="number" id="modal_nominal" min="10000" placeholder="Min. 10.000" class="w-full px-6 py-4 rounded-2xl border border-slate-100 font-black text-slate-800 focus:border-[#147a54] outline-none text-lg shadow-inner" required>
            </div>
            <button type="submit" id="pay-button" class="w-full py-5 bg-[#147a54] text-white font-black rounded-3xl shadow-xl hover:bg-[#064e3b] transition-all flex items-center justify-center gap-3 active:scale-95 uppercase tracking-widest text-xs">
                Lanjutkan Pembayaran
            </button>
        </form>
    </div>
</div>

<script>
    function filterKategori(slug, btn) {
        document.querySelectorAll('.cat-filter').forEach(b => {
            b.classList.remove('bg-[#147a54]', 'text-white', 'active');
            b.classList.add('bg-white', 'text-slate-600');
        });
        btn.classList.remove('bg-white', 'text-slate-600');
        btn.classList.add('bg-[#147a54]', 'text-white', 'active');

        document.querySelectorAll('.agenda-card').forEach(card => {
            card.style.display = (slug === 'all' || card.getAttribute('data-category') === slug) ? 'flex' : 'none';
        });
    }

    // LOAD DETAIL FUNGSI BARU
    function loadDetail(item, terkumpulFormatted, donaturArray) {
        const modal = document.getElementById('modalDetail');
        document.getElementById('detail_img').src = "{{ asset('storage') }}/" + item.pamflet_kegiatan;
        document.getElementById('detail_kategori').innerText = item.kategori ? item.kategori.nama_kategori : 'UMUM';
        document.getElementById('detail_nama').innerText = item.nama_kegiatan;
        document.getElementById('detail_terkumpul').innerText = "Rp " + terkumpulFormatted;
        document.getElementById('detail_target').innerText = "/ Rp " + new Intl.NumberFormat('id-ID').format(item.target_donasi);
        document.getElementById('detail_deskripsi').innerText = item.deskripsi_kegiatan;

        // Progress Bar Modal
        const rawTerkumpul = donaturArray.reduce((acc, obj) => acc + parseInt(obj.nominal), 0);
        const modalPersen = item.target_donasi > 0 ? (rawTerkumpul / item.target_donasi) * 100 : 0;
        document.getElementById('detail_progress_bar').style.width = (modalPersen > 100 ? 100 : modalPersen) + "%";

        // Cek Dokumentasi
        const sectionDok = document.getElementById('section_dokumentasi');
        if (item.status_kegiatan === 'selesai' && item.dokumentasi_kegiatan) {
            sectionDok.classList.remove('hidden');
            document.getElementById('detail_dok').src = "{{ asset('storage') }}/" + item.dokumentasi_kegiatan;
        } else {
            sectionDok.classList.add('hidden');
        }

        // GENERATE LIST DONATUR
        const listContainer = document.getElementById('donatur_list');
        listContainer.innerHTML = '';
        
        if(donaturArray.length > 0) {
            donaturArray.forEach(d => {
                const row = document.createElement('div');
                row.className = "p-4 bg-white rounded-2xl border border-slate-100 flex justify-between items-center shadow-sm mb-1";
                row.innerHTML = `
                    <div class="flex items-center gap-3 text-left">
                        <div class="w-8 h-8 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center text-[10px] font-bold uppercase">
                            ${d.nama_donatur.charAt(0)}
                        </div>
                        <div>
                            <p class="text-xs font-black text-slate-800 uppercase">${d.nama_donatur}</p>
                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter">Donatur Dermawan</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-black text-[#147a54]">Rp ${new Intl.NumberFormat('id-ID').format(d.nominal)}</p>
                    </div>
                `;
                listContainer.appendChild(row);
            });
        } else {
            listContainer.innerHTML = '<div class="py-16 text-center"><p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Belum ada donatur</p></div>';
        }

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDetailModal() { document.getElementById('modalDetail').classList.add('hidden'); document.body.style.overflow = 'auto'; }
    function openDonasiModal(id, nama) {
        document.getElementById('modal_id_kegiatan_hidden').value = id;
        document.getElementById('namaKegiatanModal').innerText = nama;
        document.getElementById('modalDonasi').classList.remove('hidden');
    }
    function closeDonasiModal() { document.getElementById('modalDonasi').classList.add('hidden'); }

    // Midtrans
    document.getElementById('donasiForm').onsubmit = function(e) {
        e.preventDefault();
        const btn = document.getElementById('pay-button');
        btn.disabled = true;
        btn.innerText = "Memproses...";

        fetch("{{ route('donasi.checkout') }}", {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" },
            body: JSON.stringify({
                id_kegiatan: document.getElementById('modal_id_kegiatan_hidden').value,
                nama_donatur: "{{ Auth::user()->nama }}", 
                nominal: document.getElementById('modal_nominal').value
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.token) {
                window.snap.pay(data.token, {
                    onSuccess: function() { window.location.reload(); },
                    onClose: function() { btn.disabled = false; btn.innerText = "Lanjutkan Pembayaran"; }
                });
            }
        });
    };
</script>

<style>
    .cat-filter.active { background-color: #147a54 !important; color: white !important; border-color: #147a54 !important; box-shadow: 0 10px 15px -3px rgba(20, 122, 84, 0.2); }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    @keyframes zoomIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    .animate-zoomIn { animation: zoomIn 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
</style>
@endsection