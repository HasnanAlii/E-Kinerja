<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\Kehadiran;
use App\Models\PegawaiDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Laporan kehadiran semua karyawan, difilter berdasarkan rentang tanggal.
 */
class LaporanController extends Controller
{
    private function rentang(Request $r): array
    {
        $dari   = $r->filled('tanggal_dari') ? Carbon::parse($r->tanggal_dari) : Carbon::today()->startOfMonth();
        $sampai = $r->filled('tanggal_sampai') ? Carbon::parse($r->tanggal_sampai) : Carbon::today();
        return [$dari->toDateString(), $sampai->toDateString()];
    }

    private function rekap(string $dari, string $sampai, ?string $cari = null)
    {
        $kehadiran = Kehadiran::whereBetween('tanggal', [$dari, $sampai])->get()->groupBy('pegawai_id');

        return PegawaiDetail::with('user')
            ->when($cari, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$cari}%")))
            ->get()->sortBy(fn ($p) => $p->user->name ?? '')->map(function ($p) use ($kehadiran) {
            $k = $kehadiran->get($p->id, collect());
            return (object) [
                'id'    => $p->id,
                'nama'  => $p->user->name ?? '-',
                'jabatan' => $p->jabatan,
                'hadir' => $k->where('jenis', 'hadir')->count(),
                'izin'  => $k->where('jenis', 'izin')->count(),
                'sakit' => $k->where('jenis', 'sakit')->count(),
                'cuti'  => $k->where('jenis', 'cuti')->count(),
                'alpha' => $k->where('jenis', 'alpha')->count(),
            ];
        })->values();
    }

    public function index(Request $request)
    {
        [$dari, $sampai] = $this->rentang($request);
        $rekap = $this->rekap($dari, $sampai, $request->cari);

        return view('atasan.laporan.index', compact('rekap', 'dari', 'sampai'));
    }

    public function show($id)
    {
        return redirect()->route('atasan.laporan.index');
    }

    public function exportPdf(Request $request)
    {
        [$dari, $sampai] = $this->rentang($request);
        $rekap = $this->rekap($dari, $sampai, $request->cari);

        return Pdf::loadView('atasan.laporan.pdf', compact('rekap', 'dari', 'sampai'))
            ->setPaper('a4', 'portrait')
            ->stream("Laporan_Kehadiran_{$dari}_{$sampai}.pdf");
    }

    /** Excel (CSV, dapat dibuka langsung di Excel) */
    public function exportExcel(Request $request)
    {
        [$dari, $sampai] = $this->rentang($request);
        $rekap = $this->rekap($dari, $sampai, $request->cari);

        return response()->streamDownload(function () use ($rekap) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['No', 'Nama', 'Hadir', 'Izin', 'Sakit', 'Cuti', 'Alpha']);
            foreach ($rekap as $i => $r) {
                fputcsv($out, [$i + 1, $r->nama, $r->hadir, $r->izin, $r->sakit, $r->cuti, $r->alpha]);
            }
            fclose($out);
        }, "Laporan_Kehadiran_{$dari}_{$sampai}.csv", ['Content-Type' => 'text/csv']);
    }
}
