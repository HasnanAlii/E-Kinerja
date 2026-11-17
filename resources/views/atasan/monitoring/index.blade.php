<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Monitoring Kamera
        </h2>
    </x-slot>

    <div class="py-12 mx-auto sm:px-6 lg:px-8">
        <!-- DASHBOARD MONITORING KEHADIRAN -->
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
                        <h3 class="text-lg font-bold text-gray-900 tracking-tight">CCTV Monitoring Kehadiran (Real-time)</h3>
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

            <!-- Video Container (Full Width & Large + Fullscreen Support) -->
            <div x-show="isCameraOpen" x-transition style="display: none;"
                class="bg-slate-950 border-b border-gray-200">
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
                        class="absolute bottom-4 right-4 z-20 inline-flex items-center gap-2 px-3.5 py-2 bg-black/65 hover:bg-black/85 text-white text-xs font-semibold rounded-xl border border-white/20 backdrop-blur-md shadow-lg transition">
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
                                @if(false) {{-- DISABLED: jabatan --}}
<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jabatan</th>
@endif
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Zona / Posisi Meja</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status CCTV</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Durasi di Meja</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Terakhir Terlihat</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Info Kehadiran</th>
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
                                    @if(false) {{-- DISABLED: jabatan --}}
<td class="px-4 py-3">
                                        <p class="text-xs text-gray-500" x-text="log.jabatan"></p>
                                    </td>
@endif
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

                                    <td class="px-4 py-3 text-center text-sm">
                                        <button type="button"
                                            @click="openInfoModal(log)"
                                            class="inline-flex items-center gap-1.5 text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg text-xs font-semibold transition border border-indigo-200 shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Info
                                        </button>
                                    </td>
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

            {{-- MODAL INFO PROGRES KEHADIRAN (STANDAR 8 JAM) --}}
            <div x-show="infoModalOpen" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                 style="display:none" @click.self="closeInfoModal()">

                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
                    {{-- Header --}}
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4 flex items-center justify-between text-white">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-white/20 rounded-xl">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg" x-text="infoData ? infoData.nama : 'Info Kehadiran'">Info Kehadiran</h3>
                                <p class="text-indigo-200 text-xs" x-text="infoData ? infoData.tanggal_label : 'Progres Jam Kerja Harian'"></p>
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
                            <p class="text-sm text-gray-500">Memuat informasi kehadiran...</p>
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
                                        <span class="text-sm font-semibold text-gray-700">Progres Durasi Kehadiran</span>
                                        <p class="text-xs text-gray-500 mt-0.5">Standar Kehadiran Penuh: <strong>8 Jam (480 Menit)</strong></p>
                                    </div>
                                    <span class="text-2xl font-extrabold"
                                          :class="liveProgressPct(infoLog) >= 100 ? 'text-emerald-600' : (liveProgressPct(infoLog) >= 50 ? 'text-indigo-600' : 'text-amber-500')"
                                          x-text="liveProgressPct(infoLog) + '%'">0%</span>
                                </div>

                                <div class="w-full bg-gray-200 rounded-full h-3.5 overflow-hidden">
                                    <div class="h-3.5 rounded-full transition-all duration-500"
                                         :class="liveProgressPct(infoLog) >= 100 ? 'bg-emerald-500' : (liveProgressPct(infoLog) >= 50 ? 'bg-indigo-600' : 'bg-amber-500')"
                                         :style="'width:' + Math.min(100, liveProgressPct(infoLog)) + '%'"></div>
                                </div>

                                <div class="flex justify-between mt-2 text-xs text-gray-500 font-medium">
                                    <span>0 Jam (0%)</span>
                                    <span x-text="'Tercapai: ' + (infoLog ? liveDuration(infoLog) : (infoData?.durasi_di_meja || '0 Menit'))"></span>
                                    <span>8 Jam (100%)</span>
                                </div>
                            </div>

                            {{-- Detail Grid --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-blue-50 rounded-xl p-3.5 border border-blue-100">
                                    <p class="text-xs text-blue-600 font-medium">Pertama Terdeteksi (Masuk)</p>
                                    <p class="text-base font-bold text-blue-900 mt-1" x-text="infoData?.check_in || '-'"></p>
                                </div>
                                <div class="bg-purple-50 rounded-xl p-3.5 border border-purple-100">
                                    <p class="text-xs text-purple-600 font-medium">Terakhir Terdeteksi (Pulang)</p>
                                    <p class="text-base font-bold text-purple-900 mt-1" x-text="infoData?.check_out || '-'"></p>
                                </div>
                                <div class="bg-emerald-50 rounded-xl p-3.5 border border-emerald-100">
                                    <p class="text-xs text-emerald-600 font-medium">Durasi Aktif di Zona</p>
                                    <p class="text-base font-bold text-emerald-900 mt-1" x-text="infoLog ? liveDuration(infoLog) : (infoData?.durasi_di_meja || '0 Menit')"></p>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-3.5 border border-gray-200">
                                    <p class="text-xs text-gray-500 font-medium">Sisa Menuju 8 Jam</p>
                                    <p class="text-base font-bold text-gray-800 mt-1" x-text="remainingTo8Hours(infoLog)"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                        <button type="button" @click="closeInfoModal()"
                                class="px-5 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-100 transition">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>

            {{-- MODAL PERINGATAN ZONA BELUM DIKONFIGURASI --}}
            <div x-show="zoneWarningOpen" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                 style="display:none" @click.self="zoneWarningOpen = false">

                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
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
                        <h4 class="text-base font-bold text-gray-900">Tidak Dapat Memulai Pengawasan</h4>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Belum ada zona posisi duduk/kerja karyawan yang dikonfigurasi. Silakan tandai minimal <strong>1 zona karyawan</strong> terlebih dahulu sebelum memulai pengawasan kamera.
                        </p>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
                        <button type="button" @click="zoneWarningOpen = false"
                                class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-100 transition">
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
        <div x-show="open" x-transition class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm" style="display:none" @click="closeModal()"></div>
        <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-[95vw] h-[95vh] overflow-hidden flex flex-col">
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
                                <div class="bg-black/70 text-white text-center px-6 py-4 rounded-xl backdrop-blur-sm border border-white/10">
                                    <p class="font-semibold">&larr; Pilih pegawai dari daftar kanan</p>
                                    <p class="text-sm text-gray-300 mt-1">lalu klik-drag pada tampilan kamera untuk menandai posisi</p>
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
                                            @if(false) {{-- DISABLED: jabatan --}}
<p class="text-xs text-gray-400 truncate" x-text="p.jabatan || '-'"></p>
@endif
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
