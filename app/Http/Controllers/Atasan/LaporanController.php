<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\Penilaian;
use App\Models\PeriodePenilaian;
use App\Models\PegawaiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('role:atasan');
    // }

    public function index(Request $request)
    {
        $periode = PeriodePenilaian::all();

        $query = Penilaian::with(['pegawai.user', 'periode'])
            ->where('atasan_id', Auth::id());

        if ($request->periode_id) {
            $query->where('periode_id', $request->periode_id);
        }

        $data = $query->paginate(20);

        return view('atasan.laporan.index', compact('data', 'periode'));
    }

    public function show($id)
    {
        $data = Penilaian::with(['pegawai.user', 'periode'])->findOrFail($id);

        return view('atasan.laporan.show', compact('data'));
    }

    // Jika ingin export PDF:
    public function exportPdf(Request $request)
    {
        // nanti tinggal ditambahkan package dompdf
    }

    public function exportExcel(Request $request)
    {
        // tambahkan maatwebsite/excel jika diperlukan
    }
}
