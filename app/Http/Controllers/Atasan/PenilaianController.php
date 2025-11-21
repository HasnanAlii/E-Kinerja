<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\PegawaiDetail;
use App\Models\Penilaian;
use App\Models\PeriodePenilaian;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenilaianController extends Controller
{

    public function index()
    {
        $atasan = Auth::user()->atasan;

        $periodeAktif = PeriodePenilaian::where('status_aktif', 1)
            ->orderBy('id', 'DESC')
            ->first();

        $bawahan = PegawaiDetail::where('atasan_id', $atasan->id)
            ->with('user')
            ->withCount([
                'penilaian as sudah_dinilai' => function ($q) use ($periodeAktif) {
                    $q->where('periode_id', $periodeAktif->id);
                }
            ])
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return view('atasan.penilaian.index', compact('periodeAktif', 'bawahan'));
    }

    public function downloadAll()
    {
        $atasan = Auth::user()->atasan;

        // Ambil periode aktif
        $periodeAktif = PeriodePenilaian::where('status_aktif', 1)->first();

        if (! $periodeAktif) {
            return redirect()->route('dashboard')
                ->with('sweet_alert', 'Periode penilaian aktif belum tersedia.');
        }

        // Cek apakah ada penilaian pada periode aktif
        $penilaianAda = Penilaian::where('periode_id', $periodeAktif->id)
            ->where('status', 1)
            ->exists();

        if (! $penilaianAda) {
            return redirect()->route('dashboard')
                 ->with('sweet_alert', 'Penilaian  belum tersedia.');
        }

        // Ambil pegawai bawahan
        $pegawaiAktif = PegawaiDetail::where('atasan_id', $atasan->id)->get();

        $pdf = Pdf::loadView('atasan.penilaian.pdf', [
            'pegawaiAktif' => $pegawaiAktif,
            'atasan'       => $atasan,
            'periode'      => $periodeAktif
        ])->setPaper('a4', 'portrait');

        return $pdf->download('laporan_penilaian_periode_aktif.pdf');
}

        public function download()
    {
        $pegawai = Auth::user()->detail;

        if (! $pegawai) {
            return redirect()->back()->with('sweet_alert', 'Data pegawai tidak ditemukan.');
        }

        // Periode aktif
        $periodeAktif = PeriodePenilaian::where('status_aktif', 1)->first();

        if (! $periodeAktif) {
            return redirect()->back()->with('sweet_alert', 'Periode penilaian belum aktif.');
        }

        // Ambil penilaian pegawai pada periode aktif
        $penilaian = Penilaian::where('pegawai_id', $pegawai->id)
            ->where('periode_id', $periodeAktif->id)
            ->where('status', 1)
            ->latest()
            ->first();

        if (! $penilaian) {
            return redirect()->back()
                ->with('sweet_alert', 'Penilaian Anda belum tersedia.');
        }

        // Ambil atasan dari pegawai
        $atasan = $pegawai->atasan?->user;

        if (! $atasan) {
            $atasan = (object)[ 'name' => 'Atasan Tidak Ditemukan' ];
        }

        $pdf = Pdf::loadView('pegawai.penilaian.pdf', [
            'pegawai'   => $pegawai,
            'penilaian' => $penilaian,
            'atasan'    => $atasan,
            'periode'   => $periodeAktif
        ])->setPaper('a4', 'portrait');

        $fileName = 'penilaian_' . str_replace(' ', '_', strtolower($pegawai->user->name)) . '.pdf';

        return $pdf->download($fileName);
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
            'atasan_id' => Auth::user()->atasan->id,
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
