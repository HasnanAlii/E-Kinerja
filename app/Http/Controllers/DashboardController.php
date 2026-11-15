<?php

namespace App\Http\Controllers;

use App\Models\IzinSakit;
use App\Models\Kehadiran;
use App\Models\PegawaiDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Dashboard khusus monitoring kehadiran.
 * (Statistik aktivitas/penilaian/SKP dinonaktifkan.)
 */
class DashboardController extends Controller
{
    public function index()
    {
        $user  = Auth::user();
        $today = Carbon::today();
        $role  = $user->hasRole('admin') ? 'admin' : ($user->hasRole('atasan') ? 'atasan' : 'pegawai');

        if ($role === 'pegawai') {
            $id = $user->detail->id ?? null;
            $bulan = Kehadiran::where('pegawai_id', $id)
                ->whereYear('tanggal', $today->year)->whereMonth('tanggal', $today->month);
            $hariIni = Kehadiran::where('pegawai_id', $id)->whereDate('tanggal', $today)->first();

            $stats = [
                ['Status Hari Ini', $hariIni ? ucfirst($hariIni->jenis ?? '-') : 'Belum terdeteksi', 'clock'],
                ['Jam Masuk', $hariIni && $hariIni->check_in ? substr($hariIni->check_in, 0, 5) : '-', 'log-in'],
                ['Jam Pulang', $hariIni && $hariIni->check_out ? substr($hariIni->check_out, 0, 5) : '-', 'log-out'],
                ['Hadir Bulan Ini', (clone $bulan)->where('jenis', 'hadir')->count() . ' hari', 'calendar'],
            ];
            $links = [
                ['Riwayat Kehadiran', 'Lihat kehadiran harian & bulanan.', route('pegawai.kehadiran.index'), 'calendar'],
                ['Ajukan Izin / Sakit / Cuti', 'Buat permohonan baru.', route('pegawai.izin.create'), 'file-plus'],
            ];
        } else {
            $ids = $role === 'atasan'
                ? PegawaiDetail::where('atasan_id', $user->atasan->id ?? 0)->pluck('id')
                : PegawaiDetail::pluck('id');
            $hari = Kehadiran::whereIn('pegawai_id', $ids)->whereDate('tanggal', $today);
            $stats = [
                ['Total Karyawan', $ids->count(), 'users'],
                ['Hadir Hari Ini', (clone $hari)->where('jenis', 'hadir')->count(), 'check-circle'],
                ['Izin/Sakit/Cuti Hari Ini', (clone $hari)->whereIn('jenis', ['izin', 'sakit', 'cuti'])->count(), 'file-text'],
                ['Pengajuan Menunggu', IzinSakit::whereIn('pegawai_id', $ids)->where('status', 'menunggu')->count(), 'inbox'],
            ];
            $links = $role === 'atasan' ? [
                ['Monitoring Kamera', 'Pantau kehadiran real-time.', route('atasan.monitoring.index'), 'video'],
                ['Rekap Kehadiran', 'Rekap harian & izin/sakit/cuti.', route('atasan.kehadiran.index'), 'calendar'],
                ['Laporan Kehadiran', 'Laporan per karyawan (PDF/Excel).', route('atasan.laporan.index'), 'file-text'],
            ] : [
                ['Data Karyawan', 'Kelola data karyawan.', route('admin.pegawai.index'), 'users'],
                ['Kehadiran Karyawan', 'Rekap kehadiran.', route('admin.kehadiran.index'), 'calendar'],
            ];
        }

        return view('dashboard.kehadiran', compact('stats', 'links', 'role'));
    }
}
