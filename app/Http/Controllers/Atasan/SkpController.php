<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\PegawaiDetail;
use App\Models\Skp;
use App\Models\SkpHasilKerja;
use App\Models\SkpPerilaku;
use App\Models\SkpProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkpController extends Controller
{
    /**
     * Daftar pegawai bawahan yang dinaungi atasan.
     */
    /**
     * Menampilkan Form Edit Umpan Balik
     */
    public function editFeedback($id)
    {
        // Muat SKP beserta relasinya
        $skp = Skp::with(['pegawai.user', 'hasilKerja', 'perilaku'])->findOrFail($id);

        // Pastikan hanya atasan yang bisa akses (Opsional: Tambahkan logika policy di sini)
        
        return view('atasan.skp.feedback', compact('skp'));
    }

    /**
     * Menyimpan Data Umpan Balik
     */
public function updateFeedback(Request $request, $id)
{
    $skp = Skp::findOrFail($id);

    // 1. UPDATE UMPAN BALIK HASIL KERJA
    if ($request->has('hasil_kerja')) {
        foreach ($request->hasil_kerja as $hkId => $feedback) {
            SkpHasilKerja::where('id', $hkId)->update([
                'umpan_balik' => $feedback ?? null,
            ]);
        }
    }

    // 2. UPDATE RHK PIMPINAN (EDITABLE)
    if ($request->has('rhk_pimpinan')) {
        foreach ($request->rhk_pimpinan as $hkId => $txt) {
            SkpHasilKerja::where('id', $hkId)->update([
                'rhk_pimpinan' => $txt ?? null,
            ]);
        }
    }

    // // 3. UPDATE PERILAKU (Jika diaktifkan)
    // if ($request->has('perilaku')) {
    //     foreach ($request->perilaku as $pId => $feedback) {
    //         SkpPerilaku::where('id', $pId)->update([
    //             'umpan_balik' => $feedback ?? null,
    //         ]);
    //     }
    // }

    return redirect()
        ->route('atasan.skp.show', $id)
        ->with([
            'message' => 'Umpan balik berhasil disimpan.',
            'alert-type' => 'success'
        ]);
}

public function index()
{
    $atasanId = Auth::user()->atasan->id;

    // Ambil ID pegawai bawahan
    $pegawaiIds = PegawaiDetail::where('atasan_id', $atasanId)
        ->pluck('id');

    if ($pegawaiIds->isEmpty()) {
        return view('atasan.skp.index', [
            'skp' => collect([])->paginate(15),
            'message' => 'Tidak ada pegawai bawahan yang terdaftar.'
        ]);
    }

    // Ambil SKP bawahan yang statusnya DIajukan
    $skp = Skp::with(['pegawai', 'pegawai.user'])
        ->whereIn('pegawai_id', $pegawaiIds)
        ->whereIn('status', ['Diajukan', 'Dinilai','Final']) 
        ->orderBy('periode', 'desc')
        ->paginate(15);

    return view('atasan.skp.index', compact('skp'));
}

 
    public function showSkp($id)
    {
         $atasanId = Auth::user()->atasan->id;

        $skp = Skp::with(['pegawai', 'bidang'])->findOrFail($id);

        // Cek apakah SKP ini milik bawahan yang sesuai
        if ($skp->pegawai->atasan_id !== Auth::user()->atasan->id) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        $progres = SkpProgress::where('skp_id', $id)
            ->orderBy('tanggal_update', 'desc')
            ->get();

        return view('atasan.skp.show', compact('skp', 'progres'));
    }

    /**
     * Update status SKP.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Draft,Diajukan,Dinilai,Selesai',
        ]);

        $skp = Skp::findOrFail($id);

        $skp->status = $request->status;
        $skp->save();

        return back()->with('success', 'Status SKP berhasil diperbarui.');
    }

 
public function nilai(Request $request, $id)
{
    // 1. Validasi Input (Sesuai dengan name di Modal: rating & predikat)
    $request->validate([
        'rating'   => 'required|string', // Contoh: 'Sesuai Ekspektasi'
        'predikat' => 'required|string', // Contoh: 'Baik'
    ]);

    $skp = Skp::findOrFail($id);

    // 3. Simpan Penilaian
    $skp->rating   = $request->rating;
    $skp->predikat = $request->predikat;
    $skp->status   = 'Final'; // Ubah status menjadi Selesai (Final)
    $skp->save();

    // 4. Redirect kembali dengan pesan sukses
    return back()->with('success', 'Penilaian SKP berhasil disimpan dan divalidasi.');
}
}
