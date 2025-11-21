<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
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

        $pegawai = Auth::user()->detail;

        $izin = IzinSakit::create([
            'pegawai_id' => $pegawai->id,
            'jenis' => $request->jenis,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'file_surat' => $file,
            'status' => 'menunggu'
        ]);

   
        $atasanModel = $pegawai->atasan;

        if ($atasanModel) {

            $userAtasan = \App\Models\User::whereHas('atasan', function ($q) use ($atasanModel) {
                $q->where('id', $atasanModel->id);
            })->first();

            if ($userAtasan) {
                \App\Models\Notification::create([
                    'user_id'   => $userAtasan->id,
                    'aktivitas' => "{$pegawai->user->name} mengajukan {$izin->jenis} dari {$izin->tanggal_mulai} sampai {$izin->tanggal_selesai}.",
                    'waktu'     => now(),
                ]);
            }
        }

        return redirect()->route('pegawai.izin.index');
    }

}
