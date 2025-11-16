<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\IzinSakit;
use Illuminate\Http\Request;

class AtasanIzinSakitController extends Controller
{
    // Menampilkan semua pengajuan izin/sakit dari seluruh pegawai
    public function index()
    {
        $data = IzinSakit::with('pegawai.user')
            ->latest()
            ->paginate(20);

        return view('atasan.izin.index', compact('data'));
    }

    // Detail pengajuan
    public function show($id)
    {
        $izin = IzinSakit::with('pegawai.user')->findOrFail($id);

        return view('atasan.izin.show', compact('izin'));
    }

    // Setujui pengajuan
    public function approve($id)
    {
        $izin = IzinSakit::findOrFail($id);

        if ($izin->status !== 'menunggu') {
            return back()->with('error', 'Pengajuan sudah diproses sebelumnya.');
        }

        $izin->update([
            'status' => 'disetujui'
        ]);

        return back()->with('success', 'Pengajuan berhasil disetujui.');
    }

    // Tolak pengajuan
    public function reject($id)
    {
        $izin = IzinSakit::findOrFail($id);

        if ($izin->status !== 'menunggu') {
            return back()->with('error', 'Pengajuan sudah diproses sebelumnya.');
        }

        $izin->update([
            'status' => 'ditolak'
        ]);

        return back()->with('success', 'Pengajuan berhasil ditolak.');
    }
}
