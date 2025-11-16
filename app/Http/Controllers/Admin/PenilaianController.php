<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penilaian;
use App\Models\PeriodePenilaian;

class PenilaianController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('role:admin');
    // }

    public function index()
    {
        $periode = PeriodePenilaian::all();

        $data = Penilaian::with(['pegawai.user', 'atasan', 'periode'])
            ->paginate(30);

        return view('admin.penilaian.index', compact('data', 'periode'));
    }

    public function show($id)
    {
        $data = Penilaian::with(['pegawai.user', 'atasan', 'periode'])
            ->findOrFail($id);

        return view('admin.penilaian.show', compact('data'));
    }
}
