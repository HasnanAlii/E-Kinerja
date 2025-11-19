<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\Aktivitas;
use App\Models\PegawaiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifikasiAktivitasController extends Controller
{
 
    public function index(Request $request)
    {
       
        $atasanId = Auth::user()->atasan->id; 

        $query = Aktivitas::query()
            ->with('pegawai.user') // Eager load untuk performa
            ->whereHas('pegawai', function ($q) use ($atasanId) {
                $q->where('atasan_id', $atasanId);
            });


        if ($request->filled('pegawai_id')) {
            $query->where('pegawai_id', $request->pegawai_id);
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        $data = $query->latest('tanggal')->paginate(15)->withQueryString(); // withQueryString agar filter tidak hilang saat klik halaman 2

        $pegawais = PegawaiDetail::where('atasan_id', $atasanId)
                    ->with('user')
                    ->get();

        return view('atasan.verifikasi.index', compact('data', 'pegawais'));
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
