<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bidang;
use App\Models\Penilaian;
use App\Models\PeriodePenilaian;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('role:admin');
    // }

    public function index(Request $request)
    {
        $periode = PeriodePenilaian::all();
        $bidang = Bidang::all(); // untuk dropdown

        $query = Penilaian::with(['pegawai.user', 'atasan', 'periode']);

        // Jika user memilih bidang
        if ($request->bidang_id) {
            $query->whereHas('pegawai', function ($q) use ($request) {
                $q->where('bidang_id', $request->bidang_id);
            });
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

        // Jika tidak ada periode aktif
        if (!$periodeAktif) {
            return redirect()->back()->with('error', 'Tidak ada periode aktif!');
        }

        // Update semua penilaian yang berada dalam periode aktif
        Penilaian::where('periode_id', $periodeAktif->id)
            ->update(['status' => true]);

        return redirect()->route('admin.penilaian.index')
            ->with('success', 'Semua penilaian pada periode aktif berhasil divalidasi!');
    }

}
