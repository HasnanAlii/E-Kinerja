<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\Kehadiran;
use App\Models\PegawaiDetail;
use Carbon\Carbon;

class AtasanMonitoringController extends Controller
{
    /** Halaman monitoring kamera (terpisah dari rekap kehadiran) */
    public function index()
    {
        $data = Kehadiran::whereDate('tanggal', Carbon::today())->get();

        $pegawais = PegawaiDetail::select('pegawai_details.*')
            ->join('users', 'users.id', '=', 'pegawai_details.user_id')
            ->orderBy('users.name')
            ->get();

        return view('atasan.monitoring.index', compact('data', 'pegawais'));
    }
}
