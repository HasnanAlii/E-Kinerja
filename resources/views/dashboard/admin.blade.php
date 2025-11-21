{{-- resources/views/dashboard/admin.blade.php --}}
<x-app-layout>
    <div class="py-12 bg-gray-50/50 min-h-screen">
        <div class="mx-auto sm:px-6 lg:px-8">

            {{-- ALERT WELCOME --}}
            @if(session('welcome') || true)
                <div id="welcomeAlert" class="mb-6">
                    <div class="flex items-start gap-3 bg-blue-50 border border-blue-200 text-blue-800 px-5 py-4 rounded-xl shadow-sm relative">
                        <div class="flex-shrink-0">
                            <i data-feather="info" class="w-5 h-5 text-blue-600"></i>
                        </div>

                        <div class="flex-1">
                            <p class="font-semibold text-blue-900">Selamat datang, {{ Auth::user()->name }}! 🎉</p>
                            <p class="text-sm text-blue-700 mt-0.5">
                                Semoga hari Anda menyenangkan. Berikut ringkasan kinerja sistem hari ini.
                            </p>
                        </div>

                        <button onclick="document.getElementById('welcomeAlert').remove()" 
                            class="absolute top-3 right-3 text-blue-500 hover:text-blue-700 transition">
                            <i data-feather="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            @endif

            {{-- HEADER BANNER --}}
            <div class="bg-indigo-600 rounded-2xl p-6 sm:p-10 text-white shadow-xl shadow-indigo-200 mb-10 relative overflow-hidden">
                <div class="relative z-10">
                    <h1 class="text-3xl font-bold">Dashboard Admin</h1>
                    <p class="mt-2 text-indigo-100 max-w-xl">
                        Pantau seluruh aktivitas kepegawaian dan kelola data master sistem dari sini.
                    </p>
                </div>
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            </div>

            {{-- GRID: STATS + QUICK ACTIONS --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">

                {{-- STATISTIK SINGKAT (kiri 2 kolom) --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- 3 Stats --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Total Pegawai --}}
                        <div class="bg-white p-6 rounded-2xl shadow-sm border hover:shadow-md transition">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Total Pegawai</p>
                                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $data['total_pegawai'] }}</p>
                                </div>
                                <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                                    <i data-feather="users" class="w-6 h-6"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Total Bidang --}}
                        <div class="bg-white p-6 rounded-2xl shadow-sm border hover:shadow-md transition">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Aktivitas Hari Ini:</p>
                                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $data['aktivitas_hari_ini'] }}</p>

                                </div>
                                <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                                    <i data-feather="check-square" class="w-6 h-6"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Pegawai Dinilai --}}
                        <div class="bg-white p-6 rounded-2xl shadow-sm border hover:shadow-md transition">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Pegawai Dinilai</p>
                                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $data['laporan_baru'] }}</p>
                                </div>
                                <div class="p-3 bg-orange-50 text-orange-600 rounded-xl">
                                    <i data-feather="check-circle" class="w-6 h-6"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Aktivitas Terbaru --}}
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-800 mb-4">Aktivitas Terbaru</h3>

                        <div class="space-y-4">
                            @forelse ($data['aktivitas'] as $act)
                                <div class="flex items-start gap-4 p-3 hover:bg-gray-50 rounded-xl transition border border-transparent hover:border-gray-100">

                                    {{-- Avatar --}}
                                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs shadow-sm">
                                        {{ strtoupper(substr($act->pegawai->user->name ?? 'NA', 0, 2)) }}
                                    </div>

                                    {{-- Konten --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-800 truncate">
                                            {{ $act->pegawai->user->name ?? 'Pegawai' }}
                                        </p>
                                        <p class="text-sm text-gray-600 line-clamp-1" title="{{ $act->uraian_tugas }}">
                                            {{ $act->uraian_tugas }}
                                        </p>

                                        <div class="flex items-center gap-2 mt-1 text-xs text-gray-400">
                                            <i data-feather="clock" class="w-3 h-3"></i>
                                            {{ $act->created_at->diffForHumans() }}

                                            @php
                                                $statusColor = match($act->status) {
                                                    'disetujui' => 'bg-green-100 text-green-700',
                                                    'ditolak'   => 'bg-red-100 text-red-700',
                                                    'revisi'    => 'bg-orange-100 text-orange-700',
                                                    default     => 'bg-gray-100 text-gray-600',
                                                };
                                            @endphp

                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase {{ $statusColor }}">
                                                {{ $act->status }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center py-4 text-gray-400 text-sm">Belum ada aktivitas terbaru.</p>
                            @endforelse
                        </div>

                    </div>

                </div>

                {{-- QUICK ACTIONS (kanan 1 kolom) --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden sticky top-6">

                        {{-- Header Sidebar --}}
                        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-br from-indigo-600 to-indigo-700">
                            <h3 class="font-bold text-lg text-white flex items-center gap-2">
                                <i data-feather="zap" class="w-5 h-5 text-yellow-300"></i>
                                Menu Pintasan
                            </h3>
                            <p class="text-indigo-100 text-xs mt-1">Aksi cepat administrasi.</p>
                        </div>

                        <div class="p-5 space-y-4">

                            {{-- Tambah Pegawai --}}
                            <a href="{{ route('admin.pegawai.create') }}"
                                class="quick-item">
                                <div class="quick-icon bg-blue-50 text-blue-600">
                                    <i data-feather="user-plus"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="quick-title">Tambah Pegawai</h4>
                                    <p class="quick-sub">Tambahkan data pegawai baru.</p>
                                </div>
                            </a>

                            {{-- Tambah Bidang --}}
                            <a href="{{ route('admin.bidang.create') }}"
                                class="quick-item">
                                <div class="quick-icon bg-purple-50 text-purple-600">
                                    <i data-feather="layers"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="quick-title">Tambah Bidang</h4>
                                    <p class="quick-sub">Kelola struktur bidang instansi.</p>
                                </div>
                            </a>

                            {{-- Kelola Penilaian --}}
                            <a href="{{ route('admin.penilaian.index') }}"
                                class="quick-item">
                                <div class="quick-icon bg-green-50 text-green-600">
                                    <i data-feather="check-circle"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="quick-title">Kelola Penilaian</h4>
                                    <p class="quick-sub">Lihat & validasi penilaian pegawai.</p>
                                </div>
                            </a>

                            {{-- Aktivitas Pegawai --}}
                            <a href="{{ route('admin.aktivitas.index') }}"
                                class="quick-item">
                                <div class="quick-icon bg-orange-50 text-orange-600">
                                    <i data-feather="activity"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="quick-title">Aktivitas Pegawai</h4>
                                    <p class="quick-sub">Pantau aktivitas harian pegawai.</p>
                                </div>
                            </a>

                        </div>

                        <div class="px-6 py-4 bg-gray-50 border-t text-center text-xs text-gray-400">
                            Sistem Kinerja v1.0
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script>
        feather.replace();

        // CLASS UTILITY UNTUK QUICK ITEM
        document.querySelectorAll('.quick-item').forEach(el => {
            el.classList.add(
                'group','flex','items-center','p-4','bg-white','border','border-gray-200',
                'rounded-xl','shadow-sm','hover:shadow-md','transition','duration-200',
                'hover:border-indigo-400','hover:ring-1','hover:ring-indigo-400'
            );
        });

        document.querySelectorAll('.quick-icon').forEach(el => {
            el.classList.add(
                'flex-shrink-0','p-3','rounded-lg','group-hover:bg-indigo-600',
                'group-hover:text-white','transition-colors'
            );
        });

        document.querySelectorAll('.quick-title').forEach(el => {
            el.classList.add('font-bold','text-gray-800','group-hover:text-indigo-700');
        });

        document.querySelectorAll('.quick-sub').forEach(el => {
            el.classList.add('text-xs','text-gray-500','mt-0.5');
        });
    </script>

</x-app-layout>
