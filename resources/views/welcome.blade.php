<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>E-Kinerja Dinas Arsip & Perpustakaan Cianjur</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/logo.png') }}">


    <!-- Font -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
</head>

<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    <header class="w-full py-4 border-b bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">

            <div class="flex items-center gap-2">
                <span class="p-2 bg-indigo-600 rounded-lg">
                    <i data-feather="archive" class="text-white w-5 h-5"></i>
                </span>
                <h1 class="text-lg font-semibold text-gray-900">
                    E-Kinerja Dinas Arsip & Perpustakaan Cianjur
                </h1>
            </div>

            <nav class="flex items-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}"
                       class="px-4 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="px-4 py-2 border-2 border-indigo-400 text-indigo rounded-lg shadow hover:bg-indigo-700 transition">
                        Login
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    {{-- HERO SECTION --}}
    <section class="flex-1 py-3">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            {{-- LEFT TEXT --}}
            <div>
                <h2 class="text-4xl font-extrabold text-gray-900 leading-tight mb-6">
                    Sistem E-Kinerja <span class="text-indigo-600">Terintegrasi</span><br>
                    Untuk Dinas Arsip & Perpustakaan Kabupaten Cianjur
                </h2>

                <p class="text-lg text-gray-600 leading-relaxed mb-8">
                    Platform digital untuk memantau kinerja, aktivitas harian, kehadiran,
                    serta pengajuan izin / sakit pegawai secara realtime dan lebih transparan.
                </p>

                <div class="flex gap-4">
                    @guest
                        <a href="{{ route('login') }}"
                           class="px-6 py-3 bg-indigo-600 text-white text-base rounded-lg shadow hover:bg-indigo-700 transition flex items-center gap-2">
                            <i data-feather="log-in" class="w-5 h-5"></i>
                            Masuk Sistem
                        </a>
                    @else
                        <a href="/dashboard"
                           class="px-6 py-3 bg-indigo-600 text-white text-base rounded-lg shadow hover:bg-indigo-700 transition flex items-center gap-2">
                            <i data-feather="grid" class="w-5 h-5"></i>
                            Pergi ke Dashboard
                        </a>
                    @endguest
                </div>
            </div>

            {{-- RIGHT ILLUSTRATION --}}
            <div class="flex justify-center">
                <img src="https://img.icons8.com/fluency/500/working-with-a-laptop.png"
                    class="max-w-md w-full drop-shadow-xl"
                    alt="E-Kinerja Illustration">
            </div>


        </div>
    </section>

    {{-- FEATURES SECTION --}}
    <section class="py-6 bg-white border-t">
        <div class="max-w-7xl mx-auto px-6">

            <h3 class="text-3xl font-bold text-gray-900 text-center mb-12">
                Fitur E-Kinerja 
                {{-- Dinas Arsip & Perpustakaan --}}
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <div class="bg-gray-50 p-6 rounded-xl shadow-sm border">
                    <i data-feather="check-circle" class="w-10 h-10 text-indigo-600 mb-4"></i>
                    <h4 class="font-semibold text-lg text-gray-900 mb-2">Aktivitas Harian</h4>
                    <p class="text-gray-600">Catat, laporkan, dan pantau aktivitas harian pegawai secara efisien.</p>
                </div>

                <div class="bg-gray-50 p-6 rounded-xl shadow-sm border">
                    <i data-feather="clock" class="w-10 h-10 text-indigo-600 mb-4"></i>
                    <h4 class="font-semibold text-lg text-gray-900 mb-2">Kehadiran Online</h4>
                    <p class="text-gray-600">Fitur absensi dengan check-in dan check-out realtime.</p>
                </div>

                <div class="bg-gray-50 p-6 rounded-xl shadow-sm border">
                    <i data-feather="file-text" class="w-10 h-10 text-indigo-600 mb-4"></i>
                    <h4 class="font-semibold text-lg text-gray-900 mb-2">Pengajuan Izin / Sakit</h4>
                    <p class="text-gray-600">Ajukan izin atau sakit dengan lampiran file secara digital.</p>
                </div>

                <div class="bg-gray-50 p-6 rounded-xl shadow-sm border">
                    <i data-feather="star" class="w-10 h-10 text-indigo-600 mb-4"></i>
                    <h4 class="font-semibold text-lg text-gray-900 mb-2">Penilaian Performa Pegawai</h4>
                    <p class="text-gray-600">Pantau dan nilai kinerja pegawai secara objektif dan terstruktur.</p>
                </div>

            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="py-6 text-center text-gray-600 text-sm">
        © {{ date('Y') }} Dinas Arsip & Perpustakaan Kabupaten Cianjur —
        Sistem E-Kinerja Terintegrasi.
    </footer>

    <script>
        feather.replace();
    </script>

</body>
</html>
