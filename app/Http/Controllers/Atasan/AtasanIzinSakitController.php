<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\IzinSakit;
use Illuminate\Http\Request;

class AtasanIzinSakitController extends Controller
{
    public function index()
    {
        $data = IzinSakit::with('pegawai.user')
            ->latest()
            ->paginate(20);

        return view('atasan.izin.index', compact('data'));
    }

    public function show($id)
    {
        $izin = IzinSakit::with('pegawai.user')->findOrFail($id);

        return view('atasan.izin.show', compact('izin'));
    }

    public function approve($id)
    {
        $izin = IzinSakit::findOrFail($id);

        if ($izin->status !== 'menunggu') {
            return redirect()
                ->route('atasan.izin.index')
                ->with('error', 'Pengajuan sudah diproses sebelumnya.');
        }

        $izin->update([
            'status' => 'disetujui'
        ]);

        return redirect()
            ->route('atasan.izin.index')
            ->with('success', 'Pengajuan berhasil disetujui.');
    }

    public function reject($id)
    {
        $izin = IzinSakit::findOrFail($id);

        if ($izin->status !== 'menunggu') {
            return redirect()
                ->route('atasan.izin.index')
                ->with('error', 'Pengajuan sudah diproses sebelumnya.');
        }

        $izin->update([
            'status' => 'ditolak'
        ]);

        return redirect()
            ->route('atasan.izin.index')
            ->with('success', 'Pengajuan berhasil ditolak.');
    }
}
