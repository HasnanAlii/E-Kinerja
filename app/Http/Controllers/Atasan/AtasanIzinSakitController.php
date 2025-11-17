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

    // Update status izin menjadi disetujui
    $izin->update([
        'status' => 'disetujui'
    ]);

    // Generate tanggal range
    $start = \Carbon\Carbon::parse($izin->tanggal_mulai);
    $end   = \Carbon\Carbon::parse($izin->tanggal_selesai);

    // Loop setiap hari dalam rentang tanggal
    for ($date = $start; $date->lte($end); $date->addDay()) {

        // Cegah duplikasi kehadiran pada tanggal yang sama
        $existing = \App\Models\Kehadiran::where('pegawai_id', $izin->pegawai_id)
            ->where('tanggal', $date->format('Y-m-d'))
            ->first();

        if (!$existing) {
            \App\Models\Kehadiran::create([
                'pegawai_id' => $izin->pegawai_id,
                'tanggal'    => $date->format('Y-m-d'),
                'jenis'      => $izin->jenis,   // <-- sakit / izin 
                'check_in'   => null,
                'check_out'  => null,
            ]);
        }
    }

    return redirect()
        ->route('atasan.izin.index')
        ->with('success', 'Pengajuan berhasil disetujui & tercatat di kehadiran.');
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
