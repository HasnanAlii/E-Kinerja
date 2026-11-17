<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Monitoring Kamera
        </h2>
    </x-slot>

    <div class="py-6 mx-auto sm:px-6 lg:px-8">
        <!-- DASHBOARD MONITORING KEHADIRAN -->
        <div x-data="monitoringApp()"
             x-init="init()"
             class="space-y-6">

            {{-- HERO HEADER MONITORING KAMERA --}}
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 via-blue-600 to-indigo-700 p-6 text-white shadow-lg">
                <div class="relative z-10 flex flex-col xl:flex-row xl:items-center justify-between gap-5">
                    <div class="flex items-start sm:items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur-xs border border-white/25 flex items-center justify-center shadow-md shrink-0">
                            <i data-feather="video" class="w-7 h-7 text-white"></i>
                        </div>
                        <div>
                            <div class="inline-flex items-center gap-2 px-3 py-0.5 rounded-full bg-white/15 text-xs font-semibold tracking-wide mb-1.5">
                                <span class="w-2 h-2 rounded-full"
                                      :class="isCameraOpen ? 'bg-emerald-400 animate-ping' : 'bg-indigo-200'"></span>
                                <span x-text="isCameraOpen ? 'LIVE ENGINE AKTIF (YOLOv8 + CNN)' : 'SIAP PENGAWASAN KAMERA'">SIAP PENGAWASAN KAMERA</span>
                            </div>
                            <h3 class="text-2xl font-extrabold tracking-tight">
                                Monitoring Kehadiran Kamera Real-Time
                            </h3>
                            <p class="text-sm text-indigo-100 mt-0.5">
                                PT Pou Yuen Indonesia — Deteksi otomatis keberadaan karyawan di zona kerja menuju target standar 8 jam.
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 shrink-0">
                        {{-- Zona not configured warning --}}
                        <div x-show="!hasZonas" class="flex items-center gap-2 px-3.5 py-2 bg-amber-400/20 border border-amber-300/40 backdrop-blur-xs rounded-xl text-amber-100">
                            <svg class="w-4 h-4 text-amber-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span class="text-xs font-bold">Zona belum diatur</span>
                        </div>

                        {{-- Live detection counter --}}
                        <div x-show="isCameraOpen" class="flex items-center gap-2 px-4 py-2 bg-emerald-500/25 border border-emerald-300/40 backdrop-blur-xs rounded-xl">
                            <span class="w-2.5 h-2.5 bg-emerald-300 rounded-full animate-pulse"></span>
                            <span class="text-sm font-bold text-white">
                                <span x-text="liveCount">0</span> Karyawan Terdeteksi
                            </span>
                        </div>
                        <div x-show="!isCameraOpen" class="flex items-center gap-2 px-4 py-2 bg-white/15 border border-white/20 backdrop-blur-xs rounded-xl">
                            <i data-feather="users" class="w-4 h-4 text-indigo-100"></i>
                            <span class="text-sm font-semibold text-white">{{ $pegawais->count() }} Karyawan Terdaftar</span>
                        </div>

                        {{-- Atur Zona button --}}
                        <button @click="openZonaSetup()"
                            class="inline-flex items-center justify-center px-4 py-2.5 bg-white text-indigo-700 hover:bg-indigo-50 text-sm font-bold rounded-xl shadow-md transition focus:outline-none">
                            <i data-feather="map-pin" class="w-4 h-4 mr-2"></i>
                            Atur Zona
                        </button>

                        {{-- Mulai/Stop Pengawasan button --}}
                        <button
                            @click="if(!isCameraOpen) { startCamera(); } else { stopCamera(); }"
                            :class="isCameraOpen ? 'bg-rose-600 hover:bg-rose-700 border-rose-400/40' : 'bg-emerald-500 hover:bg-emerald-600 border-emerald-400/40'"
                            class="inline-flex items-center justify-center px-5 py-2.5 text-white text-sm font-bold rounded-xl shadow-md border transition focus:outline-none">
                            <i data-feather="camera" class="w-4 h-4 mr-2" x-show="!isCameraOpen"></i>
                            <i data-feather="video-off" class="w-4 h-4 mr-2" x-show="isCameraOpen" style="display: none;"></i>
                            <span x-text="isCameraOpen ? 'Hentikan Monitoring' : 'Mulai Monitoring'">Mulai Monitoring</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Video Container (Full Width & Large + Fullscreen Support) -->
            <div x-show="isCameraOpen" x-transition style="display: none;"
                 class="bg-slate-950 rounded-2xl overflow-hidden shadow-xl border-2 border-indigo-500/30">
                <div class="px-5 py-3 bg-slate-900 border-b border-slate-800 flex items-center justify-between text-xs text-slate-300">
                    <div class="flex items-center gap-2.5">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-500/20 text-rose-400 border border-rose-500/30 font-bold">
                            <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                            LIVE CAM
                        </span>
                        <span class="font-medium text-slate-300">Deteksi Zona Aktif — Backbone CNN &amp; YOLOv8</span>
                    </div>
                    <span class="text-slate-400">Klik tombol Layar Penuh di kanan bawah video untuk memperbesar tampilan</span>
                </div>

                <div x-ref="videoWrapper"
                    class="relative w-full h-[78vh] min-h-[520px] bg-black flex items-center justify-center overflow-hidden">
                    
                    <!-- Hidden Capture Canvas -->
                    <canvas x-ref="canvasElement" class="hidden"></canvas>

                    <!-- Native Smooth 60FPS Video Stream (Full Cover) -->
                    <video x-ref="videoElement" autoplay playsinline muted
                        x-show="!isSimulating"
                        class="w-full h-full object-cover"></video>
                    
                    <!-- Annotated Stream Display (Fallback for Simulation Mode) -->
                    <img x-ref="annotatedImg"
                        x-show="isSimulating && $refs.annotatedImg && $refs.annotatedImg.src"
                        class="w-full h-full object-cover"
                        src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                        style="display:none" />

                    <!-- Smooth Overlay Canvas for Zones & AI Bounding Boxes -->
                    <canvas x-ref="overlayCanvas"
                        x-show="!isSimulating"
                        class="absolute inset-0 w-full h-full pointer-events-none"></canvas>

                    <!-- Fullscreen Toggle Button -->
                    <button type="button"
                        @click="toggleFullscreen()"
                        class="absolute bottom-4 right-4 z-20 inline-flex items-center gap-2 px-4 py-2 bg-black/70 hover:bg-indigo-600 text-white text-xs font-semibold rounded-xl border border-white/20 backdrop-blur-md shadow-lg transition">
                        <svg x-show="!isFullscreen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                        </svg>
                        <svg x-show="isFullscreen" style="display:none" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m7 5l5-5m0 0v4m0-4h-4M9 15l-5 5m0 0v-4m0 4h4m7-5l5 5m0 0v-4m0 4h-4"/>
                        </svg>
                        <span x-text="isFullscreen ? 'Keluar Layar Penuh' : 'Layar Penuh'">Layar Penuh</span>
                    </button>

                    {{-- DISABLED: Overlay UI (LIVE AI & YOLOv8 Active)
                    <div class="absolute top-4 left-4 flex flex-col gap-2">
                        <span class="px-3 py-1.5 bg-red-600 text-white text-xs font-bold rounded-lg flex items-center shadow-lg uppercase tracking-widest">
                            <span class="w-2.5 h-2.5 bg-white rounded-full mr-2 animate-pulse"></span> Live AI
                        </span>
                        <span
                            class="px-3 py-1.5 bg-black/60 backdrop-blur-md text-emerald-400 border border-emerald-500/30 text-xs font-medium rounded-lg shadow-lg flex items-center">
                            <i data-feather="cpu" class="w-3 h-3 mr-1.5"></i> YOLOv8 Active
                        </span>
                    </div>
                    --}}

                    <!-- Scanning Animation Mock -->
                    <div class="absolute inset-0 pointer-events-none border border-emerald-500/20"
                        style="background: linear-gradient(to bottom, transparent 50%, rgba(16, 185, 129, 0.04) 51%, transparent 100%); background-size: 100% 4px;">
                    </div>
                </div>
            </div>

            {{-- 3 KARTU RINGKASAN STATISTIK MONITORING --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Stat 1: Hadir -->
                <div class="bg-white rounded-2xl p-5 border border-indigo-100 shadow-xs flex items-center justify-between relative overflow-hidden">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white flex items-center justify-center shadow-md shadow-indigo-500/25 shrink-0">
                            <i data-feather="user-check" class="w-7 h-7"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Karyawan Hadir</p>
                            <h4 class="text-3xl font-extrabold text-slate-800 mt-0.5">
                                <span x-text="stats.total_hadir">{{ $data->where('jenis', 'hadir')->count() }}</span>
                                <span class="text-sm font-semibold text-slate-400">Orang</span>
                            </h4>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-600 border border-indigo-100">
                        Hari Ini
                    </span>
                </div>

                <!-- Stat 2: Keluar Ruangan -->
                <div class="bg-white rounded-2xl p-5 border border-amber-100 shadow-xs flex items-center justify-between relative overflow-hidden">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-500 text-white flex items-center justify-center shadow-md shadow-amber-500/25 shrink-0">
                            <i data-feather="log-out" class="w-7 h-7"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Sedang Di Luar Zona</p>
                            <h4 class="text-3xl font-extrabold text-amber-600 mt-0.5" x-text="stats.total_keluar + ' Orang'">0 Orang</h4>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                        Pantauan
                    </span>
                </div>

                <!-- Stat 3: Rata-rata Stay -->
                <div class="bg-white rounded-2xl p-5 border border-emerald-100 shadow-xs flex items-center justify-between relative overflow-hidden">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center shadow-md shadow-emerald-500/25 shrink-0">
                            <i data-feather="clock" class="w-7 h-7"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Rata-Rata Durasi Kerja</p>
                            <h4 class="text-3xl font-extrabold text-emerald-600 mt-0.5" x-text="stats.avg_stay_str">0j 0m</h4>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Target 8 Jam
                    </span>
                </div>
            </div>

            <!-- Detail Per Karyawan -->
            <div class="bg-white shadow-sm rounded-2xl overflow-hidden border border-slate-200/90">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/70 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                        <h4 class="text-base font-extrabold text-slate-800">Status Real-Time Seluruh Karyawan</h4>
                    </div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100 text-xs font-semibold">
                        <i data-feather="refresh-cw" class="w-3.5 h-3.5"></i>
                        <span>Sinkronisasi otomatis setiap 5 detik</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-100/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-5 py-3.5 text-left">Nama Karyawan</th>
                                @if(false) {{-- DISABLED: jabatan --}}
<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jabatan</th>
@endif
                                <th class="px-4 py-3.5 text-center">Zona / Posisi Kerja</th>
                                <th class="px-4 py-3.5 text-center">Status Kamera</th>
                                <th class="px-4 py-3.5 text-center">Durasi Kerja Aktif</th>
                                <th class="px-4 py-3.5 text-center">Terakhir Terlihat</th>
                                <th class="px-4 py-3.5 text-center">Progres 8 Jam</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="log in detectionLogs" :key="log.pegawai_id">
                                <tr class="hover:bg-indigo-50/40 transition-colors">
                                    {{-- Karyawan column --}}
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-xl flex items-center justify-center border relative font-bold text-xs shadow-2xs"
                                                :class="log.status_raw === 'di_meja'
                                                    ? 'bg-emerald-50 border-emerald-200 text-emerald-700'
                                                    : (log.status_raw === 'keluar_ruangan'
                                                        ? 'bg-rose-50 border-rose-200 text-rose-600'
                                                        : 'bg-slate-100 border-slate-200 text-slate-500')">
                                                <span x-text="(log.nama || 'K').substring(0, 2).toUpperCase()"></span>
                                                {{-- Zone color dot --}}
                                                <span x-show="log.has_zone"
                                                    class="absolute -bottom-1 -right-1 w-3.5 h-3.5 rounded-full border-2 border-white shadow-2xs"
                                                    :style="'background-color:' + (log.zona_warna || '#aaa')"></span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-800" x-text="log.nama"></p>
                                                <p x-show="!log.has_zone" class="text-[11px] font-semibold text-amber-600">Zona belum diatur</p>
                                                <p x-show="log.has_zone" class="text-[11px] text-slate-400">Terdaftar di Zona Kamera</p>
                                            </div>
                                        </div>
                                    </td>
                                    {{-- Jabatan column --}}
                                    @if(false) {{-- DISABLED: jabatan --}}
<td class="px-4 py-3">
                                        <p class="text-xs text-gray-500" x-text="log.jabatan"></p>
                                    </td>
@endif
                                    {{-- Zona column --}}
                                    <td class="px-4 py-3.5 text-center">
                                        <span x-show="log.has_zone"
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold border"
                                            :style="'background-color:' + (log.zona_warna||'#eee') + '18; border-color:' + (log.zona_warna||'#aaa') + '55; color:' + (log.zona_warna||'#444')">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                                            <span x-text="log.zona_label || '-'"></span>
                                        </span>
                                        <span x-show="!log.has_zone" class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-400">Belum Ada</span>
                                    </td>
                                    {{-- Status CCTV column --}}
                                    <td class="px-4 py-3.5 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border"
                                            :class="log.status_raw === 'di_meja' 
                                                ? 'bg-emerald-50 text-emerald-700 border-emerald-200' 
                                                : (log.status_raw === 'keluar_ruangan' 
                                                    ? 'bg-rose-50 text-rose-700 border-rose-200' 
                                                    : 'bg-slate-100 text-slate-500 border-slate-200')">
                                            <span class="w-2 h-2 rounded-full"
                                                :class="log.status_raw === 'di_meja' ? 'bg-emerald-500 animate-pulse' : (log.status_raw === 'keluar_ruangan' ? 'bg-rose-500' : 'bg-slate-400')"
                                            ></span>
                                            <span x-text="log.status_cctv"></span>
                                        </span>
                                    </td>
                                    {{-- Live Ticking Duration --}}
                                    <td class="px-4 py-3.5 text-center">
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg"
                                             :class="log.status_raw === 'di_meja' ? 'bg-emerald-50/70' : 'bg-slate-50'">
                                            <span x-show="log.status_raw === 'di_meja'"
                                                class="w-2 h-2 rounded-full bg-emerald-500 animate-ping flex-shrink-0"
                                                style="animation-duration: 1.5s"></span>
                                            <span class="text-sm font-mono font-bold"
                                                :class="log.status_raw === 'di_meja' ? 'text-emerald-700' : 'text-slate-600'"
                                                x-text="liveDuration(log)">—</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 text-center text-xs font-semibold text-slate-500" x-text="log.waktu_selesai"></td>

                                    <td class="px-4 py-3.5 text-center">
                                        <button type="button"
                                            @click="openInfoModal(log)"
                                            class="inline-flex items-center gap-1.5 text-indigo-600 hover:text-white bg-indigo-50 hover:bg-indigo-600 px-3.5 py-1.5 rounded-xl text-xs font-bold transition border border-indigo-200 hover:border-indigo-600 shadow-2xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Info 
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="detectionLogs.length === 0">
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-400">
                                        <i data-feather="video" class="w-10 h-10 mb-2 stroke-1"></i>
                                        <p class="text-sm font-semibold text-slate-600">Klik <strong class="text-indigo-600">Mulai Monitoring</strong> untuk mengaktifkan kamera pengawas.</p>
                                        <p class="text-xs text-slate-400 mt-1">Pastikan zona posisi kerja karyawan sudah diatur terlebih dahulu melalui tombol Atur Zona.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- MODAL INFO PROGRES KEHADIRAN (STANDAR 8 JAM) --}}
            <div x-show="infoModalOpen" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
                 style="display:none" @click.self="closeInfoModal()">

                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100">
                    {{-- Header --}}
                    <div class="bg-gradient-to-r from-indigo-600 via-blue-600 to-indigo-700 px-6 py-4 flex items-center justify-between text-white">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-white/20 rounded-xl">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg" x-text="infoData ? infoData.nama : 'Info Kehadiran'">Info Kehadiran</h3>
                                <p class="text-indigo-100 text-xs" x-text="infoData ? infoData.tanggal_label : 'Progres Jam Kerja Harian'"></p>
                            </div>
                        </div>
                        <button type="button" @click="closeInfoModal()" class="p-1.5 rounded-xl hover:bg-white/20 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="p-6 space-y-5">
                        <div x-show="infoLoading" class="py-10 flex flex-col items-center gap-3">
                            <div class="w-9 h-9 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
                            <p class="text-sm text-slate-500">Memuat informasi kehadiran...</p>
                        </div>

                        <div x-show="!infoLoading && infoData" class="space-y-5">
                            {{-- Status Kelayakan Kehadiran (Standar 8 Jam) --}}
                            <div class="p-4 rounded-xl border flex items-center justify-between"
                                 :class="liveProgressPct(infoLog) >= 100
                                    ? 'bg-emerald-50 border-emerald-200 text-emerald-800'
                                    : 'bg-amber-50 border-amber-200 text-amber-800'">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white flex-shrink-0"
                                         :class="liveProgressPct(infoLog) >= 100 ? 'bg-emerald-500' : 'bg-amber-500'">
                                        <span x-show="liveProgressPct(infoLog) >= 100">&#10003;</span>
                                        <span x-show="liveProgressPct(infoLog) < 100">&#8987;</span>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wider opacity-75">Status Pemenuhan Jam Kerja</p>
                                        <p class="text-base font-bold"
                                           x-text="liveProgressPct(infoLog) >= 100 ? 'HADIR PENUH (Target 8 Jam Terpenuhi)' : 'Dalam Progres Pemenuhan 8 Jam'"></p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-bold"
                                      :class="liveProgressPct(infoLog) >= 100 ? 'bg-emerald-200 text-emerald-900' : 'bg-amber-200 text-amber-900'"
                                      x-text="liveProgressPct(infoLog) >= 100 ? 'Hadir Sah' : 'Belum 8 Jam'"></span>
                            </div>

                            {{-- Progress Bar 8 Jam --}}
                            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200">
                                <div class="flex items-end justify-between mb-2">
                                    <div>
                                        <span class="text-sm font-bold text-slate-700">Progres Durasi Kehadiran</span>
                                        <p class="text-xs text-slate-500 mt-0.5">Standar Kehadiran Penuh: <strong>8 Jam (480 Menit)</strong></p>
                                    </div>
                                    <span class="text-2xl font-extrabold"
                                          :class="liveProgressPct(infoLog) >= 100 ? 'text-emerald-600' : (liveProgressPct(infoLog) >= 50 ? 'text-indigo-600' : 'text-amber-500')"
                                          x-text="liveProgressPct(infoLog) + '%'">0%</span>
                                </div>

                                <div class="w-full bg-slate-200 rounded-full h-3.5 overflow-hidden">
                                    <div class="h-3.5 rounded-full transition-all duration-500"
                                         :class="liveProgressPct(infoLog) >= 100 ? 'bg-emerald-500' : (liveProgressPct(infoLog) >= 50 ? 'bg-indigo-600' : 'bg-amber-500')"
                                         :style="'width:' + Math.min(100, liveProgressPct(infoLog)) + '%'"></div>
                                </div>

                                <div class="flex justify-between mt-2 text-xs text-slate-500 font-medium">
                                    <span>0 Jam (0%)</span>
                                    <span x-text="'Tercapai: ' + (infoLog ? liveDuration(infoLog) : (infoData?.durasi_di_meja || '0 Menit'))"></span>
                                    <span>8 Jam (100%)</span>
                                </div>
                            </div>

                            {{-- Detail Grid --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-blue-50 rounded-xl p-3.5 border border-blue-100">
                                    <p class="text-xs text-blue-600 font-semibold">Pertama Terdeteksi (Check-In)</p>
                                    <p class="text-base font-extrabold text-blue-900 mt-1" x-text="infoData?.check_in || '-'"></p>
                                </div>
                                <div class="bg-purple-50 rounded-xl p-3.5 border border-purple-100">
                                    <p class="text-xs text-purple-600 font-semibold">Terakhir Terdeteksi (Check-Out)</p>
                                    <p class="text-base font-extrabold text-purple-900 mt-1" x-text="infoData?.check_out || '-'"></p>
                                </div>
                                <div class="bg-emerald-50 rounded-xl p-3.5 border border-emerald-100">
                                    <p class="text-xs text-emerald-600 font-semibold">Durasi Aktif di Zona</p>
                                    <p class="text-base font-extrabold text-emerald-900 mt-1" x-text="infoLog ? liveDuration(infoLog) : (infoData?.durasi_di_meja || '0 Menit')"></p>
                                </div>
                                <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-200">
                                    <p class="text-xs text-slate-500 font-semibold">Sisa Menuju 8 Jam</p>
                                    <p class="text-base font-extrabold text-slate-800 mt-1" x-text="remainingTo8Hours(infoLog)"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                        {{-- <button type="button" @click="closeInfoModal()"
                                class="px-5 py-2 text-sm font-bold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-100 transition">
                            Tutup
                        </button> --}}
                    </div>
                </div>
            </div>

            {{-- MODAL PERINGATAN ZONA BELUM DIKONFIGURASI --}}
            <div x-show="zoneWarningOpen" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
                 style="display:none" @click.self="zoneWarningOpen = false">

                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border border-slate-100">
                    <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4 flex items-center justify-between text-white">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-white/20 rounded-xl">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <h3 class="font-bold text-lg">Zona Belum Dikonfigurasi</h3>
                        </div>
                        <button type="button" @click="zoneWarningOpen = false" class="p-1.5 rounded-xl hover:bg-white/20 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="p-6 text-center space-y-3">
                        <div class="w-14 h-14 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h4 class="text-base font-bold text-slate-900">Tidak Dapat Memulai Monitoring</h4>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            Belum ada zona posisi duduk/kerja karyawan yang dikonfigurasi. Silakan tandai minimal <strong>1 zona karyawan</strong> terlebih dahulu sebelum memulai pengawasan kamera.
                        </p>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="zoneWarningOpen = false"
                                class="px-4 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-100 transition">
                            Batal
                        </button>
                        <button type="button" @click="zoneWarningOpen = false; openZonaSetup();"
                                class="px-4 py-2 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow transition">
                            Atur Zona Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ZONA SETUP MODAL --}}
    <div x-data="zonaSetupApp()" x-init="initZona()" @open-zona-modal.window="openModal()">
        <div x-show="open" x-transition class="fixed inset-0 z-40 bg-slate-900/65 backdrop-blur-xs" style="display:none" @click="closeModal()"></div>
        <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-[95vw] h-[95vh] overflow-hidden flex flex-col border border-slate-200">
                <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-indigo-600 via-blue-600 to-indigo-700 text-white flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-white/15 rounded-xl">
                            <i data-feather="map-pin" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-extrabold">Konfigurasi Zona Posisi Kerja Karyawan</h3>
                            <p class="text-indigo-100 text-xs mt-0.5">Pilih nama karyawan di sebelah kanan &rarr; klik dan tarik (drag) kotak pada kamera untuk menandai zona kerja</p>
                        </div>
                    </div>
                    <button @click="closeModal()" class="p-2 rounded-xl hover:bg-white/20 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="flex flex-1 overflow-hidden" style="min-height:420px">
                    <div class="flex-1 p-5 flex flex-col bg-slate-900 overflow-hidden">
                        <div class="relative flex-1 rounded-xl border-2 border-slate-700 overflow-hidden bg-slate-950" style="min-height:300px">
                            {{-- Live Camera Preview inside Zona Setup Modal --}}
                            <video id="zonaPreviewVideo" autoplay playsinline muted
                                   x-show="previewCameraReady"
                                   class="absolute inset-0 w-full h-full object-cover pointer-events-none"></video>

                            <canvas id="zonaDrawCanvas"
                                :style="selectedPegawai ? 'cursor:crosshair' : 'cursor:not-allowed'"
                                class="relative z-10 w-full h-full select-none"
                                @mousedown="startDraw($event)" @mousemove="updateDraw($event)"
                                @mouseup="endDraw($event)" @mouseleave="cancelDraw()" style="min-height:300px">
                            </canvas>
                            <div x-show="!selectedPegawai" class="absolute inset-0 z-20 flex items-center justify-center pointer-events-none">
                                <div class="bg-slate-900/80 text-white text-center px-6 py-4 rounded-2xl backdrop-blur-xs border border-white/15 shadow-xl">
                                    <p class="font-bold text-sm">&larr; Pilih karyawan dari daftar di sebelah kanan</p>
                                    <p class="text-xs text-indigo-200 mt-1">lalu klik &amp; tarik pada area kamera untuk membuat kotak zona kerja</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center gap-3">
                            <p class="flex-1 text-xs font-medium text-slate-300" x-text="selectedPegawai ? 'Menggambar zona untuk: ' + selectedPegawai.pegawai_name : 'Silakan pilih karyawan di panel kanan terlebih dahulu'"></p>
                            <button x-show="currentRect" @click="saveZone()" :disabled="saving"
                                class="px-5 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold rounded-xl shadow-md transition disabled:opacity-50">
                                <span x-text="saving ? 'Menyimpan...' : '&#10003; Simpan Zona'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="w-72 border-l border-slate-200 flex flex-col overflow-hidden bg-white">
                        <div class="px-4 py-3.5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                            <p class="text-xs font-extrabold text-slate-600 uppercase tracking-wider">Daftar Karyawan</p>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-600 border border-indigo-100" x-text="employees.length + ' Orang'"></span>
                        </div>
                        <div class="flex-1 overflow-y-auto p-3 space-y-2">
                            <template x-for="p in employees" :key="p.pegawai_id">
                                <button @click="selectEmployee(p)"
                                    :class="selectedPegawai?.pegawai_id === p.pegawai_id ? 'ring-2 ring-indigo-600 bg-indigo-50/80 border-indigo-200' : 'hover:bg-slate-50 border-slate-100'"
                                    class="w-full px-3.5 py-3 rounded-xl text-left transition border">
                                    <div class="flex items-center gap-3">
                                        <div class="w-4 h-4 rounded-full flex-shrink-0 border border-white shadow-xs" :style="'background-color:' + p.warna"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-slate-800 truncate" x-text="p.pegawai_name"></p>
                                            @if(false) {{-- DISABLED: jabatan --}}
<p class="text-xs text-gray-400 truncate" x-text="p.jabatan || '-'"></p>
@endif
                                        </div>
                                        <span x-show="p.has_zone" class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 text-[10px] font-bold flex-shrink-0">Aktif</span>
                                    </div>
                                    <p x-show="p.has_zone" class="mt-1 ml-7 text-xs text-indigo-600 font-semibold" x-text="p.zona_label || 'Zona terdaftar'"></p>
                                </button>
                            </template>
                        </div>
                        <div class="p-3.5 border-t border-slate-100 bg-slate-50/50 space-y-2">
                            <button x-show="selectedPegawai?.has_zone" @click="deleteZone()" :disabled="deleting"
                                class="w-full px-3 py-2 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white text-xs font-bold rounded-xl transition border border-rose-200 disabled:opacity-50">
                                <span x-text="deleting ? 'Menghapus...' : 'Hapus Zona ' + (selectedPegawai?.pegawai_name || '')"></span>
                            </button>
                            <p x-show="statusMsg" class="text-xs text-center font-semibold"
                               :class="statusOk ? 'text-emerald-600' : 'text-rose-600'" x-text="statusMsg"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@include('atasan.kehadiran.script')
</x-app-layout>
