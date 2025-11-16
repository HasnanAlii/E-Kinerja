@php
$user = Auth::user();
@endphp

<div x-data="{ open: true }">

    <!-- MOBILE BUTTON -->
    <button @click="open = !open"
        class="md:hidden bg-gray-800 text-white p-3 m-2 rounded">
        ☰ Menu
    </button>

    <!-- SIDEBAR -->
    <aside x-show="open"
        class="w-64 bg-white shadow-md min-h-screen p-4 hidden md:block"
        x-transition>
        
        <h2 class="text-xl font-bold mb-6 text-center">E-Kinerja</h2>

        {{-- MENU ADMIN --}}
        @role('admin')
        <nav class="space-y-2">

            <a href="{{ route('admin.pegawai.index') }}"
                class="block p-2 rounded hover:bg-gray-100">
                👤 Manajemen Pegawai
            </a>

            <a href="{{ route('admin.bidang.index') }}"
                class="block p-2 rounded hover:bg-gray-100">
                🧩 Bidang
            </a>

            <a href="{{ route('admin.periode.index') }}"
                class="block p-2 rounded hover:bg-gray-100">
                📅 Periode Penilaian
            </a>

            <a href="{{ route('admin.penilaian.index') }}"
                class="block p-2 rounded hover:bg-gray-100">
                📊 Laporan Penilaian
            </a>

            <a href="{{ route('admin.aktivitas.index') }}"
                class="block p-2 rounded hover:bg-gray-100">
                📝 Monitoring Aktivitas
            </a>

            <a href="{{ route('admin.pengguna.index') }}"
                class="block p-2 rounded hover:bg-gray-100">
                🔐 Manajemen Role
            </a>

        </nav>
        @endrole


        {{-- MENU ATASAN --}}
        @role('atasan')
        <nav class="space-y-2">

            <a href="{{ route('atasan.verifikasi.index') }}"
                class="block p-2 rounded hover:bg-gray-100">
                ✔ Verifikasi Aktivitas
            </a>

            <a href="{{ route('atasan.penilaian.index') }}"
                class="block p-2 rounded hover:bg-gray-100">
                ⭐ Penilaian Pegawai
            </a>

            <a href="{{ route('atasan.laporan.index') }}"
                class="block p-2 rounded hover:bg-gray-100">
                📄 Laporan Penilaian
            </a>

        </nav>
        @endrole


        {{-- MENU PEGAWAI --}}
        @role('pegawai')
        <nav class="space-y-2">

            <a href="{{ route('pegawai.aktivitas.index') }}"
                class="block p-2 rounded hover:bg-gray-100">
                📝 Aktivitas Harian
            </a>

            <a href="{{ route('pegawai.kehadiran.index') }}"
                class="block p-2 rounded hover:bg-gray-100">
                📍 Kehadiran
            </a>

            <a href="{{ route('pegawai.izin.index') }}"
                class="block p-2 rounded hover:bg-gray-100">
                🗂 Izin / Sakit
            </a>

            <a href="{{ route('pegawai.skp.index') }}"
                class="block p-2 rounded hover:bg-gray-100">
                🎯 Target SKP
            </a>

            <a href="{{ route('pegawai.skp-progress.index') }}"
                class="block p-2 rounded hover:bg-gray-100">
                📈 Progress SKP
            </a>

            <a href="{{ route('pegawai.profil.index') }}"
                class="block p-2 rounded hover:bg-gray-100">
                👤 Profil Saya
            </a>

        </nav>
        @endrole

    </aside>

</div>
