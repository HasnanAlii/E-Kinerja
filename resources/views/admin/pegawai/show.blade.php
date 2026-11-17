<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Profil &amp; Riwayat Kehadiran Karyawan
            </h2>

            <nav class="flex text-sm font-medium text-gray-500 mt-2 sm:mt-0">
                <a href="{{ route('admin.pegawai.index') }}" class="hover:text-indigo-600 transition">Data Karyawan</a>
                <span class="mx-2">/</span>
                <span class="text-indigo-600 font-semibold">{{ $data->user->name }}</span>
            </nav>
        </div>
    </x-slot>

    @php
        $kehadiranList = $data->kehadiran ?? collect();
        $jmlHadir = $kehadiranList->where('jenis', 'hadir')->count();
        $jmlIzin  = $kehadiranList->where('jenis', 'izin')->count();
        $jmlSakit = $kehadiranList->where('jenis', 'sakit')->count();
        $jmlCuti  = $kehadiranList->where('jenis', 'cuti')->count();
        $jmlAlpha = $kehadiranList->where('jenis', 'alpha')->count();
    @endphp

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- TOMBOL KEMBALI --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.pegawai.index') }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:text-indigo-600 hover:border-indigo-200 shadow-2xs transition">
                <i data-feather="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Daftar Karyawan</span>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- KOLOM KIRI: KARTU IDENTITAS UTAMA --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white shadow-sm rounded-2xl overflow-hidden border border-slate-200/90 relative">
                    <div class="h-28 bg-gradient-to-r from-indigo-600 via-blue-600 to-indigo-700"></div>

                    <div class="px-6 pb-6 text-center relative">
                        <div class="-mt-14 mb-4 flex justify-center">
                            <div class="relative">
                                @if ($data->user->profile_photo)
                                    <img src="{{ asset('storage/' . $data->user->profile_photo) }}"
                                         class="w-28 h-28 rounded-2xl object-cover border-4 border-white shadow-md">
                                @else
                                    <div class="w-28 h-28 rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white font-extrabold text-3xl flex items-center justify-center border-4 border-white shadow-md">
                                        {{ strtoupper(substr($data->user->name, 0, 2)) }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Nama & Jabatan --}}
                        <h3 class="text-xl font-extrabold text-slate-800">{{ $data->user->name }}</h3>
                        <p class="text-sm text-indigo-600 font-semibold mt-1">{{ $data->jabatan ?? '— Jabatan belum diisi —' }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $data->departemen ?? '-' }} {{ ($data->divisi && $data->divisi !== '-') ? '• ' . $data->divisi : '' }}</p>

                        <div class="mt-3 flex justify-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                {{ $data->status ?? 'Masih Bekerja' }}
                            </span>
                        </div>

                        <div class="mt-6">
                            <a href="{{ route('laporan.kehadiran.perpegawai', ['pegawai_id' => $data->id, 'tanggal_dari' => request('tanggal_dari'), 'tanggal_sampai' => request('tanggal_sampai')]) }}"
                               target="_blank"
                               class="flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-600 text-white text-sm font-bold rounded-xl hover:bg-rose-700 transition shadow-sm">
                                <i data-feather="printer" class="w-4 h-4"></i>
                                <span>Cetak Laporan PDF</span>
                            </a>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="mt-3 grid grid-cols-2 gap-3">
                            <a href="{{ route('admin.pegawai.edit', $data->id) }}"
                               class="flex items-center justify-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition shadow-2xs">
                                <i data-feather="edit-2" class="w-4 h-4"></i>
                                <span>Edit</span>
                            </a>

                            <form action="{{ route('admin.pegawai.destroy', $data->id) }}" method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus karyawan ini?');">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="w-full flex items-center justify-center gap-1.5 px-4 py-2 bg-white text-rose-600 border border-rose-200 text-sm font-semibold rounded-xl hover:bg-rose-50 transition shadow-2xs">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                    <span>Hapus</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: DETAIL INFORMASI KARYAWAN --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white shadow-sm rounded-2xl border border-slate-200/90 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/70 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                <i data-feather="briefcase" class="w-4 h-4"></i>
                            </div>
                            <h3 class="font-extrabold text-slate-800">Informasi Lengkap Karyawan PT Pou Yuen</h3>
                        </div>
                        <span class="font-mono text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 px-3 py-1 rounded-lg">
                            {{ $data->nik_karyawan ?? $data->nip ?? $data->user->nik ?? '-' }}
                        </span>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
                            <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-100">
                                <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nama Lengkap</span>
                                <span class="block text-sm font-bold text-slate-800">{{ $data->user->name }}</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-100">
                                <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">NIK Karyawan</span>
                                <span class="font-mono text-sm font-bold text-indigo-700">
                                    {{ $data->nik_karyawan ?? $data->nip ?? $data->user->nik ?? '-' }}
                                </span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-100">
                                <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Jenis Kelamin</span>
                                <span class="block text-sm font-bold text-slate-800">{{ $data->user->jenis_kelamin ?? '-' }}</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-100">
                                <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Departemen</span>
                                <span class="block text-sm font-bold text-slate-800">{{ $data->departemen ?? '-' }}</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-100">
                                <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Divisi</span>
                                <span class="block text-sm font-bold text-slate-800">{{ $data->divisi ?? '-' }}</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-100">
                                <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Jabatan</span>
                                <span class="block text-sm font-bold text-slate-800">{{ $data->jabatan ?? '-' }}</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-100">
                                <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Status Kerja</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                    {{ $data->status ?? 'Masih Bekerja' }}
                                </span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-100">
                                <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Email Akun</span>
                                <span class="block text-sm font-bold text-slate-800">{{ $data->user->email }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= RIWAYAT KEHADIRAN KARYAWAN ================= --}}
        <div class="bg-white shadow-sm rounded-2xl border border-slate-200/90 overflow-hidden">
            {{-- Header & Filter Tanggal --}}
            <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-indigo-600 via-blue-600 to-indigo-700 text-white flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-white/15 border border-white/25 flex items-center justify-center shrink-0">
                        <i data-feather="calendar" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-extrabold tracking-tight">Daftar Riwayat Kehadiran Karyawan</h3>
                        <p class="text-xs text-indigo-100 mt-0.5">
                            Rekam absensi otomatis kamera AI (Check-In, Check-Out &amp; Progres Durasi 8 Jam) milik <strong>{{ $data->user->name }}</strong>
                        </p>
                    </div>
                </div>

                <form method="GET" action="{{ route('admin.pegawai.show', $data->id) }}" class="flex flex-wrap items-center gap-2">
                    <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                           class="px-3 py-2 rounded-xl border-0 text-slate-800 text-xs font-semibold shadow-xs focus:ring-2 focus:ring-white">
                    <span class="text-xs font-semibold text-indigo-100">s/d</span>
                    <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                           class="px-3 py-2 rounded-xl border-0 text-slate-800 text-xs font-semibold shadow-xs focus:ring-2 focus:ring-white">
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-indigo-700 hover:bg-indigo-50 font-bold text-xs rounded-xl shadow-xs transition">
                        <i data-feather="filter" class="w-3.5 h-3.5"></i>
                        <span>Filter</span>
                    </button>
                    @if(request()->filled('tanggal_dari') || request()->filled('tanggal_sampai'))
                        <a href="{{ route('admin.pegawai.show', $data->id) }}"
                           class="inline-flex items-center gap-1 px-3 py-2 bg-white/20 hover:bg-white/30 text-white font-semibold text-xs rounded-xl transition">
                            <i data-feather="rotate-ccw" class="w-3.5 h-3.5"></i>
                            <span>Reset</span>
                        </a>
                    @endif
                </form>
            </div>

            {{-- Ringkasan Statistik Kehadiran Karyawan Ini --}}
            <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 grid grid-cols-2 sm:grid-cols-5 gap-3">
                <div class="bg-white rounded-xl p-3 border border-emerald-200/80 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500">Hadir</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-700">{{ $jmlHadir }} Hari</span>
                </div>
                <div class="bg-white rounded-xl p-3 border border-blue-200/80 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500">Izin</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-blue-100 text-blue-700">{{ $jmlIzin }} Hari</span>
                </div>
                <div class="bg-white rounded-xl p-3 border border-amber-200/80 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500">Sakit</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-700">{{ $jmlSakit }} Hari</span>
                </div>
                <div class="bg-white rounded-xl p-3 border border-purple-200/80 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500">Cuti</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-purple-100 text-purple-700">{{ $jmlCuti }} Hari</span>
                </div>
                <div class="bg-white rounded-xl p-3 border border-rose-200/80 flex items-center justify-between col-span-2 sm:col-span-1">
                    <span class="text-xs font-bold text-slate-500">Alpha</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-rose-100 text-rose-700">{{ $jmlAlpha }} Hari</span>
                </div>
            </div>

            {{-- Tabel List Kehadiran --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-100/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3.5 text-center w-14">No</th>
                            <th class="px-5 py-3.5">Hari &amp; Tanggal</th>
                            <th class="px-5 py-3.5 text-center">Status Kehadiran</th>
                            <th class="px-5 py-3.5 text-center">Jam Masuk (Check-In)</th>
                            <th class="px-5 py-3.5 text-center">Jam Pulang (Check-Out)</th>
                            <th class="px-5 py-3.5">Durasi Kamera &amp; Efektivitas (Target 8 Jam)</th>
                            <th class="px-5 py-3.5">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($kehadiranList as $idx => $k)
                            @php
                                $tglKey = \Carbon\Carbon::parse($k->tanggal)->toDateString();
                                $logCctv = isset($cctvLogs) ? $cctvLogs->get($tglKey) : null;
                                $durasiSec = $logCctv ? (int) $logCctv->durasi_stay : 0;

                                // Jika tidak ada log cctv tapi ada check_in & check_out, hitung selisih waktu
                                if ($durasiSec === 0 && $k->check_in && $k->check_out) {
                                    try {
                                        $tIn  = \Carbon\Carbon::parse($k->check_in);
                                        $tOut = \Carbon\Carbon::parse($k->check_out);
                                        if ($tOut->greaterThan($tIn)) {
                                            $durasiSec = $tIn->diffInSeconds($tOut);
                                        }
                                    } catch (\Throwable $e) {
                                        $durasiSec = 0;
                                    }
                                }

                                $jam   = intdiv($durasiSec, 3600);
                                $menit = intdiv($durasiSec % 3600, 60);
                                $detik = $durasiSec % 60;
                                $durasiStr = $jam > 0
                                    ? "{$jam} Jam {$menit} Menit"
                                    : ($menit > 0 ? "{$menit} Menit {$detik} Detik" : ($durasiSec > 0 ? "{$detik} Detik" : '-'));
                                $pct8Jam = min(100, round(($durasiSec / 28800) * 100, 1));

                                $badgeClass = match($k->jenis) {
                                    'hadir' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                    'izin'  => 'bg-blue-100 text-blue-700 border-blue-200',
                                    'sakit' => 'bg-amber-100 text-amber-700 border-amber-200',
                                    'cuti'  => 'bg-purple-100 text-purple-700 border-purple-200',
                                    default => 'bg-rose-100 text-rose-700 border-rose-200',
                                };
                            @endphp
                            <tr class="hover:bg-indigo-50/40 transition-colors">
                                <td class="px-5 py-3.5 text-center font-semibold text-slate-400">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="font-bold text-slate-800">
                                        {{ \Carbon\Carbon::parse($k->tanggal)->translatedFormat('l, d F Y') }}
                                    </p>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border uppercase {{ $badgeClass }}">
                                        {{ ucfirst($k->jenis) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    @if($k->check_in)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg font-mono text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i data-feather="log-in" class="w-3.5 h-3.5"></i>
                                            {{ \Carbon\Carbon::parse($k->check_in)->format('H:i:s') }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    @if($k->check_out)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg font-mono text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            <i data-feather="log-out" class="w-3.5 h-3.5"></i>
                                            {{ \Carbon\Carbon::parse($k->check_out)->format('H:i:s') }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($k->jenis === 'hadir')
                                        <div class="max-w-xs">
                                            <div class="flex items-center justify-between text-xs mb-1">
                                                <span class="font-bold text-slate-700">{{ $durasiStr }}</span>
                                                <span class="font-extrabold {{ $pct8Jam >= 100 ? 'text-emerald-600' : 'text-indigo-600' }}">{{ $pct8Jam }}%</span>
                                            </div>
                                            <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden">
                                                <div class="h-2 rounded-full {{ $pct8Jam >= 100 ? 'bg-emerald-500' : 'bg-indigo-600' }}"
                                                     style="width: {{ $pct8Jam }}%"></div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400">Non-Hadir ({{ ucfirst($k->jenis) }})</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-xs text-slate-600">
                                    {{ $k->keterangan ?? ($k->jenis === 'hadir' ? 'Terdeteksi Kamera AI (YOLOv8 + CNN)' : '-') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-400">
                                        <i data-feather="calendar" class="w-10 h-10 mb-2 stroke-1"></i>
                                        <p class="text-sm font-semibold text-slate-600">Belum ada riwayat kehadiran untuk karyawan ini.</p>
                                        <p class="text-xs text-slate-400 mt-0.5">Data kehadiran akan otomatis tercatat saat karyawan terdeteksi pada kamera monitoring.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
