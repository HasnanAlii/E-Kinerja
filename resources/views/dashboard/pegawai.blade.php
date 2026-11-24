<x-app-layout>
    <div class="py-8 bg-slate-50/70 min-h-screen">
        <div class="mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- HERO HEADER BANNER --}}
            <div class="bg-gradient-to-r from-indigo-700 via-indigo-600 to-blue-600 rounded-2xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/15 backdrop-blur-sm border border-white/20 mb-3">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Monitoring Kehadiran Otomatis — PT Pou Yuen Indonesia
                        </span>
                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Dashboard Karyawan</h1>
                        <p class="mt-1.5 text-indigo-100 text-sm sm:text-base">
                            Selamat datang, <strong>{{ Auth::user()->name }}</strong>
                            @if($data['pegawai'])
                                ({{ $data['pegawai']->nik_karyawan ?? $data['pegawai']->nip ?? '-' }} &bull; {{ $data['pegawai']->departemen ?? 'PT Pou Yuen' }})
                            @endif
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('laporan.kehadiran.perpegawai', ['pegawai_id' => $data['pegawai']->id ?? null]) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-indigo-700 font-bold text-sm rounded-xl shadow-md hover:bg-indigo-50 transition">
                            <i data-feather="file-text" class="w-4 h-4"></i>
                            Cetak Laporan PDF 
                        </a>
                        <a href="{{ route('pegawai.izin.create') }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-900/50 hover:bg-indigo-900/70 text-white font-semibold text-sm rounded-xl border border-white/20 transition">
                            <i data-feather="file-plus" class="w-4 h-4"></i>
                            Ajukan Izin / Sakit / Cuti
                        </a>
                    </div>
                </div>
                <div class="absolute -top-12 -right-12 w-64 h-64 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
            </div>

            @if (session('success'))
                <div class="p-4 flex items-center bg-emerald-100 text-emerald-800 border border-emerald-300 rounded-2xl text-sm font-semibold shadow-2xs">
                    <i data-feather="check-circle" class="w-5 h-5 mr-3 text-emerald-600 shrink-0"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 flex items-center bg-rose-100 text-rose-800 border border-rose-300 rounded-2xl text-sm font-semibold shadow-2xs">
                    <i data-feather="alert-circle" class="w-5 h-5 mr-3 text-rose-600 shrink-0"></i>
                    {{ session('error') }}
                </div>
            @endif

            {{-- BANNER AKTIVASI KODE REFERAL HARIAN & KUNCI CIRI VISUAL --}}
            @if(isset($data['aktivasi_hari_ini']) && $data['aktivasi_hari_ini'])
                @php $akt = $data['aktivasi_hari_ini']; @endphp
                <div class="rounded-2xl border {{ $akt->is_active ? 'bg-gradient-to-r from-emerald-50 via-teal-50 to-emerald-50 border-emerald-200' : 'bg-gradient-to-r from-amber-50 via-orange-50 to-amber-50 border-amber-200' }} p-6 shadow-sm">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div class="space-y-2 flex-1">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider {{ $akt->is_active ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white animate-pulse' }}">
                                    <span class="w-2 h-2 rounded-full bg-white"></span>
                                    {{ $akt->is_active ? 'ZONA AKTIF' : 'ZONA DISABLE' }}
                                </span>
                                <span class="text-xs font-semibold text-gray-500">
                                    Kode Referal Harian (Reset Tiap Hari)
                                </span>
                                @if($akt->ciri_visual)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700 border border-indigo-200">
                                        🔒 Ciri Visual Tersimpan ({{ $akt->first_entered_at ? $akt->first_entered_at->format('H:i') : 'Hari Ini' }})
                                    </span>
                                @endif
                            </div>
                            <h3 class="text-lg font-extrabold text-gray-900">
                                {{ $akt->is_active ? 'Zona Kehadiran Kamera Anda Sudah Aktif Hari Ini' : 'Konfirmasi Kehadiran & Aktifkan Zona Kamera Anda' }}
                            </h3>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                @if($akt->is_active)
                                    Kode referal harian telah dikonfirmasi. Saat pertama kali Anda masuk ke zona kerja, kamera otomatis mengunci ciri-ciri penampilan Anda hari ini agar tidak mendeteksi orang lain.
                                @else
                                    Silakan input <strong>Kode Referal Harian</strong> yang tertera di samping sebelum melakukan absensi kamera. <strong>Apabila belum diinput, zona Anda berstatus Disable dan sistem tidak akan mencatat kehadiran.</strong>
                                @endif
                            </p>
                        </div>

                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 shrink-0">
                            <div class="bg-white px-5 py-3.5 rounded-xl border border-gray-200 shadow-2xs text-center">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Kode Referal Anda Hari Ini</p>
                                <p class="text-2xl font-mono font-black tracking-widest text-indigo-700 mt-0.5 select-all">{{ $akt->kode_referal }}</p>
                            </div>

                            @if(!$akt->is_active)
                                <form method="POST" action="{{ route('pegawai.kehadiran.aktivasi_referal') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                                    @csrf
                                    <input type="text"
                                           name="kode_referal"
                                           placeholder="Ketik {{ $akt->kode_referal }}"
                                           required
                                           autocomplete="off"
                                           class="w-full sm:w-48 px-4 py-3 rounded-xl border-gray-300 font-mono font-bold text-sm uppercase tracking-wider focus:border-indigo-500 focus:ring-indigo-500 shadow-2xs">
                                    <button type="submit"
                                            class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-md transition cursor-pointer">
                                        <i data-feather="unlock" class="w-4 h-4"></i>
                                        Aktifkan Zona
                                    </button>
                                </form>
                            @else
                                <div class="flex items-center gap-3 bg-emerald-600/10 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-xl">
                                    @if($akt->snapshot_wajah)
                                        <img src="{{ $akt->snapshot_wajah }}" alt="Snapshot Ciri" class="w-11 h-11 rounded-lg object-cover border border-emerald-400">
                                    @endif
                                    <div>
                                        <p class="text-xs font-extrabold uppercase text-emerald-700">✓ Zona Aktif</p>
                                        <p class="text-xs text-emerald-600">{{ $akt->ciri_visual ? 'Ciri karyawan terkunci' : 'Menunggu masuk zona pertama kali' }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- BARIS 1: 4 KARTU STATUS HARI INI & BULAN INI --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Status Hari Ini --}}
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Status Hari Ini</p>
                        <p class="text-2xl font-extrabold mt-1 {{ strtolower($data['status_hari_ini']) === 'hadir' ? 'text-emerald-600' : 'text-indigo-700' }}">
                            {{ $data['status_hari_ini'] }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1.5">{{ now()->translatedFormat('l, d M Y') }}</p>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-indigo-50 text-indigo-600">
                        <i data-feather="clock" class="w-7 h-7"></i>
                    </div>
                </div>

                {{-- Jam Masuk (Pertama Terdeteksi Kamera) --}}
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Jam Masuk (Kamera)</p>
                        <p class="text-2xl font-extrabold text-blue-600 mt-1">{{ $data['jam_masuk'] }}</p>
                        <p class="text-xs text-gray-500 mt-1.5">Pertama terdeteksi di zona</p>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-blue-50 text-blue-600">
                        <i data-feather="log-in" class="w-7 h-7"></i>
                    </div>
                </div>

                {{-- Jam Pulang (Terakhir Terdeteksi Kamera) --}}
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Jam Pulang (Kamera)</p>
                        <p class="text-2xl font-extrabold text-purple-600 mt-1">{{ $data['jam_pulang'] }}</p>
                        <p class="text-xs text-gray-500 mt-1.5">Deteksi terakhir sebelum tutup</p>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-purple-50 text-purple-600">
                        <i data-feather="log-out" class="w-7 h-7"></i>
                    </div>
                </div>

                {{-- Total Hadir Bulan Ini --}}
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Hadir Bulan Ini</p>
                        <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $data['hadir_bulan_ini'] }} Hari</p>
                        <p class="text-xs text-gray-500 mt-1.5">
                            Izin: {{ $data['izin_bulan_ini'] }} &bull; Sakit: {{ $data['sakit_bulan_ini'] }} &bull; Cuti: {{ $data['cuti_bulan_ini'] }}
                        </p>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-emerald-50 text-emerald-600">
                        <i data-feather="calendar" class="w-7 h-7"></i>
                    </div>
                </div>
            </div>

            {{-- BARIS 2: PROGRES STANDAR 8 JAM HARI INI & GRAFIK JAM KERJA 7 HARI TERAKHIR --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Progres Pemenuhan Jam Kerja 8 Jam & Ringkasan Profil --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between space-y-6">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-base font-bold text-gray-900">Progres Jam Kerja Hari Ini</h3>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $data['progres_8_jam'] >= 100 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $data['progres_8_jam'] >= 100 ? 'Hadir Penuh (8 Jam)' : 'Standar 8 Jam' }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mb-4">Durasi terdeteksi kamera terhadap standar 8 jam kerja penuh</p>

                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-200/80">
                            <div class="flex items-end justify-between mb-2">
                                <span class="text-sm font-semibold text-gray-700">Durasi: {{ $data['durasi_hari_ini'] }}</span>
                                <span class="text-2xl font-extrabold {{ $data['progres_8_jam'] >= 100 ? 'text-emerald-600' : 'text-indigo-600' }}">
                                    {{ $data['progres_8_jam'] }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                <div class="h-3 rounded-full transition-all duration-500 {{ $data['progres_8_jam'] >= 100 ? 'bg-emerald-500' : 'bg-indigo-600' }}"
                                     style="width: {{ min(100, $data['progres_8_jam']) }}%"></div>
                            </div>
                            <div class="flex justify-between mt-2 text-[11px] text-gray-400 font-medium">
                                <span>0 Jam (0%)</span>
                                <span>Target: 8 Jam (100%)</span>
                            </div>
                        </div>
                    </div>

                    {{-- Identitas Singkat Karyawan PT Pou Yuen --}}
                    @if($data['pegawai'])
                        <div class="border-t border-gray-100 pt-4 space-y-2 text-xs">
                            <p class="font-bold text-gray-400 uppercase tracking-wider">Data Kepegawaian PT Pou Yuen</p>
                            <div class="flex justify-between py-1 border-b border-gray-50">
                                <span class="text-gray-500">NIK Karyawan</span>
                                <span class="font-mono font-bold text-gray-800">{{ $data['pegawai']->nik_karyawan ?? $data['pegawai']->nip ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-gray-50">
                                <span class="text-gray-500">Departemen / Divisi</span>
                                <span class="font-semibold text-gray-800">{{ $data['pegawai']->departemen ?? '-' }} / {{ $data['pegawai']->divisi ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-gray-50">
                                <span class="text-gray-500">Jabatan</span>
                                <span class="font-semibold text-gray-800">{{ $data['pegawai']->jabatan ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-gray-500">Status</span>
                                <span class="font-bold text-emerald-700">{{ $data['pegawai']->status ?? 'Masih Bekerja' }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Grafik Durasi Kehadiran 7 Hari Terakhir --}}
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Grafik Durasi Kehadiran Pribadi (7 Hari Terakhir)</h3>
                            <p class="text-xs text-gray-500">Jumlah jam kehadiran per hari dibandingkan dengan standar 8 jam kerja</p>
                        </div>
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-semibold">Target: 8 Jam/Hari</span>
                    </div>
                    <div class="h-64">
                        <canvas id="chartPegawaiHours"></canvas>
                    </div>
                </div>
            </div>

            {{-- BARIS 3: RIWAYAT KEHADIRAN TERBARU & MENU PINTASAN / IZIN --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Tabel Riwayat Kehadiran Pribadi --}}
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Riwayat Kehadiran Terbaru Anda</h3>
                            <p class="text-xs text-gray-500">Pencatatan otomatis oleh kamera pengawas</p>
                        </div>
                        <a href="{{ route('pegawai.kehadiran.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                            Lihat Harian &amp; Bulanan &rarr;
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                                <tr>
                                    <th class="px-5 py-3 text-left">Tanggal</th>
                                    <th class="px-5 py-3 text-center">Check-In</th>
                                    <th class="px-5 py-3 text-center">Check-Out</th>
                                    <th class="px-5 py-3 text-center">Kode</th>
                                    <th class="px-5 py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($data['riwayat_kehadiran'] as $row)
                                    <tr class="hover:bg-gray-50/70 transition">
                                        <td class="px-5 py-3.5 font-medium text-gray-800">
                                            {{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d M Y') }}
                                        </td>
                                        <td class="px-5 py-3.5 text-center font-mono text-xs text-gray-800">
                                            {{ $row->check_in ? \Carbon\Carbon::parse($row->check_in)->format('H:i:s') : '-' }}
                                        </td>
                                        <td class="px-5 py-3.5 text-center font-mono text-xs text-gray-800">
                                            {{ $row->check_out ? \Carbon\Carbon::parse($row->check_out)->format('H:i:s') : '--- belum pulang' }}
                                        </td>
                                        <td class="px-5 py-3.5 text-center font-mono text-xs font-bold text-gray-600">
                                            {{ $row->jenis === 'hadir' ? 'SYNC' : strtoupper($row->jenis ?? 'OFF') }}
                                        </td>
                                        <td class="px-5 py-3.5 text-center">
                                            @php
                                                $cls = match($row->jenis) {
                                                    'hadir' => 'bg-emerald-100 text-emerald-800',
                                                    'izin'  => 'bg-blue-100 text-blue-800',
                                                    'sakit' => 'bg-amber-100 text-amber-800',
                                                    'cuti'  => 'bg-purple-100 text-purple-800',
                                                    default => 'bg-red-100 text-red-800',
                                                };
                                            @endphp
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase {{ $cls }}">
                                                {{ $row->jenis ?? 'Hadir' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-5 py-10 text-center text-gray-400 text-sm">
                                            Belum ada riwayat kehadiran.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Menu Pintasan Karyawan --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4 bg-gradient-to-r from-indigo-600 to-blue-600 text-white">
                        <h3 class="font-bold text-sm flex items-center gap-2">
                            <i data-feather="zap" class="w-4 h-4 text-yellow-300"></i>
                            Menu Pintasan Karyawan
                        </h3>
                    </div>
                    <div class="p-4 space-y-3">
                        <a href="{{ route('pegawai.kehadiran.index') }}" class="flex items-center p-3.5 rounded-xl border border-gray-100 hover:border-indigo-500 hover:bg-indigo-50/40 transition">
                            <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-lg"><i data-feather="calendar" class="w-5 h-5"></i></div>
                            <div class="ml-3">
                                <p class="text-sm font-bold text-gray-800">Cek Kehadiran (Harian &amp; Bulanan)</p>
                                <p class="text-xs text-gray-500">Lihat hasil monitoring kamera &amp; progres 8 jam</p>
                            </div>
                        </a>

                        <a href="{{ route('pegawai.izin.create') }}" class="flex items-center p-3.5 rounded-xl border border-gray-100 hover:border-indigo-500 hover:bg-indigo-50/40 transition">
                            <div class="p-2.5 bg-blue-50 text-blue-600 rounded-lg"><i data-feather="file-plus" class="w-5 h-5"></i></div>
                            <div class="ml-3">
                                <p class="text-sm font-bold text-gray-800">Ajukan Izin, Sakit, atau Cuti</p>
                                <p class="text-xs text-gray-500">Buat pengajuan baru beserta surat bukti</p>
                            </div>
                        </a>

                        <a href="{{ route('profil.edit') }}" class="flex items-center p-3.5 rounded-xl border border-gray-100 hover:border-indigo-500 hover:bg-indigo-50/40 transition">
                            <div class="p-2.5 bg-indigo-50 text-indigo-600 rounded-lg"><i data-feather="user" class="w-5 h-5"></i></div>
                            <div class="ml-3">
                                <p class="text-sm font-bold text-gray-800">Kelola Data Diri</p>
                                <p class="text-xs text-gray-500">Perbarui profil, departemen, divisi &amp; foto</p>
                            </div>
                        </a>

                        <a href="{{ route('laporan.kehadiran.perpegawai', ['pegawai_id' => $data['pegawai']->id ?? null]) }}" target="_blank"
                           class="flex items-center p-3.5 rounded-xl border border-gray-100 hover:border-red-500 hover:bg-red-50/40 transition">
                            <div class="p-2.5 bg-red-50 text-red-600 rounded-lg"><i data-feather="printer" class="w-5 h-5"></i></div>
                            <div class="ml-3">
                                <p class="text-sm font-bold text-gray-800">Lihat Laporan PDF Pribadi</p>
                                <p class="text-xs text-gray-500">Unduh laporan format Data Karyawan </p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof feather !== 'undefined') feather.replace();

            const ctxHours = document.getElementById('chartPegawaiHours');
            if (ctxHours && typeof Chart !== 'undefined') {
                new Chart(ctxHours, {
                    type: 'bar',
                    data: {
                        labels: @json($data['chart_labels']),
                        datasets: [{
                            label: 'Durasi Kehadiran (Jam)',
                            data: @json($data['chart_hours']),
                            backgroundColor: '#4f46e5',
                            borderRadius: 8,
                            barPercentage: 0.55
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'top' }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                suggestedMax: 9,
                                title: { display: true, text: 'Jam Kerja' }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
