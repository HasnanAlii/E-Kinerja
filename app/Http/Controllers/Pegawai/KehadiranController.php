<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Kehadiran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KehadiranController extends Controller
{
    public function index()
    {
        $pegawai = Auth::user()->detail;

        $data = Kehadiran::where('pegawai_id', $pegawai->id)
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        return view('pegawai.kehadiran.index', compact('data'));
    }

    public function checkIn()
    {
        $pegawai = Auth::user()->detail;

        Kehadiran::create([
            'pegawai_id' => $pegawai->id,
            'tanggal' => date('Y-m-d'),
            'check_in' => now(),
        ]);

        return back()->with('success', 'Check-in berhasil.');
    }

public function checkOut()
{
    $pegawai = Auth::user()->detail;

    // Ambil data check-in terakhir
    $data = Kehadiran::where('pegawai_id', $pegawai->id)
        ->orderBy('check_in', 'desc')
        ->first();

    // Tidak ada data sama sekali
    if (!$data) {
        return back()->with('error', 'Anda belum pernah check-in.');
    }

    // Pastikan masih hari ini
    if ($data->tanggal !== date('Y-m-d')) {
        return back()->with('error', 'Check-Out hanya untuk check-in hari ini.');
    }

    // Pastikan belum check-out
    if ($data->check_out !== null) {
        return back()->with('error', 'Anda sudah melakukan check-out.');
    }

    // Update check-out
    $data->update([
        'check_out' => now()
    ]);

    return back()->with('success', 'Check-out berhasil.');
}

}
