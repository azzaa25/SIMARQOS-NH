<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Sistem Manajemen Arisan Qurban</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #f1f5f9; 
            overflow: hidden; /* No Scroll */
        }

        /* --- Animasi Latar Belakang --- */
        .bg-animated {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: #f8fafc;
        }

        .blob {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(20, 122, 84, 0.4) 0%, rgba(255, 255, 255, 0) 70%);
            border-radius: 50%;
            filter: blur(50px);
            animation: move 20s infinite alternate ease-in-out;
        }
        .blob-1 { top: -200px; left: -100px; }
        .blob-2 { bottom: -200px; right: -100px; background: radial-gradient(circle, rgba(37, 99, 235, 0.2) 0%, rgba(255, 255, 255, 0) 70%); }

        @keyframes move {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(100px, 50px) scale(1.1); }
        }

        /* --- Style Card Glassmorphism --- */
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        .select-custom {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23147a54' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 0.8rem;
        }

        .animate-fadeInCustom {
            animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen p-4">

<div class="bg-animated">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
</div>

<div class="fixed bottom-0 left-0 w-full z-0 pointer-events-none opacity-[0.08] flex items-end overflow-hidden">
    <svg class="w-full h-auto min-w-[1200px]" viewBox="0 0 1200 300" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0 300H1200V180C1150 180 1120 150 1100 120C1080 150 1050 180 1000 180V100C1000 80 980 60 960 60H940C920 60 900 80 900 100V180C850 180 820 200 800 230C780 200 750 180 700 180V50C700 22.3858 677.614 0 650 0H550C522.386 0 500 22.3858 500 50V180C450 180 420 200 400 230C380 200 350 180 300 180V100C300 80 280 60 260 60H240C220 60 200 80 200 100V180C150 180 120 150 100 120C80 150 50 180 0 180V300Z" fill="#147a54"/>
    </svg>
</div>

<div class="glass-card w-full max-w-[600px] rounded-[32px] shadow-2xl p-6 md:p-8 animate-fadeInCustom relative z-10">

    <div class="flex flex-col items-center mb-4 text-center">
        <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center mb-2 shadow-inner text-[#147a54]">
            <svg viewBox="0 0 24 24" class="w-8 h-8 fill-current">
                <path d="M12 3a9 9 0 109 9c0-.46-.04-.92-.1-1.36a5.38 5.38 0 01-4.4 2.26 5.4 5.4 0 01-5.4-5.4c0-1.8.88-3.4 2.24-4.4-.44-.06-.9-.1-1.34-.1z"/>
                <path d="M19 5l.6 1.4L21 7l-1.4.6L19 9l-.6-1.4L17 7l1.4-.6L19 5z"/>
            </svg>
        </div>
        <h1 class="text-xl font-extrabold text-gray-900 tracking-tight">Daftar Akun Peserta</h1>
        <p class="text-gray-400 text-[11px] font-medium uppercase tracking-widest">Sistem Manajemen Arisan Qurban</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-xl bg-red-50 border border-red-100 p-3 text-left">
            <ul class="list-none text-[11px] text-red-600 font-bold">
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="/register" method="POST" class="space-y-4" novalidate>
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3">
            {{-- Bagian Kiri: Kredensial --}}
            <div class="space-y-3">
                <p class="text-[10px] font-black text-green-700 uppercase tracking-widest border-b border-green-100 pb-1">Akses Akun</p>
                <div>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required
                        class="w-full px-4 py-2 bg-white border @error('email') border-red-300 @else border-gray-200 @enderror rounded-xl focus:ring-4 focus:ring-green-500/10 focus:border-green-600 outline-none text-xs font-semibold text-gray-700">
                </div>
                <div>
                    <input type="password" name="password" placeholder="Password" required
                        class="w-full px-4 py-2 bg-white border @error('password') border-red-300 @else border-gray-200 @enderror rounded-xl focus:ring-4 focus:ring-green-500/10 focus:border-green-600 outline-none text-xs font-semibold text-gray-700">
                </div>
                <div>
                    <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" required
                        class="w-full px-4 py-2 bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-green-500/10 focus:border-green-600 outline-none text-xs font-semibold text-gray-700">
                </div>
            </div>

            {{-- Bagian Kanan: Data Diri --}}
            <div class="space-y-3">
                <p class="text-[10px] font-black text-green-700 uppercase tracking-widest border-b border-green-100 pb-1">Data Diri</p>
                <div>
                    <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Nama Lengkap" required
                        class="w-full px-4 py-2 bg-white border @error('nama') border-red-300 @else border-gray-200 @enderror rounded-xl focus:ring-4 focus:ring-green-500/10 focus:border-green-600 outline-none text-xs font-semibold text-gray-700">
                </div>
                <div>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="Nomor WhatsApp" required
                        class="w-full px-4 py-2 bg-white border @error('no_hp') border-red-300 @else border-gray-200 @enderror rounded-xl focus:ring-4 focus:ring-green-500/10 focus:border-green-600 outline-none text-xs font-semibold text-gray-700">
                </div>
                <div>
                    <select name="id_skema" required class="select-custom w-full px-3 py-2 bg-white border @error('id_skema') border-red-300 @else border-gray-200 @enderror rounded-xl focus:border-green-600 outline-none text-xs font-bold text-gray-700">
                        <option value="" disabled {{ old('id_skema') == '' ? 'selected' : '' }}>Pilih Skema Qurban</option>
                        @foreach ($skemas as $skema)
                            <option value="{{ $skema->id_skema }}" {{ old('id_skema') == $skema->id_skema ? 'selected' : '' }}>{{ $skema->nama_skema }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            {{-- Baris Bawah: Alamat (Full Width) --}}
            <div class="md:col-span-2">
                <textarea name="alamat" rows="1" placeholder="Alamat Domisili Lengkap" required
                    class="w-full px-4 py-2 bg-white border @error('alamat') border-red-300 @else border-gray-200 @enderror rounded-xl focus:ring-4 focus:ring-green-500/10 focus:border-green-600 outline-none text-xs font-semibold text-gray-700 resize-none">{{ old('alamat') }}</textarea>
            </div>
        </div>

        <div class="pt-2">
            <button type="submit"
                class="w-full bg-[#147a54] hover:bg-[#0d5c3f] text-white font-black py-3 rounded-full transition-all shadow-xl shadow-green-900/20 active:scale-[0.97] tracking-widest text-[11px] uppercase">
                DAFTARKAN SAYA
            </button>
            <p class="text-center text-[11px] font-semibold text-gray-400 mt-3">
                Sudah punya akun? <a href="/login" class="text-blue-700 font-bold hover:underline ml-1">Masuk</a>
            </p>
        </div>
    </form>
</div>

</body>
</html>