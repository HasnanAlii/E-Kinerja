<x-app-layout>
    <div class="py-8 bg-slate-50/70 min-h-screen">
        <div class="mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- HERO HEADER BANNER --}}
            <div class="bg-gradient-to-r from-indigo-700 via-indigo-600 to-blue-600 rounded-2xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/15 backdrop-blur-sm border border-white/20 mb-3">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Sistem Monitoring Kehadiran Otomatis — PT Pou Yuen Indonesia
                        </span>
                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                            Dashboard {{ ucfirst($data['role'] === 'admin' ? 'Administrator' : 'Atasan / Supervisor') }}
                        </h1>
                        <p class="mt-1.5 text-indigo-100 text-sm sm:text-base max-w-2xl">
                            Selamat datang, <strong>{{ Auth::user()->name }}</strong>. Pantau statistik kehadiran real-time, grafik tren harian, distribusi departemen, serta pengajuan izin/sakit/cuti karyawan.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        @if($data['role'] === 'atasan')
                            <a href="{{ route('atasan.monitoring.index') }}"
                               class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-indigo-700 font-bold text-sm rounded-xl shadow-md hover:bg-indigo-50 transition">
                                <i data-feather="video" class="w-4 h-4"></i>
                                Buka Kamera Monitoring
                            </a>
                            <a href="{{ route('atasan.laporan.index') }}"
                               class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-900/50 hover:bg-indigo-900/70 text-white font-semibold text-sm rounded-xl border border-white/20 transition">
                                <i data-feather="file-text" class="w-4 h-4"></i>
                                Laporan PDF / Excel
                            </a>
                        @endif
                    </div>
                </div>
                <div class="absolute -top-12 -right-12 w-64 h-64 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
            </div>

            {{-- BARIS 1: 4 KARTU KPI UTAMA --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Total Karyawan --}}
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Karyawan</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ $data['total_karyawan'] }}</p>
                        <p class="text-xs text-indigo-600 font-medium mt-1.5">{{ $data['total_zona'] }} Zona Kamera Terdaftar</p>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-indigo-50 text-indigo-600">
                        <i data-feather="users" class="w-7 h-7"></i>
                    </div>
                </div>

                {{-- Hadir Hari Ini --}}
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Hadir Hari Ini</p>
                        <p class="text-3xl font-extrabold text-emerald-600 mt-1">{{ $data['hadir_hari_ini'] }}</p>
                        @php
                            $pctHadir = $data['total_karyawan'] > 0 ? round(($data['hadir_hari_ini'] / $data['total_karyawan']) * 100, 1) : 0;
                        @endphp
                        <p class="text-xs text-emerald-600 font-medium mt-1.5">{{ $pctHadir }}% dari total karyawan</p>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-emerald-50 text-emerald-600">
                        <i data-feather="check-circle" class="w-7 h-7"></i>
                    </div>
                </div>

                {{-- Izin / Sakit / Cuti Hari Ini --}}
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Izin / Sakit / Cuti</p>
                        <p class="text-3xl font-extrabold text-blue-600 mt-1">{{ $data['izin_sakit_cuti'] }}</p>
                        <p class="text-xs text-gray-500 mt-1.5">Tercatat hari ini ({{ now()->translatedFormat('d M Y') }})</p>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-blue-50 text-blue-600">
                        <i data-feather="calendar" class="w-7 h-7"></i>
                    </div>
                </div>

                {{-- Pengajuan Menunggu Verifikasi --}}
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Menunggu Verifikasi</p>
                        <p class="text-3xl font-extrabold text-amber-500 mt-1">{{ $data['pengajuan_menunggu'] }}</p>
                        <p class="text-xs text-amber-600 font-medium mt-1.5">Permohonan Izin / Sakit / Cuti</p>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-amber-50 text-amber-500">
                        <i data-feather="inbox" class="w-7 h-7"></i>
                    </div>
                </div>
            </div>

            {{-- BARIS 2: GRAFIK TREN 7 HARI & KOMPOSISI KEHADIRAN BULAN INI --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Grafik Tren Kehadiran 7 Hari Terakhir --}}
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Grafik Tren Kehadiran (7 Hari Terakhir)</h3>
                            <p class="text-xs text-gray-500">Perbandingan jumlah karyawan hadir vs izin/sakit/cuti per hari</p>
                        </div>
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-semibold">Real-time DB</span>
                    </div>
                    <div class="h-72">
                        <canvas id="chartTrendKehadiran"></canvas>
                    </div>
                </div>

                {{-- Grafik Doughnut Komposisi Bulan Ini + Distribusi Departemen --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="text-base font-bold text-gray-900">Komposisi Bulan Ini</h3>
                                <p class="text-xs text-gray-500">Bulan {{ now()->translatedFormat('F Y') }}</p>
                            </div>
                        </div>
                        <div class="h-52 flex items-center justify-center">
                            <canvas id="chartKomposisiBulan"></canvas>
                        </div>
                    </div>

                    <div class="grid grid-cols-5 gap-1.5 pt-4 border-t border-gray-100 text-center">
                        <div class="p-2 rounded-lg bg-emerald-50">
                            <p class="text-[10px] font-bold text-emerald-600 uppercase">Hadir</p>
                            <p class="text-sm font-extrabold text-emerald-800">{{ $data['rekap_bulan']['hadir'] }}</p>
                        </div>
                        <div class="p-2 rounded-lg bg-blue-50">
                            <p class="text-[10px] font-bold text-blue-600 uppercase">Izin</p>
                            <p class="text-sm font-extrabold text-blue-800">{{ $data['rekap_bulan']['izin'] }}</p>
                        </div>
                        <div class="p-2 rounded-lg bg-amber-50">
                            <p class="text-[10px] font-bold text-amber-600 uppercase">Sakit</p>
                            <p class="text-sm font-extrabold text-amber-800">{{ $data['rekap_bulan']['sakit'] }}</p>
                        </div>
                        <div class="p-2 rounded-lg bg-purple-50">
                            <p class="text-[10px] font-bold text-purple-600 uppercase">Cuti</p>
                            <p class="text-sm font-extrabold text-purple-800">{{ $data['rekap_bulan']['cuti'] }}</p>
                        </div>
                        <div class="p-2 rounded-lg bg-red-50">
                            <p class="text-[10px] font-bold text-red-600 uppercase">Alpha</p>
                            <p class="text-sm font-extrabold text-red-800">{{ $data['rekap_bulan']['alpha'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BARIS 3: TABEL KEHADIRAN TERBARU & SIDEBAR (DEPARTEMEN + MENU PINTASAN) --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Tabel Log Kehadiran Terbaru --}}
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Riwayat Deteksi &amp; Kehadiran Terbaru</h3>
                            <p class="text-xs text-gray-500">Daftar pencatatan kehadiran otomatis kamera &amp; perizinan terakhir</p>
                        </div>
                        <a href="{{ $data['role'] === 'atasan' ? route('atasan.kehadiran.index') : route('admin.kehadiran.index') }}"
                           class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Lihat Semua &rarr;</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                                <tr>
                                    <th class="px-5 py-3 text-left">Karyawan</th>
                                    <th class="px-5 py-3 text-left">Departemen</th>
                                    <th class="px-5 py-3 text-center">Tanggal</th>
                                    <th class="px-5 py-3 text-center">Masuk</th>
                                    <th class="px-5 py-3 text-center">Pulang</th>
                                    <th class="px-5 py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($data['kehadiran_terbaru'] as $row)
                                    <tr class="hover:bg-gray-50/70 transition">
                                        <td class="px-5 py-3.5 font-semibold text-gray-900">
                                            {{ $row->pegawai->user->name ?? '-' }}
                                            <div class="text-xs font-mono text-gray-400">{{ $row->pegawai->nik_karyawan ?? $row->pegawai->nip ?? '' }}</div>
                                        </td>
                                        <td class="px-5 py-3.5 text-xs text-gray-600">
                                            {{ $row->pegawai->departemen ?? '-' }}
                                        </td>
                                        <td class="px-5 py-3.5 text-center text-gray-600">
                                            {{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-5 py-3.5 text-center font-mono text-xs text-gray-800">
                                            {{ $row->check_in ? \Carbon\Carbon::parse($row->check_in)->format('H:i') : '-' }}
                                        </td>
                                        <td class="px-5 py-3.5 text-center font-mono text-xs text-gray-800">
                                            {{ $row->check_out ? \Carbon\Carbon::parse($row->check_out)->format('H:i') : '-' }}
                                        </td>
                                        <td class="px-5 py-3.5 text-center">
                                            @php
                                                $badgeCls = match($row->jenis) {
                                                    'hadir' => 'bg-emerald-100 text-emerald-800',
                                                    'izin'  => 'bg-blue-100 text-blue-800',
                                                    'sakit' => 'bg-amber-100 text-amber-800',
                                                    'cuti'  => 'bg-purple-100 text-purple-800',
                                                    default => 'bg-red-100 text-red-800',
                                                };
                                            @endphp
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase {{ $badgeCls }}">
                                                {{ $row->jenis ?? 'Hadir' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-10 text-center text-gray-400 text-sm">
                                            Belum ada data kehadiran tercatat.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Kolom Kanan: Distribusi Departemen & Menu Pintasan --}}
                <div class="space-y-6">
                    {{-- Distribusi Karyawan per Departemen --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-base font-bold text-gray-900 mb-1">Distribusi Departemen</h3>
                        <p class="text-xs text-gray-500 mb-4">Jumlah karyawan aktif per departemen PT Pou Yuen</p>
                        <div class="h-48">
                            <canvas id="chartDepartemen"></canvas>
                        </div>
                    </div>

                    {{-- Menu Pintasan Cepat --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-5 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white">
                            <h3 class="font-bold text-sm flex items-center gap-2">
                                <i data-feather="zap" class="w-4 h-4 text-yellow-300"></i>
                                Menu Pintasan Pengelolaan
                            </h3>
                        </div>
                        <div class="p-4 space-y-2.5">
                            @if($data['role'] === 'atasan')
                                <a href="{{ route('atasan.monitoring.index') }}" class="flex items-center p-3 rounded-xl border border-gray-100 hover:border-indigo-500 hover:bg-indigo-50/40 transition">
                                    <div class="p-2.5 bg-red-50 text-red-600 rounded-lg"><i data-feather="video" class="w-5 h-5"></i></div>
                                    <div class="ml-3">
                                        <p class="text-sm font-bold text-gray-800">Monitoring Kamera (YOLOv8)</p>
                                        <p class="text-xs text-gray-500">Pengawasan zona &amp; durasi 8 jam</p>
                                    </div>
                                </a>
                                <a href="{{ route('admin.pegawai.index') }}" class="flex items-center p-3 rounded-xl border border-gray-100 hover:border-indigo-500 hover:bg-indigo-50/40 transition">
                                    <div class="p-2.5 bg-indigo-50 text-indigo-600 rounded-lg"><i data-feather="users" class="w-5 h-5"></i></div>
                                    <div class="ml-3">
                                        <p class="text-sm font-bold text-gray-800">Kelola Data Karyawan</p>
                                        <p class="text-xs text-gray-500">Tambah, edit &amp; cetak PDF per orang</p>
                                    </div>
                                </a>
                                <a href="{{ route('atasan.izin.index') }}" class="flex items-center p-3 rounded-xl border border-gray-100 hover:border-indigo-500 hover:bg-indigo-50/40 transition">
                                    <div class="p-2.5 bg-amber-50 text-amber-600 rounded-lg"><i data-feather="inbox" class="w-5 h-5"></i></div>
                                    <div class="ml-3">
                                        <p class="text-sm font-bold text-gray-800">Verifikasi Izin / Sakit / Cuti</p>
                                        <p class="text-xs text-gray-500">{{ $data['pengajuan_menunggu'] }} pengajuan menunggu persetujuan</p>
                                    </div>
                                </a>
                                <a href="{{ route('atasan.laporan.index') }}" class="flex items-center p-3 rounded-xl border border-gray-100 hover:border-indigo-500 hover:bg-indigo-50/40 transition">
                                    <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-lg"><i data-feather="file-text" class="w-5 h-5"></i></div>
                                    <div class="ml-3">
                                        <p class="text-sm font-bold text-gray-800">Laporan Kehadiran (PDF / Excel)</p>
                                        <p class="text-xs text-gray-500">Rekap formal PT Pou Yuen &amp; per orang</p>
                                    </div>
                                </a>
                            @else
                                <a href="{{ route('admin.pegawai.index') }}" class="flex items-center p-3 rounded-xl border border-gray-100 hover:border-indigo-500 hover:bg-indigo-50/40 transition">
                                    <div class="p-2.5 bg-indigo-50 text-indigo-600 rounded-lg"><i data-feather="users" class="w-5 h-5"></i></div>
                                    <div class="ml-3">
                                        <p class="text-sm font-bold text-gray-800">Data Karyawan</p>
                                        <p class="text-xs text-gray-500">Kelola seluruh karyawan PT Pou Yuen</p>
                                    </div>
                                </a>
                                <a href="{{ route('admin.kehadiran.index') }}" class="flex items-center p-3 rounded-xl border border-gray-100 hover:border-indigo-500 hover:bg-indigo-50/40 transition">
                                    <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-lg"><i data-feather="calendar" class="w-5 h-5"></i></div>
                                    <div class="ml-3">
                                        <p class="text-sm font-bold text-gray-800">Rekap Kehadiran Karyawan</p>
                                        <p class="text-xs text-gray-500">Lihat riwayat kehadiran harian</p>
                                    </div>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof feather !== 'undefined') feather.replace();

            // 1. Grafik Tren 7 Hari Terakhir
            const ctxTrend = document.getElementById('chartTrendKehadiran');
            if (ctxTrend && typeof Chart !== 'undefined') {
                new Chart(ctxTrend, {
                    type: 'bar',
                    data: {
                        labels: @json($data['trend_labels']),
                        datasets: [
                            {
                                label: 'Hadir',
                                data: @json($data['trend_hadir']),
                                backgroundColor: '#10b981',
                                borderRadius: 6,
                                barPercentage: 0.6
                            },
                            {
                                label: 'Izin / Sakit / Cuti',
                                data: @json($data['trend_izin']),
                                backgroundColor: '#3b82f6',
                                borderRadius: 6,
                                barPercentage: 0.6
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'top' }
                        },
                        scales: {
                            y: { beginAtZero: true, ticks: { precision: 0 } }
                        }
                    }
                });
            }

            // 2. Grafik Komposisi Bulan Ini
            const ctxKomp = document.getElementById('chartKomposisiBulan');
            if (ctxKomp && typeof Chart !== 'undefined') {
                const rb = @json(array_values($data['rekap_bulan']));
                const totalRb = rb.reduce((a, b) => a + b, 0);
                new Chart(ctxKomp, {
                    type: 'doughnut',
                    data: {
                        labels: ['Hadir', 'Izin', 'Sakit', 'Cuti', 'Alpha'],
                        datasets: [{
                            data: totalRb > 0 ? rb : [1, 0, 0, 0, 0],
                            backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#ef4444'],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } }
                        }
                    }
                });
            }

            // 3. Grafik Distribusi Departemen
            const ctxDept = document.getElementById('chartDepartemen');
            if (ctxDept && typeof Chart !== 'undefined') {
                new Chart(ctxDept, {
                    type: 'bar',
                    data: {
                        labels: @json($data['dept_labels']),
                        datasets: [{
                            label: 'Jumlah Karyawan',
                            data: @json($data['dept_values']),
                            backgroundColor: '#4f46e5',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { x: { beginAtZero: true, ticks: { precision: 0 } } }
                    }
                });
            }
        });
    </script>
</x-app-layout>
