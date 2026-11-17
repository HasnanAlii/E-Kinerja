<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Kehadiran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KehadiranController extends Controller
{
    public function index(Request $request)
    {
        $pegawai = Auth::user()->detail;
        $mode    = $request->get('mode', 'bulanan'); // 'harian' | 'bulanan'
        $bulan   = $request->get('bulan', Carbon::today()->format('Y-m'));
        $tanggal = $request->get('tanggal', Carbon::today()->toDateString());

        $query = Kehadiran::with('izin')
            ->where('pegawai_id', $pegawai->id);

        if ($mode === 'harian') {
            $query->whereDate('tanggal', $tanggal);
        } else {
            $parsed = Carbon::parse($bulan . '-01');
            $query->whereYear('tanggal', $parsed->year)
                  ->whereMonth('tanggal', $parsed->month);
        }

        $data = $query->orderBy('tanggal', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('pegawai.kehadiran.index', compact('data', 'pegawai', 'mode', 'bulan', 'tanggal'));
    }



    /** DISABLED: absensi diatur otomatis oleh sistem monitoring kamera */
    public function checkIn()
    {
        abort(403, 'Absensi diatur otomatis oleh sistem monitoring.');
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
            'jenis' => 'hadir'
        ]);

        return back()->with('success', 'Absen masuk berhasil!');
    }

    public function checkOut()
    {
        abort(403, 'Absensi diatur otomatis oleh sistem monitoring.');
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
