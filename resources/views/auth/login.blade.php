<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Arisan Qurban Masjid Nurul Huda</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #f1f5f9; 
            overflow: hidden; 
        }

        .bg-animated {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: #f8fafc;
            overflow: hidden;
        }

        .blob {
            position: absolute;
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(20, 122, 84, 0.45) 0%, rgba(255, 255, 255, 0) 70%);
            border-radius: 50%;
            filter: blur(50px);
            animation: move 20s infinite alternate ease-in-out;
        }

        .blob-1 { top: -200px; left: -100px; }
        .blob-2 { bottom: -200px; right: -100px; background: radial-gradient(circle, rgba(37, 99, 235, 0.2) 0%, rgba(255, 255, 255, 0) 70%); animation-delay: -5s; }
        .blob-3 { top: 30%; left: 40%; width: 500px; height: 500px; background: radial-gradient(circle, rgba(16, 185, 129, 0.3) 0%, rgba(255, 255, 255, 0) 70%); animation-duration: 15s; }

        @keyframes move {
            0% { transform: translate(0, 0) scale(1) rotate(0deg); }
            100% { transform: translate(150px, 100px) scale(1.2) rotate(15deg); }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        .text-custom-green { color: #147a54; }
        .input-transition { transition: all 0.2s ease-in-out; }
        
        .swal2-popup { border-radius: 32px !important; padding: 2em !important; }
        .swal2-confirm { border-radius: 99px !important; padding: 12px 35px !important; font-weight: 800 !important; font-size: 14px !important; text-transform: uppercase !important; letter-spacing: 1px !important; }
        
        .animate-fadeInCustom {
            animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen p-4">

<div class="bg-animated">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
</div>

{{-- Tombol Kembali ke Welcome (Floating) --}}
<div class="fixed top-6 left-6 z-50">
    <a href="/" class="flex items-center gap-2 px-5 py-2.5 bg-white/80 backdrop-blur-md border border-white/50 rounded-full text-[11px] font-black text-slate-600 hover:text-[#147a54] hover:shadow-lg transition-all group shadow-sm">
        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </a>
</div>

<div class="fixed bottom-0 left-0 w-full z-0 pointer-events-none opacity-[0.08] flex items-end overflow-hidden text-left">
    <svg class="w-full h-auto min-w-[1200px]" viewBox="0 0 1200 300" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0 300H1200V180C1150 180 1120 150 1100 120C1080 150 1050 180 1000 180V100C1000 80 980 60 960 60H940C920 60 900 80 900 100V180C850 180 820 200 800 230C780 200 750 180 700 180V50C700 22.3858 677.614 0 650 0H550C522.386 0 500 22.3858 500 50V180C450 180 420 200 400 230C380 200 350 180 300 180V100C300 80 280 60 260 60H240C220 60 200 80 200 100V180C150 180 120 150 100 120C80 150 50 180 0 180V300Z" fill="#147a54"/>
    </svg>
</div>

<div class="glass-card w-full max-w-[450px] rounded-[40px] shadow-[0_20px_50px_rgba(0,0,0,0.08)] p-8 md:p-12 text-center animate-fadeInCustom relative z-10">

    <div class="flex flex-col items-center mb-8">
        <div class="w-16 h-16 bg-green-50 rounded-2xl flex items-center justify-center mb-4 shadow-inner text-custom-green">
            <svg viewBox="0 0 24 24" class="w-10 h-10 fill-current">
                <path d="M12 3a9 9 0 109 9c0-.46-.04-.92-.1-1.36a5.38 5.38 0 01-4.4 2.26 5.4 5.4 0 01-5.4-5.4c0-1.8.88-3.4 2.24-4.4-.44-.06-.9-.1-1.34-.1z"/>
                <path d="M19 5l.6 1.4L21 7l-1.4.6L19 9l-.6-1.4L17 7l1.4-.6L19 5z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight leading-tight">
            Selamat Datang 
        </h1>
        <p class="text-gray-500 text-[10px] mt-2 font-black italic leading-relaxed uppercase tracking-[0.2em]">
            Sistem Manajemen Arisan Qurban & Kegiatan Sosial
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-2xl bg-red-100/80 backdrop-blur-sm border border-red-200 p-4 text-left shadow-sm">
            <div class="flex items-center gap-2 mb-2 text-red-700">
                <svg class="w-4 h-4 font-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-[10px] font-black uppercase tracking-widest">Akses Ditolak</span>
            </div>
            <ul class="list-none space-y-1 text-[12px] text-red-600 font-bold">
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="/login" method="POST" class="space-y-5 text-left" novalidate>
        @csrf

        <div class="space-y-1.5">
            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Alamat Email</label>
            <input type="email" name="email" placeholder="Email Anda" required
                class="input-transition w-full px-5 py-4 bg-white border @error('email') border-red-300 @else border-gray-100 @enderror rounded-2xl focus:ring-4 focus:ring-green-500/10 focus:border-green-600 outline-none text-sm font-semibold text-gray-700">
        </div>

        <div class="space-y-1.5">
            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Kata Sandi</label>
            <div class="relative group">
                <input type="password" name="password" id="passwordInput" placeholder="••••••••" required
                    class="input-transition w-full px-5 py-4 bg-white border @error('password') border-red-300 @else border-gray-100 @enderror rounded-2xl focus:ring-4 focus:ring-green-500/10 focus:border-green-600 outline-none text-sm font-semibold text-gray-700 pr-12">
                
                <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-300 hover:text-green-600 transition-colors focus:outline-none">
                    <svg id="eyeIconOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg id="eyeIconClosed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="pt-4">
            <button type="submit"
                class="w-full bg-[#147a54] hover:bg-[#0d5c3f] text-white font-black py-4 rounded-full transition-all shadow-xl shadow-green-900/20 active:scale-[0.97] tracking-widest text-xs uppercase">
                MASUK KE SISTEM
            </button>
        </div>

        <p class="text-center text-sm font-semibold text-gray-400 mt-6">
            Belum memiliki akun? 
            <a href="/register" class="text-blue-700 font-bold hover:underline ml-1">Daftar Sekarang</a>
        </p>
    </form>
</div>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('passwordInput');
        const eyeIconOpen = document.getElementById('eyeIconOpen');
        const eyeIconClosed = document.getElementById('eyeIconClosed');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIconOpen.classList.add('hidden');
            eyeIconClosed.classList.remove('hidden');
        } else {
            passwordInput.type = 'password';
            eyeIconOpen.classList.remove('hidden');
            eyeIconClosed.classList.add('hidden');
        }
    }

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            confirmButtonColor: '#147a54',
            timer: 4000
        });
    @endif
</script>

</body>
</html>