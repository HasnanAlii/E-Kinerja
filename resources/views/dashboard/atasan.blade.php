<x-app-layout>
    <div class="py-12 bg-gradient-to-b from-indigo-50 to-white min-h-screen">
        <div class="mx-auto sm:px-6 lg:px-8">

            {{-- ALERT SELAMAT DATANG --}}
            @if(session('welcome') || true)
                <div id="welcomeAlert" class="mb-6">
                    <div class="flex items-start gap-3 bg-blue-50 border border-blue-200 text-blue-800 px-5 py-4 rounded-xl shadow-sm relative">

                        {{-- Ikon --}}
                        <div class="flex-shrink-0">
                            <i data-feather="info" class="w-5 h-5 text-blue-600"></i>
                        </div>

                        {{-- Pesan --}}
                        <div class="flex-1">
                            <p class="font-semibold text-blue-900">Selamat datang, {{ Auth::user()->name }}! 🎉</p>
                            <p class="text-sm text-blue-700 mt-0.5">
                                Semoga hari Anda menyenangkan. Berikut ringkasan kinerja tim hari ini.
                            </p>
                        </div>

                        {{-- Tombol close --}}
                        <button onclick="document.getElementById('welcomeAlert').remove()" 
                            class="absolute top-3 right-3 text-blue-500 hover:text-blue-700 transition">
                            <i data-feather="x" class="w-4 h-4"></i>
                        </button>

                    </div>
                </div>
            @endif


            {{-- HEADER CARD --}}
            <div class="bg-indigo-600 rounded-2xl p-6 sm:p-10 text-white shadow-xl shadow-indigo-300 mb-10 relative">
                <div class="relative z-10">
                    <h1 class="text-3xl font-bold tracking-tight">Dashboard Atasan</h1>
                    <p class="mt-2 text-indigo-100 max-w-xl text-base">
                        Pantau kinerja, aktivitas, dan laporan bawahan Anda dengan lebih mudah.
                    </p>
                </div>

                <div class="absolute top-0 right-0 -mt-12 -mr-12 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            </div>

            {{-- STAT CARDS  --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">

                {{-- Pegawai Belum Dinilai --}}
                <div class="bg-white p-6 rounded-2xl border shadow-sm hover:shadow-indigo-300 
                            hover:border-indigo-300 transition-all duration-300 group">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500">Pegawai Belum Dinilai</p>
                            <p class="text-3xl font-extrabold text-indigo-700 mt-1">
                                {{ $data['menunggu_validasi'] }}
                            </p>
                        </div>

                        <div class="p-3 rounded-xl bg-indigo-50 text-indigo-600
                                    group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                            <i data-feather="alert-triangle" class="w-7 h-7"></i>
                        </div>
                    </div>
                </div>

                {{-- Aktivitas Hari Ini --}}
                <div class="bg-white p-6 rounded-2xl border shadow-sm hover:shadow-purple-300 
                            hover:border-purple-300 transition-all duration-300 group">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500">Aktivitas Hari Ini</p>
                            <p class="text-3xl font-extrabold text-gray-800 mt-1">
                                {{ $data['aktivitas_hari_ini'] }}
                            </p>
                        </div>

                        <div class="p-3 rounded-xl bg-purple-50 text-purple-600
                                    group-hover:bg-purple-600 group-hover:text-white transition-all duration-300">
                            <i data-feather="check-square" class="w-7 h-7"></i>
                        </div>
                    </div>
                </div>

                {{-- Total Bawahan --}}
                <div class="bg-white p-6 rounded-2xl border shadow-sm hover:shadow-blue-300 
                            hover:border-blue-300 transition-all duration-300 group">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500">Total Bawahan</p>
                            <p class="text-3xl font-extrabold text-blue-700 mt-1">
                                {{ $data['total_bawahan'] }}
                            </p>
                        </div>

                        <div class="p-3 rounded-xl bg-blue-50 text-blue-600
                                    group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                            <i data-feather="users" class="w-7 h-7"></i>
                        </div>
                    </div>
                </div>

                {{-- Rata-rata Kinerja --}}
                <div class="bg-white p-6 rounded-2xl border shadow-sm hover:shadow-sky-300 
                            hover:border-sky-300 transition-all duration-300 group">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500">Rata-rata Kinerja</p>
                            <p class="text-3xl font-extrabold text-sky-700 mt-1">
                                {{ $data['kinerja_tim'] }}
                            </p>
                        </div>

                        <div class="p-3 rounded-xl bg-sky-50 text-sky-600
                                    group-hover:bg-sky-600 group-hover:text-white transition-all duration-300">
                            <i data-feather="award" class="w-7 h-7"></i>
                        </div>
                    </div>
                </div>

            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-10">

                {{-- KOLOM KIRI - Aktivitas --}}
                <div class="xl:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">

                        {{-- Header --}}
                        <div class="px-6 py-5 border-b border-gray-100 bg-white flex items-center justify-between">
                            <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                                <span class="p-2 bg-indigo-100 text-indigo-600 rounded-lg">
                                    <i data-feather="activity" class="w-5 h-5"></i>
                                </span>
                                Aktivitas Terbaru Bawahan
                            </h3>
                        </div>

                        <div class="p-6">
                            <div class="space-y-4">

                                @forelse ($data['aktivitas'] as $act)
                                    <div class="group flex items-start gap-4 p-4 rounded-xl border border-gray-100 
                                                bg-gray-50 hover:bg-white hover:border-indigo-200 hover:shadow-md 
                                                transition-all duration-200">

                                        {{-- Avatar --}}
                                        <div class="flex-shrink-0">
                                            @if($act->pegawai->user->profile_photo_path)
                                                <img src="{{ $act->pegawai->user->profile_photo_url }}"
                                                     class="h-12 w-12 rounded-full object-cover border-2 border-white shadow-sm">
                                            @else
                                                <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center 
                                                            text-indigo-600 font-bold text-sm border-2 border-white shadow-sm">
                                                    {{ strtoupper(substr($act->pegawai->user->name, 0, 2)) }}
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Detail --}}
                                        <div class="flex-1 min-w-0">
                                            <div class="flex justify-between items-start">
                                                <p class="text-sm font-bold text-gray-900 truncate group-hover:text-indigo-700 transition-colors">
                                                    {{ $act->pegawai->user->name }}
                                                </p>
                                                <span class="text-[10px] font-medium text-gray-400">
                                                    {{ $act->created_at->diffForHumans() }}
                                                </span>
                                            </div>

                                            <p class="mt-1 text-sm text-gray-600 line-clamp-2">{{ $act->uraian_tugas }}</p>

                                            {{-- Status --}}
                                            @php
                                                $styles = [
                                                    'disetujui' => ['bg' => 'bg-green-50', 'text' => 'text-green-700', 'icon' => 'check-circle'],
                                                    'ditolak'   => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'icon' => 'x-circle'],
                                                    'revisi'    => ['bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'icon' => 'alert-circle'],
                                                    'default'   => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'icon' => 'clock']
                                                ];
                                                $st = $styles[$act->status] ?? $styles['default'];
                                            @endphp

                                            <span class="inline-flex items-center px-2.5 py-0.5 mt-3 rounded-md text-xs font-medium {{ $st['bg'] }} {{ $st['text'] }}">
                                                <i data-feather="{{ $st['icon'] }}" class="w-3 h-3 mr-1"></i>
                                                {{ ucfirst($act->status) }}
                                            </span>
                                        </div>
                                    </div>

                                @empty

                                    <div class="flex flex-col items-center justify-center py-10 text-center">
                                        <div class="bg-gray-50 p-4 rounded-full mb-3">
                                            <i data-feather="inbox" class="w-8 h-8 text-gray-300"></i>
                                        </div>
                                        <p class="text-gray-500 text-sm font-medium">Belum ada aktivitas terbaru.</p>
                                    </div>

                                @endforelse

                            </div>
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN - QUICK ACTIONS --}}
                <div class="xl:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden sticky top-6">

                        {{-- Header --}}
                        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-br from-indigo-600 to-indigo-700">
                            <h3 class="font-bold text-lg text-white flex items-center gap-2">
                                <i data-feather="zap" class="w-5 h-5 text-yellow-300"></i>
                                Menu Pintasan
                            </h3>
                            <p class="text-indigo-100 text-xs mt-1">Aksi cepat untuk pengelolaan kinerja.</p>
                        </div>

                        <div class="p-5 space-y-4">

                            {{-- Tambah SKP --}}
                            <a href="{{ route('atasan.skp.create') }}"
                               class="group flex items-center p-4 bg-white border border-gray-200 rounded-xl 
                                      shadow-sm hover:shadow-md hover:border-indigo-500 hover:ring-1 hover:ring-indigo-500 
                                      transition-all duration-200">
                                <div class="flex-shrink-0 p-3 bg-indigo-50 text-indigo-600 rounded-lg 
                                            group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                    <i data-feather="plus-circle" class="w-6 h-6"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-bold text-gray-800 group-hover:text-indigo-700">Tambah SKP</h4>
                                    <p class="text-xs text-gray-500 mt-0.5">Buat sasaran kinerja baru.</p>
                                </div>
                            </a>

                            {{-- Unduh Penilaian --}}
                            <a href="{{ route('atasan.penilaian.download_all') }}"
                               class="group flex items-center p-4 bg-white border border-gray-200 rounded-xl 
                                      shadow-sm hover:shadow-md hover:border-green-500 hover:ring-1 hover:ring-green-500 
                                      transition-all duration-200">
                                <div class="flex-shrink-0 p-3 bg-green-50 text-green-600 rounded-lg 
                                            group-hover:bg-green-600 group-hover:text-white transition-colors">
                                    <i data-feather="download" class="w-6 h-6"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-bold text-gray-800 group-hover:text-green-700">Laporan Penilaian</h4>
                                    <p class="text-xs text-gray-500 mt-0.5">Unduh rekap nilai pegawai.</p>
                                </div>
                            </a>

                        </div>

                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                            <p class="text-xs text-center text-gray-400">Sistem Kinerja v1.0</p>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof feather !== 'undefined') feather.replace();
        });
    </script>
</x-app-layout>
