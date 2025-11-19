<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\PegawaiDetail;
use App\Models\Skp;
use App\Models\SkpProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkpController extends Controller
{
    public function index()
    {
        $atasanId = Auth::user()->id;

        $pegawai = PegawaiDetail::with('user', 'bidang')
            ->where('atasan_id', $atasanId)
            ->orderBy('nama_lengkap')
            ->paginate(15);

        return view('atasan.pegawai.index', compact('pegawai'));
    }

    public function showPegawai($id)
    {
        $pegawai = PegawaiDetail::with('user', 'bidang')->findOrFail($id);

        $skp = Skp::where('pegawai_id', $pegawai->id)
            ->orderBy('periode', 'desc')
            ->get();

        return view('atasan.pegawai.show', compact('pegawai', 'skp'));
    }

    public function showSkp($id)
    {
        $skp = Skp::with('pegawai', 'bidang')->findOrFail($id);

        $progres = SkpProgress::where('skp_id', $id)
            ->orderBy('tanggal_update', 'desc')
            ->get();

        return view('atasan.skp.show', compact('skp', 'progres'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Draft,Diajukan,Dinilai,Selesai',
            'catatan' => 'nullable|string',
        ]);

        $skp = Skp::findOrFail($id);
        $skp->status = $request->status;
        $skp->catatan = $request->catatan;
        $skp->save();

        return back()->with('success', 'Status SKP berhasil diperbarui.');
    }

    public function nilai(Request $request, $id)
    {
        $request->validate([
            'nilai_capaian' => 'required|numeric|min:0|max:100',
            'catatan' => 'nullable|string',
        ]);

        $skp = Skp::findOrFail($id);
        $skp->nilai_capaian = $request->nilai_capaian;
        $skp->status = 'Dinilai';
        $skp->catatan = $request->catatan;
        $skp->save();

        return back()->with('success', 'Nilai SKP berhasil diberikan.');
    }
}
