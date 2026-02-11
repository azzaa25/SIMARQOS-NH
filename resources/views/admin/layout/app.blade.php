<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }} - Arisan Qurban Masjid Nurul Huda</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #14532d; border-radius: 10px; }
    </style>
</head>
<body class="bg-[#f8fafc] flex min-h-screen overflow-hidden">

    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-20 hidden backdrop-blur-sm transition-all md:hidden"></div>

    <div id="sidebar-container" class="fixed inset-y-0 left-0 z-30 transform -translate-x-full transition-transform duration-300 md:relative md:translate-x-0">
        @include('admin.layout.sidebar')
    </div>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto custom-scrollbar">
        
        <header class="bg-white/80 backdrop-blur-md sticky top-0 z-10 border-b border-gray-100 px-4 md:px-8 py-3 flex justify-between items-center">
            
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="p-2 bg-green-50 text-green-700 rounded-xl md:hidden hover:bg-green-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <div class="hidden md:block">
                    <span class="text-xs font-medium text-gray-400 italic tracking-wide">
                        Sistem Manajemen Masjid Nurul Huda
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-white p-1 pr-4 rounded-full shadow-sm border border-gray-100 transition-all hover:shadow-md">
                <div class="w-8 h-8 bg-green-800 text-white rounded-full flex items-center justify-center text-xs font-bold shadow-inner">
                    AM
                </div>
                <div class="text-[10px] leading-tight">
                    <p class="font-bold text-gray-800 tracking-tight">Admin Masjid</p>
                    <p class="text-gray-500 lowercase font-semibold">Administrator</p>
                </div>
            </div>
        </header>

        <div class="p-4 md:p-8">
            @yield('content')
        </div>
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar-container');
            const overlay = document.getElementById('sidebar-overlay');
            
            const isHidden = sidebar.classList.toggle('-translate-x-full');
            sidebar.classList.toggle('translate-x-0', !isHidden);
            
            overlay.classList.toggle('hidden', isHidden);
            overlay.classList.toggle('block', !isHidden);
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>