<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Bidang;
use App\Models\Penilaian;
use App\Models\PeriodePenilaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenilaianController extends Controller
{

public function index(Request $request)
{
    $periode = PeriodePenilaian::all();
    $bidang = Bidang::all();

    $query = Penilaian::with(['pegawai.user', 'atasan', 'periode']);

    // Filter Bidang
    if ($request->bidang_id) {
        $query->whereHas('pegawai', function ($q) use ($request) {
            $q->where('bidang_id', $request->bidang_id);
        });
    }

    // 🔥 Filter Periode
    if ($request->periode_id) {
        $query->where('periode_id', $request->periode_id);
    }

    $data = $query->paginate(30);

    return view('admin.penilaian.index', compact('data', 'periode', 'bidang'));
}


    public function show($id)
    {
        $data = Penilaian::with(['pegawai.user', 'atasan', 'periode'])
            ->findOrFail($id);

        return view('admin.penilaian.show', compact('data'));
    }
    public function validasi()
    {
        // Ambil periode yang status_aktif = true
        $periodeAktif = PeriodePenilaian::where('status_aktif', true)->first();

        if (!$periodeAktif) {
            return redirect()->back()->with('error', 'Tidak ada periode aktif!');
        }

        // Update semua penilaian
        Penilaian::where('periode_id', $periodeAktif->id)
            ->update(['status' => true]);

        // ============================
        // NOTIFIKASI KE SEMUA PEGAWAI
        // ============================
        $semuaPegawai = \App\Models\User::role('pegawai')->get();

        foreach ($semuaPegawai as $pegawai) {
            \App\Models\Notification::create([
                'user_id'   => $pegawai->id,
                'aktivitas' => "Penilaian periode {$periodeAktif->nama_periode} telah divalidasi oleh admin.",
                'waktu'     => now(),
            ]);
        }

        // ============================
        // NOTIFIKASI KE SEMUA ATASAN
        // ============================
        $semuaAtasan = \App\Models\User::role('atasan')->get();

        foreach ($semuaAtasan as $atasan) {
            \App\Models\Notification::create([
                'user_id'   => $atasan->id,
                'aktivitas' => "Penilaian periode {$periodeAktif->nama_periode} telah divalidasi dan dapat dilihat.",
                'waktu'     => now(),
            ]);
        }

        return redirect()->route('admin.penilaian.index')
            ->with('success', 'Semua penilaian pada periode aktif berhasil divalidasi!');
    }

}
