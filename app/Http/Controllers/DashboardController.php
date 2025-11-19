<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\PegawaiDetail;
use App\Models\Bidang;
use App\Models\Penilaian;
use App\Models\Aktivitas;
use App\Models\Kehadiran;
use App\Models\PeriodePenilaian;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();


        /* ============================================================
         * 1. DASHBOARD ADMIN
         * ============================================================ */
        if ($user->hasRole('admin')) {

            $totalPegawai = User::role('pegawai')->count();
            $totalBidang  = Bidang::count();

            $laporanBaru = Penilaian::whereMonth('created_at', Carbon::now()->month)
                ->where('status', true)
                ->count();

            // Jumlah aktivitas hari ini (semua pegawai)
            $aktivitasHariIni = Aktivitas::whereDate('created_at', $today)->count();

            $recentAktivitas = Aktivitas::with('pegawai.user')
                ->latest()
                ->take(5)
                ->get();

            $data = [
                'total_pegawai'      => $totalPegawai,
                'total_bidang'       => $totalBidang,
                'laporan_baru'       => $laporanBaru,
                'aktivitas'          => $recentAktivitas,
                'aktivitas_hari_ini' => $aktivitasHariIni
            ];

            return view('dashboard.admin', compact('data'));
        }


        /* ============================================================
         * 2. DASHBOARD ATASAN
         * ============================================================ */
        if ($user->hasRole('atasan')) {

            $atasanId = Auth::user()->atasan->id;
            $periodeAktif = PeriodePenilaian::where('status_aktif', 1)->first();

            if (!$periodeAktif) {
                return view('dashboard.atasan', [
                    'data' => [
                        'menunggu_validasi' => 0,
                        'total_bawahan'     => 0,
                        'kinerja_tim'       => 'Belum Ada Periode Penilaian',
                        'aktivitas'         => [],
                        'aktivitas_hari_ini' => 0
                    ]
                ]);
            }

            // Total bawahan
            $totalBawahan = PegawaiDetail::where('atasan_id', $atasanId)->count();

            // Pegawai belum dinilai
            $menungguValidasi = PegawaiDetail::where('atasan_id', $atasanId)
                ->whereHas('penilaian', function($q) use ($periodeAktif) {
                    $q->where('periode_id', $periodeAktif->id)
                      ->whereNull('nilai_total');
                })
                ->orWhereDoesntHave('penilaian', function($q) use ($periodeAktif) {
                    $q->where('periode_id', $periodeAktif->id);
                })
                ->count();

            // Total nilai
            $totalNilai = Penilaian::where('atasan_id', $atasanId)
                ->where('periode_id', $periodeAktif->id)
                ->sum('nilai_total');

            $rataKinerja = $totalBawahan > 0 
                ? round($totalNilai / $totalBawahan, 2)
                : 0;

            $predikatTim = $this->getPredikat($rataKinerja);

            // Aktivitas terbaru
            $recentAktivitas = Aktivitas::whereHas('pegawai', function($q) use ($atasanId) {
                    $q->where('atasan_id', $atasanId);
                })
                ->latest()
                ->take(5)
                ->get();

            // Jumlah aktivitas bawahan hari ini
            $aktivitasHariIni = Aktivitas::whereDate('created_at', $today)
                ->whereHas('pegawai', function($q) use ($atasanId) {
                    $q->where('atasan_id', $atasanId);
                })
                ->count();

            $data = [
                'menunggu_validasi' => $menungguValidasi,
                'total_bawahan'     => $totalBawahan,
                'kinerja_tim'       => $predikatTim,
                'aktivitas'         => $recentAktivitas,
                'aktivitas_hari_ini' => $aktivitasHariIni
            ];

            return view('dashboard.atasan', compact('data'));
        }


        /* ============================================================
         * 3. DASHBOARD PEGAWAI
         * ============================================================ */

        $pegawaiId = $user->detail->id ?? null;

        $kehadiran = Kehadiran::where('pegawai_id', $pegawaiId)
            ->whereMonth('tanggal', Carbon::now()->month)
            ->count();

        $totalLaporan = Penilaian::where('pegawai_id', $pegawaiId)->count();

        $nilaiTerakhir = Penilaian::where('pegawai_id', $pegawaiId)
            ->latest()
            ->value('kategori');

        // Aktivitas terbaru
        $recentAktivitas = Aktivitas::where('pegawai_id', $pegawaiId)
            ->latest()
            ->take(5)
            ->get();

        // Jumlah aktivitas hari ini untuk pegawai
        $aktivitasHariIni = Aktivitas::where('pegawai_id', $pegawaiId)
            ->whereDate('created_at', $today)
            ->count();

        $data = [
            'kehadiran_bulan_ini' => $kehadiran,
            'total_laporan'       => $totalLaporan,
            'nilai_terakhir'      => $nilaiTerakhir ?? '-',
            'aktivitas'           => $recentAktivitas,
            'aktivitas_hari_ini'  => $aktivitasHariIni
        ];

        return view('dashboard.pegawai', compact('data'));
    }


    /* ============================================================
     * FUNGSI PREDIKAT (SKALA 1–5)
     * ============================================================ */
    private function getPredikat($nilai)
    {
        if ($nilai >= 4.5) {
            return 'Sangat Baik';
        } elseif ($nilai >= 3.2) {
            return 'Baik';
        } elseif ($nilai >= 2.5) {
            return 'Cukup';
        } else {
            return 'Belum Dinilai';
        }
    }
}
