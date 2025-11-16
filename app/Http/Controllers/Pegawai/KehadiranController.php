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

        // Cek apakah sudah absen masuk hari ini
        $cekHariIni = Kehadiran::where('pegawai_id', $pegawai->id)
            ->where('tanggal', date('Y-m-d'))
            ->first();

        if ($cekHariIni) {
            return back()->with('error', 'Anda sudah melakukan absen masuk hari ini.');
        }

        Kehadiran::create([
            'pegawai_id' => $pegawai->id,
            'tanggal' => date('Y-m-d'),
            'check_in' => now(),
        ]);

        return back()->with('success', 'Absen masuk berhasil!');
    }

    public function checkOut()
    {
        $pegawai = Auth::user()->detail;

        $data = Kehadiran::where('pegawai_id', $pegawai->id)
            ->where('tanggal', date('Y-m-d'))
            ->first();

        if (!$data) {
            return back()->with('error', 'Anda belum melakukan absen masuk hari ini.');
        }

        if ($data->check_out !== null) {
            return back()->with('error', 'Anda sudah melakukan absen pulang hari ini.');
        }

        $data->update([
            'check_out' => now()
        ]);

        return back()->with('success', 'Absen pulang berhasil!');
    }


}
