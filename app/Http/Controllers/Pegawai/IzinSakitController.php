<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\IzinSakit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IzinSakitController extends Controller
{
    public function index()
    {
        $pegawai = Auth::user()->detail;

        $data = IzinSakit::where('pegawai_id', $pegawai->id)->latest()->paginate(15);

        return view('pegawai.izin.index', compact('data'));
    }

    public function create()
    {
        return view('pegawai.izin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:izin,sakit',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'file_surat' => 'nullable|file|mimes:jpg,png,pdf'
        ]);

        $file = null;
        if ($request->hasFile('file_surat')) {
            $file = $request->file('file_surat')->store('surat_izin', 'public');
        }

        IzinSakit::create([
            'pegawai_id' => Auth::user()->detail->id,
            'jenis' => $request->jenis,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'file_surat' => $file,
            'status' => 'menunggu'
        ]);

        return redirect()->route('pegawai.izin.index')->with('success', 'Pengajuan izin/sakit berhasil.');
    }
}
