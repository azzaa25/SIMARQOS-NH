<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $item->nama_kegiatan }} - Masjid Nurul Huda</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script type="text/javascript"
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.clientKey') }}">
    </script>
    <style>
        body{
            font-family:'Plus Jakarta Sans',sans-serif;
            background:#f8fafc;
            color:#1e293b;
            overflow-x:hidden;
        }
        .glass-nav{
            background:rgba(255,255,255,.7);
            backdrop-filter:blur(15px);
            border-bottom:1px solid rgba(255,255,255,.3);
        }
        .glass-card{
            background:rgba(255,255,255,.8);
            backdrop-filter:blur(10px);
            border:1px solid rgba(255,255,255,.5);
        }
        .blob{
            position:absolute;
            z-index:-1;
            filter:blur(80px);
            opacity:.4;
        }
        .custom-scrollbar::-webkit-scrollbar{
            width:5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track{
            background:#f1f1f1;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb{
            background:#147a54;
            border-radius:10px;
        }
        @keyframes pulse-slow{
            0%,100%{opacity:.4;}
            50%{opacity:.2;}
        }
        .animate-pulse-slow{
            animation:pulse-slow 8s ease-in-out infinite;
        }
        .quick-btn{
            padding:10px 18px;
            border-radius:16px;
            background:#f1f5f9;
            font-size:11px;
            font-weight:800;
            color:#147a54;
            transition:.3s;
            text-transform:uppercase;
            letter-spacing:.1em;
        }
        .quick-btn:hover{
            background:#147a54;
            color:white;
        }
    </style>
</head>

<body class="antialiased">
    <div class="blob w-[600px] h-[600px] bg-green-200 -top-20 -left-20 animate-pulse-slow"></div>
    <div class="blob w-[400px] h-[400px] bg-blue-100 bottom-0 right-0 opacity-30"></div>
    {{-- NAVBAR --}}
    <nav class="glass-nav sticky top-0 z-50 px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <a href="{{ route('welcome') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 bg-[#147a54] rounded-xl flex items-center justify-center shadow-lg shadow-green-900/20">
                    <svg viewBox="0 0 24 24" class="w-6 h-6 text-white fill-current">
                        <path d="M12 3a9 9 0 109 9c0-.46-.04-.92-.1-1.36a5.38 5.38 0 01-4.4 2.26 5.4 5.4 0 01-5.4-5.4c0-1.8.88-3.4 2.24-4.4-.44-.06-.9-.1-1.34-.1z"/>
                    </svg>
                </div>
                <div>
                    <span class="block text-sm font-black text-gray-900 leading-none">
                        MASJID NURUL HUDA
                    </span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        Digital Platform
                    </span>
                </div>
            </a>
            <div class="hidden md:flex items-center gap-8 text-xs font-bold uppercase tracking-widest text-gray-500">
                <a href="{{ route('welcome') }}#hero" class="hover:text-[#147a54] transition-colors">
                    Beranda
                </a>
                <a href="{{ route('welcome') }}#fitur" class="hover:text-[#147a54] transition-colors">
                    Fitur
                </a>
                <a href="{{ route('welcome') }}#sosial" class="text-[#147a54] transition-colors">
                    Kegiatan Sosial
                </a>
                <a href="{{ route('welcome') }}#masjid" class="hover:text-[#147a54] transition-colors">
                    Informasi Masjid
                </a>
                <a href="{{ route('login') }}"
                   class="px-6 py-3 bg-[#147a54] text-white rounded-full shadow-lg shadow-green-900/20 hover:bg-[#0d5c3f] transition-all">
                    Masuk
                </a>
            </div>
        </div>
    </nav>
    {{-- CONTENT --}}
    <main class="pt-16 pb-24 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-12 gap-12 items-start">
                {{-- LEFT --}}
                <div class="md:col-span-4 sticky top-28">

                    <div class="glass-card p-3 rounded-[45px] shadow-2xl border-4 border-white">
                        <div class="relative rounded-[35px] overflow-hidden bg-slate-200 shadow-inner"
                             style="aspect-ratio:2/3;">

                            <img src="{{ asset('storage/'.$item->pamflet_kegiatan) }}"
                                 alt="{{ $item->nama_kegiatan }}"
                                 class="w-full h-full object-cover">

                            <div class="absolute top-6 left-6">
                                <span class="px-4 py-2 bg-white/95 backdrop-blur-md text-[#147a54] text-[10px] font-black rounded-full uppercase tracking-[0.2em] shadow-xl">
                                    {{ $item->kategori->nama_kategori ?? 'Umum' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    {{-- PROGRESS --}}
                    <div class="mt-8 p-8 bg-white rounded-[35px] border border-slate-100 shadow-sm">
                        @php
                            $danaMasuk = \App\Models\DanaSosial::where('id_kegiatan', $item->id_kegiatan)
                                        ->where('tipe_dana', 'masuk')
                                        ->whereIn('status_pembayaran', ['success', 'settlement'])
                                        ->sum('nominal');

                            $persentase = $item->target_donasi > 0
                                            ? ($danaMasuk / $item->target_donasi) * 100
                                            : 0;

                            if($persentase > 100){
                                $persentase = 100;
                            }
                        @endphp
                        <div class="flex justify-between items-end mb-3">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Progress Dana
                            </span>
                            <span class="text-lg font-black text-[#147a54]">
                                {{ round($persentase) }}%
                            </span>
                        </div>
                        <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-[#147a54] rounded-full transition-all duration-1000"
                                 style="width:{{ $persentase }}%">
                            </div>
                        </div>
                        <p class="mt-4 text-[11px] font-bold text-slate-400 text-center uppercase tracking-widest">
                            Terkumpul :
                            <span class="text-slate-900 font-black">
                                Rp {{ number_format($danaMasuk,0,',','.') }}
                            </span>
                        </p>
                    </div>
                </div>
                {{-- RIGHT --}}
                <div class="md:col-span-8 space-y-10">
                    {{-- ================= DETAIL KEGIATAN ================= --}}
                    <div class="bg-white p-10 md:p-14 rounded-[50px] border border-slate-100 shadow-sm relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-green-50 rounded-bl-[100px] -z-0 opacity-50"></div>
                        <div class="relative z-10 space-y-8">
                            {{-- STATUS --}}
                            <div class="inline-flex items-center gap-2 px-4 py-1.5 {{ $item->status_kegiatan == 'selesai' ? 'bg-gray-100 text-gray-500' : 'bg-green-100 text-[#147a54]' }} rounded-full text-[10px] font-black uppercase tracking-[0.2em]">                                
                                <span class="relative flex h-2 w-2">
                                    @if($item->status_kegiatan != 'selesai')
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-600"></span>
                                    @else
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-gray-400"></span>
                                    @endif
                                </span>
                                {{ $item->status_kegiatan == 'selesai' ? 'Agenda Telah Selesai' : 'Agenda Sosial Masjid' }}
                            </div>
                            {{-- JUDUL --}}
                            <h1 class="text-4xl md:text-6xl font-black text-slate-900 leading-[1.1] tracking-tight">
                                {{ $item->nama_kegiatan }}
                            </h1>
                            {{-- ================= JIKA MASIH AKTIF ================= --}}
                            @if($item->status_kegiatan != 'selesai')
                                <div class="flex flex-wrap gap-8 py-6 border-y border-slate-100">
                                    <div>
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">
                                            Target Donasi
                                        </p>
                                        <p class="text-3xl font-black text-slate-900">
                                            Rp {{ number_format($item->target_donasi, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <div class="h-12 w-[1px] bg-slate-100 hidden md:block"></div>
                                    <div>
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">
                                            Status
                                        </p>
                                        <p class="text-sm font-black text-green-600 uppercase tracking-wider bg-green-50 px-3 py-1 rounded-lg inline-block">
                                            Aktif / Terbuka
                                        </p>
                                    </div>
                                </div>
                                {{-- DESKRIPSI --}}
                                <div class="prose prose-slate max-w-none">
                                    <p class="text-slate-600 text-lg leading-relaxed whitespace-pre-line font-medium">
                                        {{ $item->deskripsi_kegiatan }}
                                    </p>
                                </div>
                                {{-- BUTTON DONASI --}}
                                <button onclick="openDonasiModal('{{ $item->id_kegiatan }}', '{{ $item->nama_kegiatan }}')" 
                                        class="w-full md:w-auto px-12 py-5 bg-[#147a54] text-white font-black rounded-2xl shadow-xl shadow-green-900/20 hover:bg-[#0d5c3f] transition-all hover:-translate-y-1 text-sm uppercase tracking-widest">
                                    Salurkan Infaq Sekarang
                                </button>
                            {{-- ================= JIKA SUDAH SELESAI ================= --}}
                            @else
                                <div class="space-y-8">
                                    {{-- ALERT --}}
                                    <div class="p-6 bg-blue-50 rounded-3xl border border-blue-100">
                                        <p class="text-blue-700 font-bold text-sm">
                                            Alhamdulillah! Kegiatan ini telah terlaksana.
                                            Terima kasih kepada seluruh donatur atas kontribusi terbaiknya.
                                        </p>
                                    </div>
                                    {{-- DOKUMENTASI --}}
                                    <div id="dokumentasi">
                                        <h3 class="text-xl font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-3">
                                            <svg class="w-6 h-6 text-[#147a54]"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            Dokumentasi Kegiatan
                                        </h3>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            @if($item->dokumentasi && count($item->dokumentasi) > 0)
                                                @foreach($item->dokumentasi as $foto)
                                                    <div class="rounded-3xl overflow-hidden shadow-md h-64 border-4 border-white group">
                                                        <img src="{{ asset('storage/' . $foto) }}"
                                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="col-span-full py-12 text-center bg-slate-50 rounded-[35px] border-2 border-dashed border-slate-200">
                                                    <p class="text-slate-400 font-bold italic text-sm">
                                                        Foto dokumentasi akan segera diunggah oleh admin.
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    {{-- ================= LIST DONATUR ================= --}}
                    <div class="glass-card p-10 rounded-[50px] border border-white/50 shadow-xl border-l-8 border-l-[#147a54]">
                        {{-- HEADER --}}
                        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-8 gap-6">
                            <div>
                                <h3 class="text-2xl font-black text-slate-900 tracking-tight">
                                    Donasi Masuk
                                </h3>
                                <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-widest italic">
                                    Semoga menjadi amal jariyah
                                </p>
                            </div>
                            <div class="w-12 h-12 bg-green-50 text-[#147a54] rounded-2xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                                </svg>
                            </div>
                        </div>

                        {{-- LIST --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                            @forelse($donatur as $d)
                                <div class="p-4 bg-white/70 rounded-[24px] border border-slate-100 hover:border-green-200 transition-all flex justify-between items-center group">
                                    <div class="flex items-center gap-3">
                                        {{-- ICON --}}
                                        <div class="w-8 h-8 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 group-hover:bg-[#147a54] group-hover:text-white transition-colors">
                                            <svg class="w-4 h-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24">

                                                <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                                    stroke-width="2"
                                                    stroke-linecap="round"/>
                                            </svg>
                                        </div>
                                        {{-- INFO --}}
                                        <div>
                                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                                                Donasi Masuk
                                            </p>

                                            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-1">
                                                {{ \Carbon\Carbon::parse($d->tanggal_input)->translatedFormat('d M Y') }}
                                            </p>
                                        </div>
                                    </div>
                                    {{-- NOMINAL --}}
                                    <div class="text-right">
                                        <p class="text-base font-black text-[#147a54]">
                                            Rp{{ number_format($d->nominal, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full text-center py-20 bg-slate-50/50 rounded-[40px] border-2 border-dashed border-slate-200">
                                    <p class="text-slate-400 font-bold italic tracking-widest text-sm">
                                        Belum ada donasi masuk.
                                    </p>
                                </div>
                            @endforelse
                        </div>
                        {{-- BACK --}}
                        <div class="mt-10 text-center">
                            <a href="{{ route('welcome') }}"
                            class="inline-flex items-center gap-3 text-[10px] font-black text-slate-400 hover:text-[#147a54] transition-all uppercase tracking-[0.3em]">
                                <svg class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path d="M10 19l-7-7m0 0l7-7m-7 7h18"
                                        stroke-width="3"
                                        stroke-linecap="round"/>
                                </svg>
                                Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    {{-- MODAL --}}
    <div id="modalDonasi" class="fixed inset-0 z-[60] hidden items-center justify-center p-4">
        {{-- Overlay --}}
        <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-md"
             onclick="closeDonasiModal()"></div>
        {{-- Modal --}}
        <div class="relative w-full max-w-md bg-white rounded-[40px] shadow-2xl overflow-hidden">
            {{-- Header --}}
            <div class="relative px-8 pt-8 pb-6 bg-gradient-to-br from-[#147a54] to-[#0d5c3f] text-white">
                <button onclick="closeDonasiModal()"
                        class="absolute top-5 right-5 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <p class="text-[10px] uppercase tracking-[0.3em] font-black text-green-100 mb-3">
                    Infaq & Donasi
                </p>
                <h3 class="text-3xl font-black leading-tight">
                    Salurkan Kebaikan
                </h3>
                <p id="namaKegiatanModal"
                   class="mt-3 text-sm text-green-100 font-semibold">
                </p>
            </div>
            {{-- Body --}}
            <div class="p-8">
                <form id="donasiForm" class="space-y-6">
                    <input type="hidden" id="modal_id_kegiatan_hidden">
                    {{-- Nama --}}
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 block mb-3 ml-1">
                            Nama Donatur
                        </label>
                        <input type="text"
                               id="modal_nama"
                               placeholder="Hamba Allah"
                               class="w-full h-14 px-5 rounded-2xl border border-slate-200 bg-slate-50 focus:ring-4 focus:ring-green-500/10 focus:border-[#147a54] outline-none font-bold text-sm">
                    </div>
                    {{-- Nominal --}}
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 block mb-3 ml-1">
                            Nominal Donasi
                        </label>
                        <div class="relative">
                            <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 font-black text-sm">
                                Rp
                            </span>
                            <input type="text"
                                   id="modal_nominal"
                                   placeholder="50.000"
                                   class="w-full h-16 pl-14 pr-5 rounded-2xl border-2 border-slate-100 bg-slate-50 text-2xl font-black tracking-tight focus:ring-4 focus:ring-green-500/10 focus:border-[#147a54] outline-none"
                                   required>
                        </div>
                        <div class="flex gap-2 mt-4 flex-wrap">
                            <button type="button" onclick="setNominal(50000)" class="quick-btn">
                                50rb
                            </button>
                            <button type="button" onclick="setNominal(100000)" class="quick-btn">
                                100rb
                            </button>
                            <button type="button" onclick="setNominal(250000)" class="quick-btn">
                                250rb
                            </button>
                            <button type="button" onclick="setNominal(500000)" class="quick-btn">
                                500rb
                            </button>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-3 ml-1 italic">
                            Minimal donasi Rp 10.000
                        </p>
                    </div>
                    {{-- BUTTON --}}
                    <div class="pt-2">
                        <button type="submit"
                                id="pay-button"
                                class="w-full h-16 bg-[#147a54] hover:bg-[#0d5c3f] text-white rounded-2xl font-black uppercase tracking-[0.2em] shadow-xl shadow-green-900/20 transition-all hover:-translate-y-1">
                            Bayar Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- SCRIPT --}}
    <script>
        function openDonasiModal(id,nama){
            document.getElementById('modal_id_kegiatan_hidden').value = id;
            document.getElementById('namaKegiatanModal').innerText = nama;
            const modal = document.getElementById('modalDonasi');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeDonasiModal(){
            const modal = document.getElementById('modalDonasi');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        const nominalInput = document.getElementById('modal_nominal');
        nominalInput.addEventListener('input', function(){
            let value = this.value.replace(/\D/g,'');
            if(value){
                this.value = new Intl.NumberFormat('id-ID').format(value);
            }else{
                this.value = '';
            }
        });

        function setNominal(nominal){
            nominalInput.value = new Intl.NumberFormat('id-ID').format(nominal);
        }
        document.getElementById('donasiForm').onsubmit = function(e){
            e.preventDefault();
            const btn = document.getElementById('pay-button');
            const originalText = btn.innerHTML;
            btn.innerHTML = "Menghubungkan Midtrans...";
            btn.disabled = true;
            const dataDonasi = {
                id_kegiatan : document.getElementById('modal_id_kegiatan_hidden').value,
                nama_donatur : document.getElementById('modal_nama').value || 'Hamba Allah',
                nominal : document.getElementById('modal_nominal').value.replace(/\./g,'')
            };
            fetch("{{ route('donasi.checkout') }}",{
                method:"POST",
                headers:{
                    "Content-Type":"application/json",
                    "X-CSRF-TOKEN":"{{ csrf_token() }}"
                },
                body:JSON.stringify(dataDonasi)
            })
            .then(response => response.json())
            .then(data => {
                if(data.token){
                    window.snap.pay(data.token,{
                        onSuccess:function(result){
                            window.location.reload();
                        },
                        onPending:function(result){
                            window.location.reload();
                        },
                        onError:function(result){
                            alert("Pembayaran gagal");
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        },
                        onClose:function(){
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        }
                    });
                }else{
                    alert("Sistem sibuk, coba lagi nanti.");
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(err => {
                console.log(err);
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        };
    </script>
</body>
</html>