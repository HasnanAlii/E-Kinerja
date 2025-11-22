<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\Notification;
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

        if ($request->has('hasil_kerja')) {
            foreach ($request->hasil_kerja as $hkId => $feedback) {
                SkpHasilKerja::where('id', $hkId)->update([
                    'umpan_balik' => $feedback ?? null,
                ]);
            }
        }

        if ($request->has('rhk_pimpinan')) {
            foreach ($request->rhk_pimpinan as $hkId => $txt) {
                SkpHasilKerja::where('id', $hkId)->update([
                    'rhk_pimpinan' => $txt ?? null,
                ]);
            }
        }

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
            ->whereIn('status', ['Diajukan', 'Disetujui','Final']) 
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

        return view('atasan.skp.show', compact('skp'));
    }

    /**
     * Update status SKP.
     */
    
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Draft,Diajukan,Disetujui,Selesai',
            'komentar_atasan' => $request->status === 'Draft' ? 'required|string' : 'nullable|string',
        ]);

        $skp = Skp::with('pegawai.user')->findOrFail($id);
        $oldStatus = $skp->status;

        $skp->status = $request->status;
        if ($request->status === 'Draft') {
            $skp->komentar_atasan = $request->komentar_atasan;
        }

        $skp->save();

        $pegawaiUser = $skp->pegawai->user ?? null;

        /* ===================
            Status -> Dinilai
        ===================== */
        if ($request->status === 'Disetujui' && $oldStatus !== 'Disetujui') {

            if ($pegawaiUser) {
                Notification::create([
                    'user_id'   => $pegawaiUser->id,
                    'aktivitas' => "SKP Anda telah Disetujui oleh atasan.",
                    'waktu'     => now(),
                ]);
            }
        }

        /* ===================
            Status -> Draft (Revisi)
        ===================== */
        if ($request->status === 'Draft' && $oldStatus !== 'Draft') {

            if ($pegawaiUser) {
                Notification::create([
                    'user_id'   => $pegawaiUser->id,
                    'aktivitas' => "SKP Anda dengan ID {$skp->id} dikembalikan untuk direvisi. Catatan: {$request->komentar_atasan}",
                    'waktu'     => now(),
                ]);
            }
        }

        return back()->with('success', 'Status SKP berhasil diperbarui.');
    }


 
    public function nilai(Request $request, $id)
    {
        $request->validate([
            'rating'   => 'required|string',
            'predikat' => 'required|string',
        ]);

        $skp = Skp::with('pegawai.user')->findOrFail($id);

        $skp->rating   = $request->rating;
        $skp->predikat = $request->predikat;
        $skp->status   = 'Final';
        $skp->save();

        // Kirim notifikasi ke pegawai
        $pegawaiUser = $skp->pegawai->user ?? null;

        if ($pegawaiUser) {
            Notification::create([
                'user_id'   => $pegawaiUser->id,
                'aktivitas' => "SKP Anda telah selesai dinilai. Rating: {$request->rating}, Predikat: {$request->predikat}.",
                'waktu'     => now(),
            ]);
        }

        return back()->with('success', 'Penilaian SKP berhasil disimpan dan divalidasi.');
    }
}
