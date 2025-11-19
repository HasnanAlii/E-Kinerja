<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aktivitas;
use App\Models\Bidang;
use Illuminate\Http\Request;
class AktivitasController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('role:admin');
    // }
    public function index(Request $request)
    {
        // Query awal
        $query = Aktivitas::with(['pegawai.user'])->latest();

        // Filter berdasarkan bidang
        if ($request->filled('bidang_id')) {
            $query->whereHas('pegawai', function ($q) use ($request) {
                $q->where('bidang_id', $request->bidang_id);
            });
        }

        // Hasil akhir + pagination tetap ikut query string
        $data = $query->paginate(30)->withQueryString();

        // Untuk dropdown
        $bidang = Bidang::all();

        return view('admin.aktivitas.index', compact('data', 'bidang'));
    }

    public function show($id)
    {
        $data = Aktivitas::with('pegawai.user')->findOrFail($id);

        return view('admin.aktivitas.show', compact('data'));
    }
}
