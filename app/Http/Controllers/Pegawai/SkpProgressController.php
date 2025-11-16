<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Skp;
use App\Models\SkpProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkpProgressController extends Controller
{
    public function index()
    {
        $pegawai = Auth::user()->detail;

        $data = SkpProgress::where('pegawai_id', $pegawai->id)
            ->with('skp')
            ->paginate(15);

        return view('pegawai.skp.progress', compact('data'));
    }

    public function create()
    {
        $pegawai = Auth::user()->detail;

        $skp = Skp::where('bidang_id', $pegawai->bidang_id)->get();

        return view('pegawai.skp.create', compact('skp'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'skp_id' => 'required|exists:skp,id',
            'persentase' => 'required|integer|min:0|max:100',
            'bukti_file' => 'nullable|file|mimes:jpg,png,pdf'
        ]);

        $file = null;
        if ($request->hasFile('bukti_file')) {
            $file = $request->file('bukti_file')->store('bukti_skp', 'public');
        }

        SkpProgress::create([
            'pegawai_id' => Auth::user()->detail->id,
            'skp_id' => $request->skp_id,
            'persentase' => $request->persentase,
            'bukti_file' => $file,
            'tanggal_update' => now()
        ]);

        return back()->with('success', 'Progress SKP berhasil ditambahkan.');
    }
}
