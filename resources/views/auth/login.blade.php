<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Manajemen Arisan Qurban</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-custom-green { background-color: #147a54; }
        .text-custom-green { color: #147a54; }
    </style>
</head>

<body class="bg-[#f3f4f6] flex items-center justify-center min-h-screen p-4">

<div class="bg-white w-full max-w-[420px] rounded-[32px] shadow-xl shadow-gray-200/50 p-8 md:p-12">

    <!-- Logo -->
    <div class="flex items-center gap-3 mb-10">
        <svg viewBox="0 0 24 24" class="w-10 h-10 text-custom-green fill-current">
            <path d="M12 3a9 9 0 109 9c0-.46-.04-.92-.1-1.36a5.38 5.38 0 01-4.4 2.26 5.4 5.4 0 01-5.4-5.4c0-1.8.88-3.4 2.24-4.4-.44-.06-.9-.1-1.34-.1z"/>
            <path d="M19 5l.6 1.4L21 7l-1.4.6L19 9l-.6-1.4L17 7l1.4-.6L19 5z"/>
        </svg>
        <div class="leading-tight text-gray-800 font-bold text-sm">
            Sistem<br>Manajemen Arisan Qurban &<br> Kegiatan Sosial
        </div>
    </div>

    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6">
        Masuk ke Akun Anda
    </h1>

    <!-- ERROR GLOBAL -->
    @if ($errors->has('login'))
        <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700">
            {{ $errors->first('login') }}
        </div>
    @endif

    <!-- ERROR VALIDASI -->
    @if ($errors->any() && !$errors->has('login'))
        <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="/login" method="POST" class="space-y-6">
        @csrf

        <!-- EMAIL -->
        <div>
            <label class="block text-xs font-semibold text-gray-500 ml-1 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email"
                class="w-full px-4 py-3.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-600 outline-none">

            @error('email')
                <p class="text-xs text-red-600 mt-1 ml-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- PASSWORD -->
        <div>
            <label class="block text-xs font-semibold text-gray-500 ml-1 mb-1">Password</label>
            <input type="password" name="password" placeholder="Password minimal 8 karakter"
                class="w-full px-4 py-3.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-600 outline-none">

            @error('password')
                <p class="text-xs text-red-600 mt-1 ml-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- LINK -->
        <div class="flex items-center justify-between text-[13px] font-medium px-1">
            <a href="#" class="text-gray-500 hover:underline">Lupa Password?</a>
            <a href="/register" class="text-blue-700 font-bold hover:underline">Daftar Sekarang</a>
        </div>

        <!-- BUTTON -->
        <button type="submit"
            class="w-full bg-[#147a54] hover:bg-[#0e5e40] text-white font-bold py-4 rounded-full transition-all shadow-lg shadow-green-900/20 active:scale-[0.98]">
            MASUK
        </button>

    </form>
</div>

</body>
</html>
