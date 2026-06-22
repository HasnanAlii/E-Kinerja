<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Manajemen Kehadiran
        </h2>
    </x-slot>

    <div class="py-12 mx-auto sm:px-6 lg:px-8">
        <!-- DASHBOARD MONITORING KINERJA -->
        <div x-data="monitoringApp()"
             x-init="init()"
            class="mb-8 bg-white shadow-xl sm:rounded-2xl overflow-hidden border border-gray-100">
            <div
                class="px-6 py-6 border-b border-gray-200 bg-gray-50 flex flex-col xl:flex-row xl:items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-red-50 text-red-600 rounded-2xl shadow-sm ring-1 ring-red-100">
                        <i data-feather="video" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 tracking-tight">CCTV Monitoring Kinerja (Real-time)</h3>
                        <p class="text-sm text-gray-500 mt-0.5">
                            Pengawasan otomatis menggunakan integrasi AI Kamera Pengawas (YOLOv8).
                        </p>
                    </div>
                </div>
                <div class="flex-shrink-0 flex flex-wrap items-center gap-3">
                    {{-- Zona not configured warning --}}
                    <div x-show="!hasZonas" class="flex items-center gap-2 px-3 py-2 bg-amber-50 border border-amber-200 rounded-xl">
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span class="text-xs font-semibold text-amber-700">Zona belum dikonfigurasi</span>
                    </div>

                    {{-- Live detection counter --}}
                    <div x-show="isCameraOpen" class="flex items-center gap-2 px-4 py-2 bg-emerald-50 border border-emerald-200 rounded-xl">
                        <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></span>
                        <span class="text-sm font-bold text-emerald-700">
                            <span x-text="liveCount">0</span> Orang Terdeteksi
                        </span>
                    </div>
                    <div x-show="!isCameraOpen" class="flex items-center gap-2 px-4 py-2 bg-gray-100 border border-gray-200 rounded-xl">
                        <i data-feather="users" class="w-4 h-4 text-gray-500"></i>
                        <span class="text-sm font-semibold text-gray-600">{{ $pegawais->count() }} Pegawai Terdaftar</span>
                    </div>

                    {{-- Atur Zona button --}}
                    <button @click="openZonaSetup()"
                        class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-md transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i data-feather="map-pin" class="w-4 h-4 mr-2"></i>
                        Atur Zona
                    </button>

                    {{-- Mulai/Stop Pengawasan button --}}
                    <button
                        @click="if(!isCameraOpen) { startCamera(); } else { stopCamera(); }"
                        :class="isCameraOpen ? 'bg-slate-700 hover:bg-slate-800' : 'bg-red-600 hover:bg-red-700'"
                        class="inline-flex items-center justify-center px-5 py-2.5 text-white text-sm font-bold rounded-xl shadow-md transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <i data-feather="camera" class="w-4 h-4 mr-2" x-show="!isCameraOpen"></i>
                        <i data-feather="video-off" class="w-4 h-4 mr-2" x-show="isCameraOpen" style="display: none;"></i>
                        <span x-text="isCameraOpen ? 'Hentikan Pengawasan' : 'Mulai Pengawasan'">Mulai Pengawasan</span>
                    </button>
                </div>

            </div>

            <!-- Video Container (Hidden by default) -->
            <div x-show="isCameraOpen" x-transition style="display: none;"
                class="p-6 bg-slate-900 flex justify-center border-b border-gray-200">
                <div
                    class="relative rounded-2xl overflow-hidden border-4 border-slate-800 shadow-2xl max-w-4xl w-full aspect-video bg-black flex items-center justify-center">
                    
                    <!-- Hidden Canvas & Video -->
                    <canvas x-ref="canvasElement" class="hidden"></canvas>
                    <video x-ref="videoElement" autoplay playsinline muted class="hidden"></video>
                    
                    <!-- Annotated Stream Display -->
                    <img x-ref="annotatedImg" x-show="$refs.annotatedImg && $refs.annotatedImg.src && $refs.annotatedImg.src !== window.location.href" class="w-full h-full object-contain" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" style="display:none" />

                    <!-- Overlay UI -->
                    <div class="absolute top-4 left-4 flex flex-col gap-2">
                        <span class="px-3 py-1.5 bg-red-600 text-white text-xs font-bold rounded-lg flex items-center shadow-lg uppercase tracking-widest">
                            <span class="w-2.5 h-2.5 bg-white rounded-full mr-2 animate-pulse"></span> Live AI
                        </span>
                        <span
                            class="px-3 py-1.5 bg-black/60 backdrop-blur-md text-emerald-400 border border-emerald-500/30 text-xs font-medium rounded-lg shadow-lg flex items-center">
                            <i data-feather="cpu" class="w-3 h-3 mr-1.5"></i> YOLOv8 Active
                        </span>
                    </div>

                    <!-- Scanning Animation Mock -->
                    <div class="absolute inset-0 pointer-events-none border border-emerald-500/20"
                        style="background: linear-gradient(to bottom, transparent 50%, rgba(16, 185, 129, 0.05) 51%, transparent 100%); background-size: 100% 4px;">
                    </div>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Stat 1: Hadir -->
                <div
                    class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl p-5 text-white shadow-lg relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-indigo-100 text-sm font-medium mb-1">Total Pegawai Hadir</p>
                        <h4 class="text-4xl font-bold">
                            <span x-text="stats.total_hadir">{{ $data->where('jenis', 'hadir')->count() }}</span>
                            <span class="text-base font-normal text-indigo-200">Orang</span>
                        </h4>
                    </div>
                    <i data-feather="users"
                        class="w-24 h-24 absolute -bottom-4 -right-4 text-indigo-400 opacity-30"></i>
                </div>

                <!-- Stat 2: Keluar Ruangan -->
                <div
                    class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-2xl p-5 text-white shadow-lg relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-orange-100 text-sm font-medium mb-1">Terdeteksi Keluar Ruangan</p>
                        <h4 class="text-4xl font-bold" x-text="stats.total_keluar + ' Orang'">0 Orang</h4>
                    </div>
                    <i data-feather="log-out"
                        class="w-24 h-24 absolute -bottom-4 -right-4 text-orange-400 opacity-30"></i>
                </div>

                <!-- Stat 3: Rata-rata Stay -->
                <div
                    class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-5 text-white shadow-lg relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-emerald-100 text-sm font-medium mb-1">Rata-rata Stay di Meja</p>
                        <h4 class="text-4xl font-bold" x-text="stats.avg_stay_str">0j 0m</h4>
                    </div>
                    <i data-feather="check-circle"
                        class="w-24 h-24 absolute -bottom-4 -right-4 text-emerald-400 opacity-30"></i>
                </div>
            </div>

            <!-- Detail Per Pegawai -->
            <div class="px-6 pb-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-md font-bold text-gray-800">Monitoring Status Semua Pegawai</h4>
                    <span class="text-xs text-gray-400">Update otomatis setiap 5 detik</span>
                </div>
                <div class="overflow-x-auto bg-white border border-gray-200 rounded-xl">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pegawai</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jabatan</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Zona / Posisi Meja</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status CCTV</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Durasi di Meja</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Terakhir Terlihat</th>
                                {{-- <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th> --}}
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="log in detectionLogs" :key="log.pegawai_id">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    {{-- Pegawai column --}}
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-9 w-9 rounded-full flex items-center justify-center border-2 relative"
                                                :class="log.status_raw === 'di_meja' ? 'bg-emerald-50 border-emerald-200' : (log.status_raw === 'keluar_ruangan' ? 'bg-red-50 border-red-200' : 'bg-gray-100 border-gray-200')">
                                                <i data-feather="user" class="w-4 h-4"
                                                   :class="log.status_raw === 'di_meja' ? 'text-emerald-600' : (log.status_raw === 'keluar_ruangan' ? 'text-red-500' : 'text-gray-400')"></i>
                                                {{-- Zone color dot --}}
                                                <span x-show="log.has_zone"
                                                    class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full border-2 border-white"
                                                    :style="'background-color:' + (log.zona_warna || '#aaa')"></span>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-semibold text-gray-900" x-text="log.nama"></p>
                                                <p x-show="!log.has_zone" class="text-xs text-amber-500">Zona belum diatur</p>
                                            </div>
                                        </div>
                                    </td>
                                    {{-- Jabatan column --}}
                                    <td class="px-4 py-3">
                                        <p class="text-xs text-gray-500" x-text="log.jabatan"></p>
                                    </td>
                                    {{-- Zona column --}}
                                    <td class="px-4 py-3 text-center">
                                        <span x-show="log.has_zone"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border"
                                            :style="'background-color:' + (log.zona_warna||'#eee') + '22; border-color:' + (log.zona_warna||'#aaa') + '66; color:' + (log.zona_warna||'#555')">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                                            <span x-text="log.zona_label || '-'"></span>
                                        </span>
                                        <span x-show="!log.has_zone" class="text-xs text-gray-400">—</span>
                                    </td>
                                    {{-- Status CCTV column --}}
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                                            :class="log.status_raw === 'di_meja' 
                                                ? 'bg-emerald-100 text-emerald-800' 
                                                : (log.status_raw === 'keluar_ruangan' 
                                                    ? 'bg-red-100 text-red-800' 
                                                    : 'bg-gray-100 text-gray-500')">
                                            <span class="w-1.5 h-1.5 rounded-full"
                                                :class="log.status_raw === 'di_meja' ? 'bg-emerald-500 animate-pulse' : (log.status_raw === 'keluar_ruangan' ? 'bg-red-500' : 'bg-gray-400')"
                                            ></span>
                                            <span x-text="log.status_cctv"></span>
                                        </span>
                                    </td>
                                    {{-- Live Ticking Duration --}}
                                    <td class="px-4 py-3 text-center">
                                        <div class="inline-flex items-center gap-1.5">
                                            {{-- Pulsing dot when live --}}
                                            <span x-show="log.status_raw === 'di_meja'"
                                                class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping flex-shrink-0"
                                                style="animation-duration: 1.5s"></span>
                                            <span class="text-sm font-mono font-semibold"
                                                :class="log.status_raw === 'di_meja' ? 'text-emerald-700' : 'text-gray-500'"
                                                x-text="liveDuration(log)">—</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm text-gray-500" x-text="log.waktu_selesai"></td>

                                    {{-- <td class="px-4 py-3 text-center text-sm">
                                        <button onclick="alert('Fitur timeline pergerakan sedang dalam pengembangan.')"
                                            class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                                            Timeline
                                        </button>
                                    </td> --}}
                                </tr>
                            </template>
                            <tr x-show="detectionLogs.length === 0">
                                <td colspan="7" class="px-4 py-10 text-center">
                                    <i data-feather="video" class="w-10 h-10 text-gray-200 mx-auto mb-3"></i>
                                    <p class="text-sm font-medium text-gray-400">Klik <strong class="text-gray-600">Mulai Pengawasan</strong> untuk memulai monitoring.</p>
                                    <p class="text-xs text-gray-400 mt-1">Pastikan zona posisi duduk sudah dikonfigurasi terlebih dahulu.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a href="{{ route('atasan.kehadiran.index') }}"
                        class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-base flex items-center">
                        <i data-feather="clock" class="w-5 h-5 mr-2"></i>
                        Rekap Kehadiran
                    </a>

                    <a href="{{ route('atasan.izin.index') }}"
                        class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-base flex items-center">
                        <i data-feather="file-text" class="w-5 h-5 mr-2"></i>
                        Izin / Sakit / Cuti
                    </a>
                </nav>
            </div>
        </div>

        @if (session('success'))
            <div
                class="mb-4 p-4 flex items-center bg-green-100 text-green-700 rounded-lg border border-green-300 text-base">
                <i data-feather="check-circle" class="w-5 h-5 mr-3 text-green-500"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow-xl sm:rounded-2xl overflow-hidden border border-gray-100">

            {{-- HEADER CARD --}}
            <div
                class="px-6 py-6 border-b border-gray-200 bg-gray-50 flex flex-col xl:flex-row xl:items-center justify-between gap-6">

                {{-- LEFT: ICON & TITLE --}}
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl shadow-sm ring-1 ring-indigo-100">
                        <i data-feather="clock" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 tracking-tight">Rekap Kehadiran Pegawai</h3>
                        <p class="text-sm text-gray-500 mt-0.5">
                            Lihat dan kelola data kehadiran berdasarkan pegawai dan rentang tanggal.
                        </p>
                    </div>
                </div>

                {{-- RIGHT: FILTER & EXPORT --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full xl:w-auto">

                    {{-- FILTER FORM --}}
                    <form method="GET" class="flex flex-col sm:flex-row gap-3 w-full xl:w-auto">

                        {{-- Filter Pegawai --}}
                        <div class="relative w-full sm:w-56">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i data-feather="user" class="w-4 h-4"></i>
                            </div>
                            <select name="pegawai_id"
                                class="w-full pl-10 pr-8 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition cursor-pointer appearance-none hover:bg-gray-100">
                                <option value="">Semua Pegawai</option>
                                @foreach ($pegawais as $p)
                                    <option value="{{ $p->id }}" @selected(request('pegawai_id') == $p->id)>
                                        {{ $p->user->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div
                                class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                <i data-feather="chevron-down" class="w-4 h-4"></i>
                            </div>
                        </div>

                        {{-- Filter Tanggal --}}
                        <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-xl p-1 shadow-sm">
                            <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                                class="py-1.5 px-2 bg-transparent border-none text-sm focus:ring-0 cursor-pointer">

                            <span class="text-gray-400 text-xs font-medium">s/d</span>

                            <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                                class="py-1.5 px-2 bg-transparent border-none text-sm focus:ring-0 cursor-pointer">
                        </div>

                        {{-- Tombol Filter --}}
                        <button type="submit"
                            class="inline-flex items-center justify-center bg-indigo-600 text-white px-5 py-2.5 rounded-xl shadow-md hover:bg-indigo-700 transition">
                            <i data-feather="filter" class="w-4 h-4 mr-2"></i>
                            Filter
                        </button>
                    </form>

                    {{-- EXPORT PDF --}}
                    <a href="{{ route('laporan.kehadiran.perpegawai') . '?' . http_build_query(request()->only(['pegawai_id', 'tanggal_dari', 'tanggal_sampai'])) }}"
                        target="_blank"
                        class="inline-flex items-center justify-center px-5 py-2.5 bg-red-600 text-white text-sm font-medium rounded-xl shadow-md hover:bg-red-700 transition">
                        <i data-feather="file-text" class="w-4 h-4 mr-2"></i>
                        Laporan PDF
                    </a>

                </div>
            </div>


            <div class="bg-white overflow-hidden shadow-xl sm:rounded-b-lg">
                <div class=" bg-white border-b border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal</th>
                                    <th
                                        class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Pegawai</th>
                                    <th
                                        class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Masuk</th>
                                    <th
                                        class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Pulang</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($data as $row)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-base text-gray-700">
                                            {{ \Carbon\Carbon::parse($row->tanggal)->isoFormat('D MMM YYYY') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-base font-medium text-gray-900">
                                            {{ $row->pegawai->user->name }}</td>

                                        {{-- Absen Masuk --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if ($row->jenis != 'hadir')
                                                <span class="text-base text-blue-600 font-semibold">
                                                    {{ ucfirst($row->jenis) }}
                                                </span>
                                            @else
                                                <div class="text-base text-gray-900">
                                                    {{ $row->check_in ? \Carbon\Carbon::parse($row->check_in)->format('H:i') : '-' }}
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Absen Pulang --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if ($row->jenis != 'hadir')
                                                <span class="text-base text-blue-600 font-semibold">
                                                    {{ ucfirst($row->jenis) }}
                                                </span>
                                            @else
                                                @if ($row->check_out)
                                                    <div class="text-base text-gray-900">
                                                        {{ \Carbon\Carbon::parse($row->check_out)->format('H:i') }}
                                                    </div>
                                                @else
                                                    <span class="text-base text-gray-400 italic">Belum Absen
                                                        Pulang</span>
                                                @endif
                                            @endif
                                        </td>
                                        {{-- <td class="px-6 py-4 whitespace-nowrap text-base text-gray-700">{{ $row->keterangan ?? '-' }}</td>
                                    <td class="px-6 py-4 text-right text-base">
                                        <a href="{{ route('atasan.kehadiran.show', $row->id) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-900 font-medium">
                                            <i data-feather="edit" class="w-4 h-4 mr-1"></i>
                                            Detail / Koreksi
                                        </a>
                                    </td> --}}
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-6 text-center text-base text-gray-500">Belum
                                            ada data kehadiran.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        @if ($data->hasPages())
                            <div class="mt-6 p-4 bg-gray-50 border-t rounded-b-lg">
                                {{ $data->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

    </div>{{-- /py-12 --}}

    {{-- ZONA SETUP MODAL --}}
    <div x-data="zonaSetupApp()" x-init="initZona()" @open-zona-modal.window="openModal()">
        <div x-show="open" x-transition class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm" style="display:none" @click="closeModal()"></div>
        <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden flex flex-col">
                <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold">Konfigurasi Zona Posisi Duduk</h3>
                        <p class="text-indigo-200 text-sm mt-0.5">Pilih pegawai &rarr; klik-drag pada area kamera untuk menandai meja mereka</p>
                    </div>
                    <button @click="closeModal()" class="p-2 rounded-xl hover:bg-white/20 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="flex flex-1 overflow-hidden" style="min-height:420px">
                    <div class="flex-1 p-5 flex flex-col bg-slate-900 overflow-hidden">
                        <div class="relative flex-1" style="min-height:300px">
                            <canvas id="zonaDrawCanvas"
                                :style="selectedPegawai ? 'cursor:crosshair' : 'cursor:not-allowed'"
                                class="w-full h-full rounded-xl border-2 border-slate-700 select-none"
                                @mousedown="startDraw($event)" @mousemove="updateDraw($event)"
                                @mouseup="endDraw($event)" @mouseleave="cancelDraw()" style="min-height:300px">
                            </canvas>
                            <div x-show="!selectedPegawai" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <div class="bg-black/70 text-white text-center px-6 py-4 rounded-xl">
                                    <p class="font-semibold">&larr; Pilih pegawai dari daftar kanan</p>
                                    <p class="text-sm text-gray-400 mt-1">lalu klik-drag untuk menandai meja</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center gap-3">
                            <p class="flex-1 text-xs text-slate-400" x-text="selectedPegawai ? 'Menggambar zona untuk: ' + selectedPegawai.pegawai_name : 'Pilih pegawai di kanan'"></p>
                            <button x-show="currentRect" @click="saveZone()" :disabled="saving"
                                class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold rounded-xl transition disabled:opacity-50">
                                <span x-text="saving ? 'Menyimpan...' : '&#10003; Simpan Zona'"></span>
                            </button>
                        </div>
                    </div>
                    <div class="w-64 border-l border-gray-200 flex flex-col overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Daftar Pegawai</p>
                        </div>
                        <div class="flex-1 overflow-y-auto p-3 space-y-2">
                            <template x-for="p in employees" :key="p.pegawai_id">
                                <button @click="selectEmployee(p)"
                                    :class="selectedPegawai?.pegawai_id === p.pegawai_id ? 'ring-2 ring-indigo-500 bg-indigo-50' : 'hover:bg-gray-50'"
                                    class="w-full px-3 py-3 rounded-xl text-left transition border border-gray-100">
                                    <div class="flex items-center gap-3">
                                        <div class="w-4 h-4 rounded-full flex-shrink-0 border border-white shadow" :style="'background-color:' + p.warna"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-800 truncate" x-text="p.pegawai_name"></p>
                                            <p class="text-xs text-gray-400 truncate" x-text="p.jabatan || '-'"></p>
                                        </div>
                                        <span x-show="p.has_zone" class="text-emerald-500 text-xs font-bold flex-shrink-0">&#10003;</span>
                                    </div>
                                    <p x-show="p.has_zone" class="mt-1 ml-7 text-xs text-indigo-600 font-medium" x-text="p.zona_label || 'Zona terdaftar'"></p>
                                </button>
                            </template>
                        </div>
                        <div class="p-3 border-t border-gray-200 space-y-2">
                            <button x-show="selectedPegawai?.has_zone" @click="deleteZone()" :disabled="deleting"
                                class="w-full px-3 py-2 bg-red-50 text-red-600 hover:bg-red-100 text-xs font-bold rounded-xl transition border border-red-200 disabled:opacity-50">
                                <span x-text="deleting ? 'Menghapus...' : '&#128465; Hapus Zona ' + (selectedPegawai?.pegawai_name || '')"></span>
                            </button>
                            <p x-show="statusMsg" class="text-xs text-center font-medium"
                               :class="statusOk ? 'text-emerald-600' : 'text-red-600'" x-text="statusMsg"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@include('atasan.kehadiran.script')
</x-app-layout>



