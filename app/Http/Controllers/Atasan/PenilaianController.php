<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\PegawaiDetail;
use App\Models\Penilaian;
use App\Models\PeriodePenilaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenilaianController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('role:atasan');
    // }
    public function index()
    {
        // Data atasan yang login
        $atasan = Auth::user()->atasan;

        // Periode penilaian yang aktif
        $periodeAktif = PeriodePenilaian::where('status_aktif', 1)
            ->orderBy('id', 'DESC')
            ->first();

        // Bawahan dengan pagination
        $bawahan = PegawaiDetail::where('atasan_id', $atasan->id)
            ->with('user')
            ->orderBy('id', 'DESC')
            ->paginate(10); // <--- PAGINATION DI SINI

        return view('atasan.penilaian.index', compact('periodeAktif', 'bawahan'));
    }


    public function create($pegawai_id)
    {
        $periode = PeriodePenilaian::where('status_aktif', true)->firstOrFail();

        $pegawai = PegawaiDetail::with('user')->findOrFail($pegawai_id);

        return view('atasan.penilaian.create', compact('pegawai', 'periode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai_details,id',
            'periode_id' => 'required|exists:periode_penilaian,id',
            'skp' => 'required|integer|min:1|max:5',
            'kedisiplinan' => 'required|integer|min:1|max:5',
            'perilaku' => 'required|integer|min:1|max:5',
            'komunikasi' => 'required|integer|min:1|max:5',
            'tanggung_jawab' => 'required|integer|min:1|max:5',
            'kerja_sama' => 'required|integer|min:1|max:5',
            'produktivitas' => 'required|integer|min:1|max:5',
        ]);

        // Hitung nilai total
        $nilaiTotal = (
            $request->skp +
            $request->kedisiplinan +
            $request->perilaku +
            $request->komunikasi +
            $request->tanggung_jawab +
            $request->kerja_sama +
            $request->produktivitas
        ) / 7;

        // Tentukan kategori
        if ($nilaiTotal >= 4.5) {
            $kategori = 'Sangat Baik';
        } elseif ($nilaiTotal >= 3.5) {
            $kategori = 'Baik';
        } elseif ($nilaiTotal >= 2.5) {
            $kategori = 'Cukup';
        } else {
            $kategori = 'Kurang';
        }

        Penilaian::create([
            'pegawai_id' => $request->pegawai_id,
            'atasan_id' => Auth::id(),
            'periode_id' => $request->periode_id,
            'skp' => $request->skp,
            'kedisiplinan' => $request->kedisiplinan,
            'perilaku' => $request->perilaku,
            'komunikasi' => $request->komunikasi,
            'tanggung_jawab' => $request->tanggung_jawab,
            'kerja_sama' => $request->kerja_sama,
            'produktivitas' => $request->produktivitas,
            'nilai_total' => $nilaiTotal,
            'kategori' => $kategori,
        ]);

        return redirect()->route('atasan.penilaian.index')
            ->with('success', 'Penilaian berhasil disimpan.');
    }
}
