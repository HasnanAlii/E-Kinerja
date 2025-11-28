<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\PegawaiDetail;
use App\Models\Skp;
use App\Models\SkpHasilKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;


class SkpController extends Controller
{
  
    public function editFeedback($id)
    {
        $skp = Skp::with(['pegawai.user', 'hasilKerja'])->findOrFail($id);

        
        return view('atasan.skp.feedback', compact('skp'));
    }

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

        if ($skp->pegawai->atasan_id !== Auth::user()->atasan->id) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        return view('atasan.skp.show', compact('skp'));
    }


    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'in:Draft,Diajukan,Revisi,Disetujui,Selesai'],
            'komentar_atasan' => ['required', 'string'],

        ]);
        $skp = Skp::with('pegawai.user')->findOrFail($id);
        $oldStatus = $skp->status;

        $skp->status = $request->status;

        $skp->komentar_atasan = $request->status === 'Revisi'
            ? $request->komentar_atasan
            : null;

        $skp->save();

        $pegawaiUser = $skp->pegawai->user ?? null;

        // ==========================
        // NOTIFIKASI: DISETUJUI
        // ==========================
        if ($request->status === 'Disetujui' && $oldStatus !== 'Disetujui') {
            if ($pegawaiUser) {
                Notification::create([
                    'user_id' => $pegawaiUser->id,
                    'aktivitas' => "SKP Anda telah Disetujui oleh atasan.",
                    'waktu' => now(),
                ]);
            }
        }

        // ==========================
        // NOTIFIKASI: REVISI
        // ==========================
        if ($request->status === 'Revisi' && $oldStatus !== 'Revisi') {
            if ($pegawaiUser) {
                Notification::create([
                    'user_id' => $pegawaiUser->id,
                    'aktivitas' => "SKP Anda (ID {$skp->id}) dikembalikan untuk revisi. Catatan: {$request->komentar_atasan}",
                    'waktu' => now(),
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
