<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\IzinSakit;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    Notification::create([
        'user_id'   => $izin->pegawai->user_id, // user pegawai
        'aktivitas' => "Pengajuan izin/ sakit Anda telah disetujui.",
        'waktu'     => now(),
    ]);


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
        // Kirim notifikasi ke pegawai
        Notification::create([
            'user_id'   => $izin->pegawai->user_id,
            'aktivitas' => "Pengajuan izin Anda ditolak oleh atasan.",
            'waktu'     => now(),
        ]);

        return redirect()
            ->route('atasan.izin.index')
            ->with('success', 'Pengajuan berhasil ditolak.');
    }





// public function approve($id)
// {
//     $izin = IzinSakit::findOrFail($id);

//     if ($izin->status !== 'menunggu') {
//         session()->flash('notif', 'Pengajuan sudah diproses sebelumnya.');
//         return redirect()->route('atasan.izin.index');
//     }

//     // Update status
//     $izin->update(['status' => 'disetujui']);

//     // Generate tanggal range
//     $start = \Carbon\Carbon::parse($izin->tanggal_mulai);
//     $end   = \Carbon\Carbon::parse($izin->tanggal_selesai);

//     // Loop tanggal
//     for ($date = $start; $date->lte($end); $date->addDay()) {

//         $existing = \App\Models\Kehadiran::where('pegawai_id', $izin->pegawai_id)
//             ->where('tanggal', $date->format('Y-m-d'))
//             ->first();

//         if (!$existing) {
//             \App\Models\Kehadiran::create([
//                 'pegawai_id' => $izin->pegawai_id,
//                 'tanggal'    => $date->format('Y-m-d'),
//                 'jenis'      => $izin->jenis,
//                 'check_in'   => null,
//                 'check_out'  => null,
//             ]);
//         }
//     }

//     // === AUDIT LOG ===
//     AuditLog::create([
//         'user_id'   => Auth::id(),
//         'aktivitas' => "Menyetujui pengajuan {$izin->jenis} pegawai ID {$izin->pegawai_id} pada tanggal {$izin->tanggal_mulai} - {$izin->tanggal_selesai}.",
//     ]);

//     // === FLASH NOTIFIKASI ===
//     session()->flash('pegawai', 'Pengajuan Izin/Sakit disetujui.');

//     return redirect()->route('atasan.izin.index');
// }

// public function reject($id)
// {
//     $izin = IzinSakit::findOrFail($id);

//     if ($izin->status !== 'menunggu') {
//         session()->flash('notif', 'Pengajuan sudah diproses sebelumnya.');
//         return redirect()->route('atasan.izin.index');
//     }

//     // Update status -> ditolak
//     $izin->update(['status' => 'ditolak']);

//     // === AUDIT LOG ===
//     AuditLog::create([
//         'user_id'   => Auth::id(),
//         'aktivitas' => "Menolak pengajuan {$izin->jenis} pegawai ID {$izin->pegawai_id} tanggal {$izin->tanggal_mulai} - {$izin->tanggal_selesai}.",
//     ]);

//     // === FLASH NOTIFIKASI ===
//     session()->flash('pegawai', 'Pengajuan Izin/Sakit ditolak.');

//     return redirect()->route('atasan.izin.index');
// }

}
