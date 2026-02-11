<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Sistem Manajemen Arisan Qurban</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-custom-green { background-color: #147a54; }
        .text-custom-green { color: #147a54; }

        /* PERBAIKAN DROPDOWN: Menambahkan Ikon Panah Kustom agar terlihat lebih modern */
        .select-custom {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1.2rem;
            cursor: pointer;
        }
        
        /* Efek transisi halus untuk semua input */
        input, select, textarea {
            transition: all 0.2s ease-in-out;
        }
    </style>
</head>

<body class="bg-[#f3f4f6] flex items-center justify-center min-h-screen p-4">

<div class="bg-white w-full max-w-[550px] rounded-[32px] shadow-xl shadow-gray-200/50 p-6 md:p-8">

    <div class="flex items-center gap-3 mb-6">
        <svg viewBox="0 0 24 24" class="w-9 h-9 text-custom-green fill-current">
            <path d="M12 3a9 9 0 109 9c0-.46-.04-.92-.1-1.36a5.38 5.38 0 01-4.4 2.26 5.4 5.4 0 01-5.4-5.4c0-1.8.88-3.4 2.24-4.4-.44-.06-.9-.1-1.34-.1z"/>
            <path d="M19 5l.6 1.4L21 7l-1.4.6L19 9l-.6-1.4L17 7l1.4-.6L19 5z"/>
        </svg>
        <div class="text-gray-800 font-bold text-xs uppercase tracking-wider leading-tight">
            Sistem<br>Manajemen Arisan Qurban &<br>Kegiatan Sosial
        </div>
    </div>

    <h1 class="text-xl font-extrabold text-gray-800 mb-1">Daftar Akun Peserta</h1>
    <hr class="border-gray-100 mb-4">

    @if ($errors->any())
        <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="/register" method="POST" class="space-y-4">
        @csrf

        <div class="space-y-3">
            <h3 class="text-[12px] font-bold text-gray-400 uppercase tracking-widest">
                Informasi Akun
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Email"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-600 outline-none bg-gray-50/30">
                    @error('email')
                        <p class="text-xs text-red-600 mt-1 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <input type="password" name="password" placeholder="Password"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-600 outline-none bg-gray-50/30">
                    @error('password')
                        <p class="text-xs text-red-600 mt-1 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <input type="password" name="password_confirmation" placeholder="Konfirmasi Password"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-600 outline-none bg-gray-50/30">
                </div>
            </div>
        </div>

        <div class="space-y-3 pt-2">
            <h3 class="text-[12px] font-bold text-gray-400 uppercase tracking-widest">
                Data Peserta
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                <div class="md:col-span-2">
                    <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Nama Lengkap"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-600 outline-none bg-gray-50/30">
                    @error('nama')
                        <p class="text-xs text-red-600 mt-1 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="Nomor HP / WhatsApp"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-600 outline-none bg-gray-50/30">
                    @error('no_hp')
                        <p class="text-xs text-red-600 mt-1 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <select name="jenis_kelamin"
                        class="select-custom w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-600 outline-none bg-gray-50/30 text-gray-500">
                        <option value="" disabled {{ old('jenis_kelamin') == '' ? 'selected' : '' }}>Jenis Kelamin</option>
                        <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <select name="id_skema"
                        class="select-custom w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-600 outline-none bg-gray-50/30 text-gray-500">
                        <option value="" disabled {{ old('id_skema') == '' ? 'selected' : '' }}>Pilih Skema Qurban</option>
                        @foreach ($skemas as $skema)
                            <option value="{{ $skema->id_skema }}"
                                {{ old('id_skema') == $skema->id_skema ? 'selected' : '' }}>
                                {{ $skema->nama_skema }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_skema')
                        <p class="text-xs text-red-600 mt-1 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <textarea name="alamat" rows="2" placeholder="Alamat Rumah Lengkap"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-600 outline-none bg-gray-50/30 resize-none">{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <p class="text-xs text-red-600 mt-1 ml-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        <div class="pt-4 space-y-3">
            <button type="submit"
                class="w-full bg-[#147a54] hover:bg-[#0e5e40] text-white font-bold py-3.5 rounded-full transition-all shadow-lg shadow-green-900/20 active:scale-[0.97] tracking-wide">
                DAFTAR SEKARANG
            </button>

            <p class="text-center text-sm text-gray-500">
                Sudah punya akun?
                <a href="/login" class="text-blue-700 font-bold hover:underline ml-1">Masuk</a>
            </p>
        </div>

    </form>
</div>

</body>
</html>