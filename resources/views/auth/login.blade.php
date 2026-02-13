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
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .bg-custom-green { background-color: #147a54; }
        .text-custom-green { color: #147a54; }
        .input-transition { transition: all 0.2s ease-in-out; }
        
        .swal2-popup { border-radius: 32px !important; padding: 2em !important; }
        .swal2-confirm { border-radius: 99px !important; padding: 12px 35px !important; font-weight: 800 !important; font-size: 14px !important; text-transform: uppercase !important; letter-spacing: 1px !important; }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen p-4">

<div class="bg-white w-full max-w-[450px] rounded-[40px] shadow-[0_20px_50px_rgba(0,0,0,0.05)] border border-gray-50 p-8 md:p-12 text-center">

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
        <p class="text-gray-400 text-sm mt-2 font-medium italic leading-relaxed">
            Sistem Manajemen Masjid Nurul Huda
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-2xl bg-red-50 border border-red-100 p-4 text-left animate-fadeIn">
            <div class="flex items-center gap-2 mb-2 text-red-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-xs font-black uppercase tracking-widest">Terjadi Kesalahan</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-[13px] text-red-600 font-semibold">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="/login" method="POST" class="space-y-5 text-left" novalidate>
        @csrf

        <div class="space-y-1.5">
            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Alamat Email</label>
            <input type="email" name="email" placeholder="Email Anda" required
                class="input-transition w-full px-5 py-4 bg-gray-50 border @error('email') border-red-300 @else border-gray-200 @enderror rounded-2xl focus:ring-4 focus:ring-green-500/10 focus:border-green-600 outline-none text-sm font-semibold">
        </div>

        <div class="space-y-1.5">
            <div class="flex justify-between items-center px-1">
                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest">Kata Sandi</label>
            </div>
            <input type="password" name="password" placeholder="••••••••" required
                class="input-transition w-full px-5 py-4 bg-gray-50 border @error('password') border-red-300 @else border-gray-200 @enderror rounded-2xl focus:ring-4 focus:ring-green-500/10 focus:border-green-600 outline-none text-sm font-semibold">
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
    // Pop-up hanya untuk Pesan Sukses agar tidak double dengan alert box di atas
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