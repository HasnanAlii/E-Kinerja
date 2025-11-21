<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Skp;
use App\Models\SkpHasilKerja;
use App\Models\SkpPerilaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkpController extends Controller
{/**
     * 1. List Semua SKP (pegawai / atasan / admin)
     */
    public function index()
    {
        $user = Auth::user();

        // Pegawai → melihat SKP dirinya
        if ($user->hasRole('pegawai')) {
            $skp = Skp::where('pegawai_id', $user->detail->id)
                      ->latest()
                      ->paginate(10);
        }

        // Atasan → melihat SKP bawahan
        elseif ($user->hasRole('atasan')) {
            $skp = Skp::where('atasan_id', $user->pegawaiDetail->id)
                      ->latest()
                      ->paginate(10);
        }

        // Admin → melihat semua
        else {
            $skp = Skp::latest()->paginate(10);
        }

        return view('pegawai.skp.index', compact('skp'));
    }

    /**
     * 2. Form Create SKP
     */
    public function create()
    {
        return view('pegawai.skp.create');
    }

    /**
     * 3. Simpan SKP baru lengkap (header + hasil kerja + perilaku)
     */
    public function store(Request $request)
    {
        $request->validate([
            'periode' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
        ]);

        $pegawai = Auth::user()->detail;

        // Simpan Header SKP
        $skp = Skp::create([
            'pegawai_id' => $pegawai->id,
            'atasan_id'  => $pegawai->atasan_id,
            'bidang_id'  => $pegawai->bidang_id,

            'periode'    => $request->periode,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,

            'capaian_kinerja_organisasi' => $request->capaian_kinerja_organisasi,
            'pola_distribusi'            => $request->pola_distribusi,

            'status' => 'Draft'
        ]);

        /**
         * SIMPAN HASIL KERJA
         */
        if ($request->hasil_kerja) {
            foreach ($request->hasil_kerja as $item) {
                SkpHasilKerja::create([
                    'skp_id' => $skp->id,
                    'jenis'  => $item['jenis'],
                    'rhk_pimpinan' => $item['rhk_pimpinan'] ?? null,
                    'rhk' => $item['rhk'] ?? null,
                    'aspek' => $item['aspek'] ?? null,
                    'indikator_kinerja' => $item['indikator_kinerja'] ?? null,
                    'target' => $item['target'] ?? null,
                    'realisasi' => $item['realisasi'] ?? null,
                    'umpan_balik' => $item['umpan_balik'] ?? null,
                ]);
            }
        }

        /**
         * SIMPAN PERILAKU ASN
         */
        if ($request->perilaku) {
            foreach ($request->perilaku as $aspek => $value) {
                SkpPerilaku::create([
                    'skp_id' => $skp->id,
                    'aspek' => $aspek,
                    'perilaku' => $value['perilaku'] ?? null,
                    'ekspektasi' => $value['ekspektasi'] ?? null,
                    'umpan_balik' => $value['umpan_balik'] ?? null,
                ]);
            }
        }

        return redirect()->route('skp.index')
            ->with('success', 'SKP berhasil dibuat.');
    }

    /**
     * 4. Detail SKP Lengkap
     */
    public function show($id)
    {
        $data = Skp::with(['hasilKerja', 'perilaku', 'pegawai.user', 'bidang'])
                   ->findOrFail($id);

        return view('pegawai.skp.show', compact('data'));
    }

    /**
     * 5. Form Edit SKP
     */
    public function edit($id)
    {
        $data = Skp::with(['hasilKerja', 'perilaku'])->findOrFail($id);
        return view('pegawai.skp.edit', compact('data'));
    }

    /**
     * 6. Update SKP + update hasil kerja + update perilaku
     */
    public function update(Request $request, $id)
    {
        $skp = Skp::findOrFail($id);

        // update header
        $skp->update([
            'periode' => $request->periode,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'capaian_kinerja_organisasi' => $request->capaian_kinerja_organisasi,
            'pola_distribusi'            => $request->pola_distribusi,
        ]);

        /**
         * UPDATE BARIS HASIL KERJA
         */
        if ($request->hasil_kerja) {
            foreach ($request->hasil_kerja as $idRow => $item) {
                SkpHasilKerja::where('id', $idRow)->update([
                    'jenis'  => $item['jenis'],
                    'rhk_pimpinan' => $item['rhk_pimpinan'] ?? null,
                    'rhk' => $item['rhk'] ?? null,
                    'aspek' => $item['aspek'] ?? null,
                    'indikator_kinerja' => $item['indikator_kinerja'] ?? null,
                    'target' => $item['target'] ?? null,
                    'realisasi' => $item['realisasi'] ?? null,
                    'umpan_balik' => $item['umpan_balik'] ?? null,
                ]);
            }
        }

        /**
         * TAMBAH BARIS HASIL KERJA BARU
         */
        if ($request->new_hasil_kerja) {
            foreach ($request->new_hasil_kerja as $item) {
                SkpHasilKerja::create([
                    'skp_id' => $skp->id,
                    'jenis'  => $item['jenis'],
                    'rhk_pimpinan' => $item['rhk_pimpinan'] ?? null,
                    'rhk' => $item['rhk'] ?? null,
                    'aspek' => $item['aspek'] ?? null,
                    'indikator_kinerja' => $item['indikator_kinerja'] ?? null,
                    'target' => $item['target'] ?? null,
                    'realisasi' => $item['realisasi'] ?? null,
                    'umpan_balik' => $item['umpan_balik'] ?? null,
                ]);
            }
        }

        /**
         * UPDATE PERILAKU ASN
         */
        if ($request->perilaku) {
            foreach ($request->perilaku as $idRow => $item) {
                SkpPerilaku::where('id', $idRow)->update([
                    'perilaku' => $item['perilaku'],
                    'ekspektasi' => $item['ekspektasi'],
                    'umpan_balik' => $item['umpan_balik'],
                ]);
            }
        }

        return redirect()->route('skp.show', $id)
            ->with('success', 'SKP berhasil diperbarui.');
    }

    /**
     * 7. Hapus SKP
     */
    public function destroy($id)
    {
        Skp::findOrFail($id)->delete();
        return redirect()->route('skp.index')
                         ->with('success', 'Data SKP berhasil dihapus.');
    }

    /**
     * 8. Pegawai → Ajukan SKP
     */
    public function ajukan($id)
    {
        $skp = Skp::findOrFail($id);

        if ($skp->status !== 'Draft') {
            return back()->with('error', 'SKP tidak dapat diajukan.');
        }

        $skp->update(['status' => 'Diajukan']);

        return back()->with('success', 'SKP berhasil diajukan.');
    }

    /**
     * 9. Atasan → Menilai SKP
     */
    public function nilai(Request $request, $id)
    {
        $skp = Skp::findOrFail($id);

        $skp->update([
            'rating'   => $request->rating,
            'predikat' => $request->predikat,
            'status'   => 'Dinilai'
        ]);

        return back()->with('success', 'Penilaian berhasil disimpan.');
    }

    /**
     * 10. Admin → Finalisasi SKP
     */
    public function final($id)
    {
        $skp = Skp::findOrFail($id);

        if ($skp->status !== 'Dinilai') {
            return back()->with('error', 'SKP belum selesai dinilai.');
        }

        $skp->update(['status' => 'Final']);

        return back()->with('success', 'SKP telah difinalisasi.');
    }
}
