@php
$user = Auth::user();
@endphp

<div x-data="{ open: true }">

    <button @click="open = !open"
        class="md:hidden bg-gray-800 text-white p-3 m-2 rounded focus:outline-none focus:ring-2 focus:ring-gray-500">
        <i data-feather="menu"></i>
    </button>

    <aside x-show="open"
        class="w-64 bg-white shadow-md h-full p-4 hidden md:block"
        x-transition>
        
        <h2 class="text-2xl font-bold mb-6 text-center text-indigo-600">
            E-Kinerja
        </h2>

        {{-- MENU ADMIN --}}
        @role('admin')
        <nav class="space-y-1">
            <a href="{{ route('dashboard') }}" 
               class="flex items-center p-2 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <i data-feather="home" class="w-5 h-5 mr-3"></i>
                dashboard
            </a>
            <a href="{{ route('admin.pegawai.index') }}" 
               class="flex items-center p-2 rounded-lg {{ request()->routeIs('admin.pegawai*')||request()->routeIs('admin.atasan*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <i data-feather="users" class="w-5 h-5 mr-3"></i>
                Manajemen Pegawai
            </a>
            <a href="{{ route('admin.bidang.index') }}" 
               class="flex items-center p-2 rounded-lg {{ request()->routeIs('admin.bidang.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <i data-feather="grid" class="w-5 h-5 mr-3"></i>
                Manajemen Bidang
            </a>
              <a href="{{ route('admin.kehadiran.index') }}" 
               class="flex items-center p-2 rounded-lg {{ request()->routeIs('admin.kehadiran.*')||request()->routeIs('atasan.izin.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <i data-feather="map-pin" class="w-5 h-5 mr-3"></i>
                Kehadiran Pegawai
            </a>
            <a href="{{ route('admin.periode.index') }}" 
               class="flex items-center p-2 rounded-lg {{ request()->routeIs('admin.periode.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <i data-feather="calendar" class="w-5 h-5 mr-3"></i>
                Periode Penilaian
            </a>
            <a href="{{ route('admin.penilaian.index') }}" 
               class="flex items-center p-2 rounded-lg {{ request()->routeIs('admin.penilaian.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <i data-feather="file-text" class="w-5 h-5 mr-3"></i>
                Laporan Penilaian
            </a>
            <a href="{{ route('admin.aktivitas.index') }}" 
               class="flex items-center p-2 rounded-lg {{ request()->routeIs('admin.aktivitas.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <i data-feather="clipboard" class="w-5 h-5 mr-3"></i>
                Monitoring Aktivitas
            </a>
        </nav>
        @endrole

        {{-- MENU ATASAN --}}
        @role('atasan')
        <nav class="space-y-1">
            <a href="{{ route('dashboard') }}" 
               class="flex items-center p-2 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <i data-feather="home" class="w-5 h-5 mr-3"></i>
                dashboard
            </a>
            <a href="{{ route('atasan.verifikasi.index') }}" 
               class="flex items-center p-2 rounded-lg {{ request()->routeIs('atasan.verifikasi.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <i data-feather="check-square" class="w-5 h-5 mr-3"></i>
                Verifikasi Aktivitas
            </a>
            <a href="{{ route('atasan.kehadiran.index') }}" 
               class="flex items-center p-2 rounded-lg {{ request()->routeIs('atasan.kehadiran.*')||request()->routeIs('atasan.izin.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <i data-feather="map-pin" class="w-5 h-5 mr-3"></i>
                Kehadiran
            </a>
            <a href="{{ route('atasan.penilaian.index') }}" 
               class="flex items-center p-2 rounded-lg {{ request()->routeIs('atasan.penilaian.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <i data-feather="star" class="w-5 h-5 mr-3"></i>
                Penilaian Pegawai
            </a>
            <a href="{{ route('atasan.laporan.index') }}" 
               class="flex items-center p-2 rounded-lg {{ request()->routeIs('atasan.laporan.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <i data-feather="file-text" class="w-5 h-5 mr-3"></i>
                Laporan Penilaian
            </a>
             <a href="{{ route('atasan.pegawai.index') }}" 
               class="flex items-center p-2 rounded-lg {{ request()->routeIs('atasan.pegawai.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <i data-feather="file-text" class="w-5 h-5 mr-3"></i>
               Kelola Skp
            </a> 
        </nav>
        @endrole

        {{-- MENU PEGAWAI --}}
        @role('pegawai')
        <nav class="space-y-1">
            <a href="{{ route('dashboard') }}" 
               class="flex items-center p-2 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <i data-feather="home" class="w-5 h-5 mr-3"></i>
                dashboard
            </a>
            <a href="{{ route('pegawai.aktivitas.index') }}" 
               class="flex items-center p-2 rounded-lg {{ request()->routeIs('pegawai.aktivitas.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <i data-feather="edit" class="w-5 h-5 mr-3"></i>
                Aktivitas Harian
            </a>
            <a href="{{ route('pegawai.kehadiran.index') }}" 
               class="flex items-center p-2 rounded-lg {{ request()->routeIs('pegawai.kehadiran.*')||request()->routeIs('pegawai.izin.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <i data-feather="map-pin" class="w-5 h-5 mr-3"></i>
                Kehadiran
            </a>
            <a href="{{ route('pegawai.skp.index') }}" 
               class="flex items-center p-2 rounded-lg {{ request()->routeIs('pegawai.skp.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <i data-feather="target" class="w-5 h-5 mr-3"></i>
                Target SKP
            </a>
            <a href="{{ route('pegawai.skp-progress.index') }}" 
               class="flex items-center p-2 rounded-lg {{ request()->routeIs('pegawai.skp-progress.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <i data-feather="trending-up" class="w-5 h-5 mr-3"></i>
                Progress SKP
            </a>
        </nav>
        @endrole
        
    </aside>

</div>