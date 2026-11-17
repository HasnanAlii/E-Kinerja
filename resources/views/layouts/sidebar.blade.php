@php
$user = Auth::user();
$roleName = ucfirst($user->roles->first()->name ?? 'User');
@endphp

<div x-data="{ open: true }" class="relative z-40">

    {{-- TOMBOL TOGGLE MOBILE --}}
    <button @click="open = !open"
        class="md:hidden fixed top-3 left-3 z-50 bg-indigo-600 text-white p-2.5 rounded-xl shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
        <i data-feather="menu" class="w-5 h-5"></i>
    </button>

    <aside x-show="open"
        class="w-72 bg-gradient-to-b from-white via-white to-indigo-50/40 text-slate-700 shadow-lg min-h-screen h-full flex flex-col justify-between border-r border-slate-200/90 hidden md:flex select-none"
        x-transition>

        {{-- BAGIAN ATAS: BRANDING & NAVIGASI --}}
        <div>
            {{-- HEADER BRANDING PT POU YUEN --}}
            <div class="px-6 pt-6 pb-5 border-b border-slate-100">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3.5 group">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-600 to-blue-600 text-white shadow-md shadow-indigo-500/25 flex items-center justify-center ring-2 ring-indigo-100 group-hover:scale-105 transition-transform duration-200 shrink-0">
                        {{-- Ikon Gedung Perusahaan (Corporate Building) --}}
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V5a2 2 0 012-2h6a2 2 0 012 2v16M15 21V9a2 2 0 012-2h2a2 2 0 012 2v12M9 7h2M9 11h2M9 15h2"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-extrabold tracking-tight text-slate-800 leading-tight group-hover:text-indigo-600 transition-colors">
                            PT POU YUEN
                        </h2>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[11px] font-semibold text-indigo-600 tracking-wide">
                                Smart Attendance AI
                            </span>
                        </div>
                    </div>
                </a>
            </div>

            {{-- KARTU RINGKAS PENGGUNA AKTIF --}}
            <div class="px-4 pt-4 pb-2">
                <div class="bg-gradient-to-r from-indigo-50/80 to-blue-50/80 border border-indigo-100 rounded-xl p-3 flex items-center gap-3 shadow-xs">
                    @if ($user && $user->profile_photo)
                        <img src="{{ asset('storage/' . $user->profile_photo) }}"
                             class="w-10 h-10 rounded-lg object-cover ring-2 ring-white shadow-xs shrink-0">
                    @else
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-600 to-blue-600 flex items-center justify-center text-white font-bold text-sm shadow-xs shrink-0">
                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                        </div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-slate-800 truncate">
                            {{ $user->name ?? 'Pengguna' }}
                        </p>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-white text-indigo-700 border border-indigo-200/80 shadow-2xs">
                                {{ $roleName === 'Pegawai' ? 'Karyawan' : $roleName }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= MENU ADMIN ================= --}}
            @role('admin')
            <div class="px-4 pt-4">
                <p class="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                    Menu Administrator
                </p>
                <nav class="space-y-1.5">
                    @php $isDash = request()->routeIs('dashboard'); @endphp
                    <a href="{{ route('dashboard') }}"
                       class="group flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $isDash ? 'bg-gradient-to-r from-indigo-600 to-blue-600 text-white shadow-md shadow-indigo-500/25 font-semibold' : 'text-slate-600 hover:bg-indigo-50/80 hover:text-indigo-700' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ $isDash ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-indigo-100 group-hover:text-indigo-600' }}">
                                <i data-feather="home" class="w-4 h-4"></i>
                            </span>
                            <span>Dashboard</span>
                        </div>
                        @if($isDash)
                            <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                        @endif
                    </a>

                    @php $isPegawai = request()->routeIs('admin.pegawai*') || request()->routeIs('admin.atasan*'); @endphp
                    <a href="{{ route('admin.pegawai.index') }}"
                       class="group flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $isPegawai ? 'bg-gradient-to-r from-indigo-600 to-blue-600 text-white shadow-md shadow-indigo-500/25 font-semibold' : 'text-slate-600 hover:bg-indigo-50/80 hover:text-indigo-700' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ $isPegawai ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-indigo-100 group-hover:text-indigo-600' }}">
                                <i data-feather="users" class="w-4 h-4"></i>
                            </span>
                            <span>Data Karyawan</span>
                        </div>
                        @if($isPegawai)
                            <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                        @endif
                    </a>

                    {{-- DISABLED: <a href="{{ route('admin.bidang.index') }}" 
                       class="flex items-center p-2 rounded-lg {{ request()->routeIs('admin.bidang.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <i data-feather="grid" class="w-5 h-5 mr-3"></i>
                        Manajemen Bidang
                    </a> --}}

                    @php $isAdminHadir = request()->routeIs('admin.kehadiran.*') || request()->routeIs('atasan.izin.*'); @endphp
                    <a href="{{ route('admin.kehadiran.index') }}"
                       class="group flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $isAdminHadir ? 'bg-gradient-to-r from-indigo-600 to-blue-600 text-white shadow-md shadow-indigo-500/25 font-semibold' : 'text-slate-600 hover:bg-indigo-50/80 hover:text-indigo-700' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ $isAdminHadir ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-indigo-100 group-hover:text-indigo-600' }}">
                                <i data-feather="calendar" class="w-4 h-4"></i>
                            </span>
                            <span>Kehadiran Karyawan</span>
                        </div>
                        @if($isAdminHadir)
                            <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                        @endif
                    </a>

                    {{-- DISABLED: <a href="{{ route('admin.periode.index') }}" 
                       class="flex items-center p-2 rounded-lg {{ request()->routeIs('admin.periode.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <i data-feather="repeat" class="w-5 h-5 mr-3"></i>
                        Periode Penilaian
                    </a> --}}
                    {{-- DISABLED: <a href="{{ route('admin.penilaian.index') }}" 
                       class="flex items-center p-2 rounded-lg {{ request()->routeIs('admin.penilaian.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <i data-feather="file-text" class="w-5 h-5 mr-3"></i>
                        Laporan Kehadiran
                    </a> --}}
                    {{-- DISABLED: <a href="{{ route('admin.aktivitas.index') }}" 
                       class="flex items-center p-2 rounded-lg {{ request()->routeIs('admin.aktivitas.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <i data-feather="clipboard" class="w-5 h-5 mr-3"></i>
                        Monitoring Aktivitas
                    </a> --}}
                </nav>
            </div>
            @endrole

            {{-- ================= MENU ATASAN ================= --}}
            @role('atasan')
            <div class="px-4 pt-4">
                <p class="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                    Menu Utama
                </p>
                <nav class="space-y-1.5">
                    @php $isDash = request()->routeIs('dashboard'); @endphp
                    <a href="{{ route('dashboard') }}"
                       class="group flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $isDash ? 'bg-gradient-to-r from-indigo-600 to-blue-600 text-white shadow-md shadow-indigo-500/25 font-semibold' : 'text-slate-600 hover:bg-indigo-50/80 hover:text-indigo-700' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ $isDash ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-indigo-100 group-hover:text-indigo-600' }}">
                                <i data-feather="home" class="w-4 h-4"></i>
                            </span>
                            <span>Dashboard</span>
                        </div>
                        @if($isDash)
                            {{-- <span class="w-1.5 h-1.5 rounded-full bg-white"></span> --}}
                        @endif
                    </a>

                    {{-- DISABLED: <a href="{{ route('atasan.verifikasi.index') }}" 
                       class="flex items-center p-2 rounded-lg {{ request()->routeIs('atasan.verifikasi.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <i data-feather="check-square" class="w-5 h-5 mr-3"></i>
                        Verifikasi Aktivitas
                    </a> --}}

                    @php $isPegawai = request()->routeIs('admin.pegawai.*'); @endphp
                    <a href="{{ route('admin.pegawai.index') }}"
                       class="group flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $isPegawai ? 'bg-gradient-to-r from-indigo-600 to-blue-600 text-white shadow-md shadow-indigo-500/25 font-semibold' : 'text-slate-600 hover:bg-indigo-50/80 hover:text-indigo-700' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ $isPegawai ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-indigo-100 group-hover:text-indigo-600' }}">
                                <i data-feather="users" class="w-4 h-4"></i>
                            </span>
                            <span>Data Karyawan</span>
                        </div>
                        @if($isPegawai)
                            {{-- <span class="w-1.5 h-1.5 rounded-full bg-white"></span> --}}
                        @endif
                    </a>

                    <p class="px-3 pt-3 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Pengawasan &amp; Laporan
                    </p>

                    @php $isMon = request()->routeIs('atasan.monitoring.*'); @endphp
                    <a href="{{ route('atasan.monitoring.index') }}"
                       class="group flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $isMon ? 'bg-gradient-to-r from-indigo-600 to-blue-600 text-white shadow-md shadow-indigo-500/25 font-semibold' : 'text-slate-600 hover:bg-indigo-50/80 hover:text-indigo-700' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ $isMon ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-indigo-100 group-hover:text-indigo-600' }}">
                                <i data-feather="video" class="w-4 h-4"></i>
                            </span>
                            <span>Monitoring Kamera</span>
                        </div>
                        {{-- <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $isMon ? 'bg-white/20 text-white' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                            AI
                        </span> --}}
                    </a>

                    @php $isHadir = request()->routeIs('atasan.kehadiran.*') || request()->routeIs('atasan.izin.*'); @endphp
                    <a href="{{ route('atasan.kehadiran.index') }}"
                       class="group flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $isHadir ? 'bg-gradient-to-r from-indigo-600 to-blue-600 text-white shadow-md shadow-indigo-500/25 font-semibold' : 'text-slate-600 hover:bg-indigo-50/80 hover:text-indigo-700' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ $isHadir ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-indigo-100 group-hover:text-indigo-600' }}">
                                <i data-feather="calendar" class="w-4 h-4"></i>
                            </span>
                            <span>Kehadiran &amp; Izin</span>
                        </div>
                        @if($isHadir)
                            {{-- <span class="w-1.5 h-1.5 rounded-full bg-white"></span> --}}
                        @endif
                    </a>

                    {{-- DISABLED: <a href="{{ route('atasan.penilaian.index') }}" 
                       class="flex items-center p-2 rounded-lg {{ request()->routeIs('atasan.penilaian.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <i data-feather="star" class="w-5 h-5 mr-3"></i>
                        Penilaian Pegawai
                    </a> --}}

                    @php $isLap = request()->routeIs('atasan.laporan.*'); @endphp
                    <a href="{{ route('atasan.laporan.index') }}"
                       class="group flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $isLap ? 'bg-gradient-to-r from-indigo-600 to-blue-600 text-white shadow-md shadow-indigo-500/25 font-semibold' : 'text-slate-600 hover:bg-indigo-50/80 hover:text-indigo-700' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ $isLap ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-indigo-100 group-hover:text-indigo-600' }}">
                                <i data-feather="file-text" class="w-4 h-4"></i>
                            </span>
                            <span>Laporan Kehadiran</span>
                        </div>
                        @if($isLap)
                            {{-- <span class="w-1.5 h-1.5 rounded-full bg-white"></span> --}}
                        @endif
                    </a>

                    {{-- DISABLED: <a href="{{ route('atasan.skp.index') }}" 
                       class="flex items-center p-2 rounded-lg {{ request()->routeIs('atasan.pegawai.*')||request()->routeIs('atasan.skp.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <i data-feather="target" class="w-5 h-5 mr-3"></i>
                       Target Skp
                    </a> --}}
                </nav>
            </div>
            @endrole

            {{-- ================= MENU PEGAWAI / KARYAWAN ================= --}}
            @role('pegawai')
            <div class="px-4 pt-4">
                <p class="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                    Menu Karyawan
                </p>
                <nav class="space-y-1.5">
                    @php $isDash = request()->routeIs('dashboard'); @endphp
                    <a href="{{ route('dashboard') }}"
                       class="group flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $isDash ? 'bg-gradient-to-r from-indigo-600 to-blue-600 text-white shadow-md shadow-indigo-500/25 font-semibold' : 'text-slate-600 hover:bg-indigo-50/80 hover:text-indigo-700' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ $isDash ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-indigo-100 group-hover:text-indigo-600' }}">
                                <i data-feather="home" class="w-4 h-4"></i>
                            </span>
                            <span>Dashboard</span>
                        </div>
                        @if($isDash)
                            {{-- <span class="w-1.5 h-1.5 rounded-full bg-white"></span> --}}
                        @endif
                    </a>

                    {{-- DISABLED: <a href="{{ route('pegawai.aktivitas.index') }}" 
                       class="flex items-center p-2 rounded-lg {{ request()->routeIs('pegawai.aktivitas.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <i data-feather="edit" class="w-5 h-5 mr-3"></i>
                        Aktivitas Harian
                    </a> --}}

                    @php $isPegHadir = request()->routeIs('pegawai.kehadiran.*') || request()->routeIs('pegawai.izin.*'); @endphp
                    <a href="{{ route('pegawai.kehadiran.index') }}"
                       class="group flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $isPegHadir ? 'bg-gradient-to-r from-indigo-600 to-blue-600 text-white shadow-md shadow-indigo-500/25 font-semibold' : 'text-slate-600 hover:bg-indigo-50/80 hover:text-indigo-700' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ $isPegHadir ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-indigo-100 group-hover:text-indigo-600' }}">
                                <i data-feather="calendar" class="w-4 h-4"></i>
                            </span>
                            <span>Kehadiran &amp; Izin</span>
                        </div>
                        @if($isPegHadir)
                            {{-- <span class="w-1.5 h-1.5 rounded-full bg-white"></span> --}}
                        @endif
                    </a>

                    {{-- DISABLED: <a href="{{ route('pegawai.skp.index') }}" 
                       class="flex items-center p-2 rounded-lg {{ request()->routeIs('pegawai.skp.*') || request()->routeIs('skp.*')? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <i data-feather="target" class="w-5 h-5 mr-3"></i>
                        Target Skp
                    </a> --}}
                    {{-- <a href="{{ route('pegawai.skp-progress.index') }}" 
                       class="flex items-center p-2 rounded-lg {{ request()->routeIs('pegawai.skp-progress.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <i data-feather="trending-up" class="w-5 h-5 mr-3"></i>
                        Progress SKP
                    </a> --}}
                </nav>
            </div>
            @endrole

            {{-- MENU AKUN & PROFIL --}}
            <div class="px-4 pt-4">
                <p class="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                    Akun Saya
                </p>
                <nav class="space-y-1.5">
                    @php $isProfil = request()->routeIs('profil.*') || request()->routeIs('profile.*'); @endphp
                    <a href="{{ route('profil.edit') }}"
                       class="group flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $isProfil ? 'bg-gradient-to-r from-indigo-600 to-blue-600 text-white shadow-md shadow-indigo-500/25 font-semibold' : 'text-slate-600 hover:bg-indigo-50/80 hover:text-indigo-700' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ $isProfil ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-indigo-100 group-hover:text-indigo-600' }}">
                                <i data-feather="user-check" class="w-4 h-4"></i>
                            </span>
                            <span>Profil &amp; Data Diri</span>
                        </div>
                        @if($isProfil)
                            {{-- <span class="w-1.5 h-1.5 rounded-full bg-white"></span> --}}
                        @endif
                    </a>
                </nav>
            </div>
        </div>

        {{-- BAGIAN BAWAH: INFO STANDAR 8 JAM & AI ENGINE --}}
        <div class="p-4 border-t border-slate-100">
            <div class="bg-gradient-to-br from-indigo-50 via-blue-50/70 to-white border border-indigo-100 rounded-xl p-3.5 shadow-2xs">
                <div class="flex items-center gap-2.5 mb-1.5">
                    <div class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center shadow-xs">
                        <i data-feather="cpu" class="w-3.5 h-3.5"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-800 leading-none">YOLOv8 + CNN</p>
                        <p class="text-[10px] font-semibold text-indigo-600 mt-0.5">Target Kerja: 8 Jam / Hari</p>
                    </div>
                </div>
                <p class="text-[11px] text-slate-500 leading-relaxed">
                    Absensi otomatis berbasis deteksi kamera zona kerja PT Pou Yuen.
                </p>
            </div>
        </div>

    </aside>

</div>
