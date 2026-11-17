<?php

namespace App\Http\Controllers;

use App\Models\IzinSakit;
use App\Models\Kehadiran;
use App\Models\PegawaiDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Dashboard Profesional Sistem Monitoring Kehadiran Karyawan PT Pou Yuen Indonesia.
 * Menyediakan grafik tren kehadiran, komposisi status, distribusi departemen,
 * progres standar 8 jam, dan riwayat kehadiran/izin terbaru.
 */
class DashboardController extends Controller
{
    public function index()
    {
        $user  = Auth::user();
        $today = Carbon::today();
        $role  = $user->hasRole('admin') ? 'admin' : ($user->hasRole('atasan') ? 'atasan' : 'pegawai');

        /* ============================================================
         * 1. DASHBOARD KARYAWAN (PEGAWAI)
         * ============================================================ */
        if ($role === 'pegawai') {
            $pegawai   = $user->detail;
            $pegawaiId = $pegawai->id ?? 0;

            $hariIni = Kehadiran::where('pegawai_id', $pegawaiId)
                ->whereDate('tanggal', $today)
                ->first();

            // Statistik Bulan Ini
            $bulanQuery = Kehadiran::where('pegawai_id', $pegawaiId)
                ->whereYear('tanggal', $today->year)
                ->whereMonth('tanggal', $today->month)
                ->get();

            $hadirBulanIni = $bulanQuery->where('jenis', 'hadir')->count();
            $izinBulanIni  = $bulanQuery->where('jenis', 'izin')->count();
            $sakitBulanIni = $bulanQuery->where('jenis', 'sakit')->count();
            $cutiBulanIni  = $bulanQuery->where('jenis', 'cuti')->count();
            $alphaBulanIni = $bulanQuery->where('jenis', 'alpha')->count();

            // Durasi & Progres 8 Jam Hari Ini (dari cctv_monitoring_logs atau selisih check_in/check_out)
            $cctvLog = null;
            try {
                $cctvLog = DB::table('cctv_monitoring_logs')
                    ->where('pegawai_id', $pegawaiId)
                    ->where('tanggal', $today->toDateString())
                    ->first();
            } catch (\Throwable $e) {
                $cctvLog = null;
            }

            $durasiDetik = $cctvLog ? (int) $cctvLog->durasi_stay : 0;
            if ($durasiDetik === 0 && $hariIni && $hariIni->check_in && $hariIni->check_out) {
                $durasiDetik = max(0, Carbon::parse($hariIni->check_in)->diffInSeconds(Carbon::parse($hariIni->check_out), false));
            }
            $standarDetik = 8 * 3600; // 8 Jam = 28.800 detik
            $progres8Jam  = min(100, round(($durasiDetik / $standarDetik) * 100, 1));
            $jamKerjaStr  = floor($durasiDetik / 3600) . 'j ' . floor(($durasiDetik % 3600) / 60) . 'm';

            // Tren 7 Hari Terakhir (Jam Kerja / Status Kehadiran)
            $chartLabels = [];
            $chartHours  = [];
            for ($i = 6; $i >= 0; $i--) {
                $d = $today->copy()->subDays($i);
                $chartLabels[] = $d->translatedFormat('d M');

                $k = Kehadiran::where('pegawai_id', $pegawaiId)->whereDate('tanggal', $d)->first();
                $sec = 0;
                try {
                    $logD = DB::table('cctv_monitoring_logs')
                        ->where('pegawai_id', $pegawaiId)
                        ->where('tanggal', $d->toDateString())
                        ->first();
                    if ($logD) {
                        $sec = (int) $logD->durasi_stay;
                    }
                } catch (\Throwable $e) {}

                if ($sec === 0 && $k && $k->check_in && $k->check_out) {
                    $sec = max(0, Carbon::parse($k->check_in)->diffInSeconds(Carbon::parse($k->check_out), false));
                } elseif ($sec === 0 && $k && $k->jenis === 'hadir') {
                    $sec = 8 * 3600; // fallback visual bila hadir tercatat
                }
                $chartHours[] = round($sec / 3600, 2);
            }

            // Riwayat Kehadiran Terbaru (7 record terakhir)
            $riwayatKehadiran = Kehadiran::where('pegawai_id', $pegawaiId)
                ->orderBy('tanggal', 'desc')
                ->take(7)
                ->get();

            // Pengajuan Izin/Sakit/Cuti Terbaru
            $riwayatIzin = IzinSakit::where('pegawai_id', $pegawaiId)
                ->latest()
                ->take(5)
                ->get();

            $data = [
                'pegawai'            => $pegawai,
                'hari_ini'           => $hariIni,
                'status_hari_ini'    => $hariIni ? ucfirst($hariIni->jenis ?? 'Hadir') : 'Belum Terdeteksi',
                'jam_masuk'          => $hariIni && $hariIni->check_in ? Carbon::parse($hariIni->check_in)->format('H:i') : '-',
                'jam_pulang'         => $hariIni && $hariIni->check_out ? Carbon::parse($hariIni->check_out)->format('H:i') : '-',
                'durasi_hari_ini'    => $jamKerjaStr,
                'progres_8_jam'      => $progres8Jam,
                'hadir_bulan_ini'    => $hadirBulanIni,
                'izin_bulan_ini'     => $izinBulanIni,
                'sakit_bulan_ini'    => $sakitBulanIni,
                'cuti_bulan_ini'     => $cutiBulanIni,
                'alpha_bulan_ini'    => $alphaBulanIni,
                'chart_labels'       => $chartLabels,
                'chart_hours'        => $chartHours,
                'riwayat_kehadiran'  => $riwayatKehadiran,
                'riwayat_izin'       => $riwayatIzin,
            ];

            return view('dashboard.pegawai', compact('data'));
        }

        /* ============================================================
         * 2. DASHBOARD ATASAN & ADMIN
         * ============================================================ */
        $allPegawai = PegawaiDetail::with('user')->get();
        $totalKaryawan = $allPegawai->count();

        $kehadiranHariIni = Kehadiran::with('pegawai.user')
            ->whereDate('tanggal', $today)
            ->get();

        $hadirHariIni = $kehadiranHariIni->where('jenis', 'hadir')->count();
        $izinHariIni  = $kehadiranHariIni->where('jenis', 'izin')->count();
        $sakitHariIni = $kehadiranHariIni->where('jenis', 'sakit')->count();
        $cutiHariIni  = $kehadiranHariIni->where('jenis', 'cuti')->count();
        $alphaHariIni = $kehadiranHariIni->where('jenis', 'alpha')->count();
        $belumHadir   = max(0, $totalKaryawan - ($hadirHariIni + $izinHariIni + $sakitHariIni + $cutiHariIni));

        $pengajuanMenunggu = IzinSakit::with('pegawai.user')
            ->where('status', 'menunggu')
            ->latest()
            ->get();

        $totalZonaAktif = 0;
        try {
            $totalZonaAktif = DB::table('zona_kameras')->count();
        } catch (\Throwable $e) {
            $totalZonaAktif = 0;
        }

        // Tren Kehadiran 7 Hari Terakhir
        $trendLabels = [];
        $trendHadir  = [];
        $trendIzinSakitCuti = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = $today->copy()->subDays($i);
            $trendLabels[] = $d->translatedFormat('d M');
            $dayRows = Kehadiran::whereDate('tanggal', $d)->get();
            $trendHadir[] = $dayRows->where('jenis', 'hadir')->count();
            $trendIzinSakitCuti[] = $dayRows->whereIn('jenis', ['izin', 'sakit', 'cuti'])->count();
        }

        // Rekap Bulan Ini untuk Komposisi Doughnut Chart
        $bulanRows = Kehadiran::whereYear('tanggal', $today->year)
            ->whereMonth('tanggal', $today->month)
            ->get();

        $rekapBulan = [
            'hadir' => $bulanRows->where('jenis', 'hadir')->count(),
            'izin'  => $bulanRows->where('jenis', 'izin')->count(),
            'sakit' => $bulanRows->where('jenis', 'sakit')->count(),
            'cuti'  => $bulanRows->where('jenis', 'cuti')->count(),
            'alpha' => $bulanRows->where('jenis', 'alpha')->count(),
        ];

        // Distribusi Karyawan per Departemen (PT Pou Yuen)
        $deptCounts = $allPegawai
            ->groupBy(fn ($p) => strtoupper($p->departemen ?: 'UMUM'))
            ->map(fn ($group) => $group->count())
            ->sortDesc()
            ->take(6);

        // Daftar Kehadiran / Deteksi Terbaru
        $kehadiranTerbaru = Kehadiran::with('pegawai.user')
            ->orderBy('tanggal', 'desc')
            ->orderBy('updated_at', 'desc')
            ->take(8)
            ->get();

        $data = [
            'role'               => $role,
            'total_karyawan'     => $totalKaryawan,
            'hadir_hari_ini'     => $hadirHariIni,
            'izin_sakit_cuti'    => $izinHariIni + $sakitHariIni + $cutiHariIni,
            'belum_terdeteksi'   => $belumHadir,
            'pengajuan_menunggu' => $pengajuanMenunggu->count(),
            'list_menunggu'      => $pengajuanMenunggu->take(5),
            'total_zona'         => $totalZonaAktif,
            'trend_labels'       => $trendLabels,
            'trend_hadir'        => $trendHadir,
            'trend_izin'         => $trendIzinSakitCuti,
            'rekap_bulan'        => $rekapBulan,
            'dept_labels'        => $deptCounts->keys()->values()->all(),
            'dept_values'        => $deptCounts->values()->all(),
            'kehadiran_terbaru'  => $kehadiranTerbaru,
        ];

        return view('dashboard.atasan', compact('data'));
    }
}
