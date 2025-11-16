<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\Aktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifikasiAktivitasController extends Controller
{
 
    public function index()
    {
        $atasan = Auth::user()->atasan;

        $data = Aktivitas::whereHas('pegawai', function ($q) use ($atasan) {
                $q->where('atasan_id', $atasan->id);
            })
            // ->where('status', 'menunggu')
            ->with('pegawai.user')
            ->latest()
            ->paginate(15);

        return view('atasan.verifikasi.index', compact('data'));
    }


    public function show($id)
    {
        $data = Aktivitas::findOrFail($id);

        return view('atasan.verifikasi.show', compact('data'));
    }

    public function approve($id)
    {
        $data = Aktivitas::findOrFail($id);

        $data->update([
            'status' => 'disetujui',
            'komentar_atasan' => 'Aktivitas disetujui'
        ]);

        return redirect()
            ->route('atasan.verifikasi.index')
            ->with('success', 'Aktivitas berhasil disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'komentar_atasan' => 'required'
        ]);

        $data = Aktivitas::findOrFail($id);

        $data->update([
            'status' => 'ditolak',
            'komentar_atasan' => $request->komentar_atasan
        ]);

        return redirect()
            ->route('atasan.verifikasi.index')
            ->with('success', 'Aktivitas ditolak.');
    }

    public function revisi(Request $request, $id)
    {
        $request->validate([
            'komentar_atasan' => 'required'
        ]);

        $data = Aktivitas::findOrFail($id);

        $data->update([
            'status' => 'revisi',
            'komentar_atasan' => $request->komentar_atasan
        ]);

        return redirect()
            ->route('atasan.verifikasi.index')
            ->with('success', 'Permintaan revisi dikirim ke pegawai.');
    }

}
