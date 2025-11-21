<?php

namespace App\Http\Controllers;

use App\Models\Skp;
use App\Models\SkpHasilKerja; // Pastikan model ini sudah dibuat
use Illuminate\Http\Request;

class SkpHasilKerjaController extends Controller
{
    /**
     * Menampilkan Form Tambah Hasil Kerja
     */
    public function create($skpId)
    {
        $skp = Skp::findOrFail($skpId);
        return view('pegawai.skp.rhk', compact('skp'));
    }

    /**
     * Menyimpan Data ke Database
     */
    public function store(Request $request, $skpId)
    {
        $request->validate([
            'jenis'             => 'required|in:Utama,Hasil Kerja', // Sesuai enum migration
            'rhk_pimpinan'      => 'nullable|string',
            'rhk'               => 'required|string',
            'aspek'             => 'required|string', // Kuantitas/Kualitas/Waktu
            'indikator_kinerja' => 'required|string',
            'target'            => 'required|string',
            // Realisasi & Umpan Balik opsional (bisa diisi nanti)
            'realisasi'         => 'nullable|string',
            'umpan_balik'       => 'nullable|string',
        ]);

        SkpHasilKerja::create([
            'skp_id'            => $skpId,
            'jenis'             => $request->jenis,
            'rhk_pimpinan'      => 'Terwujudnya Pengelolaan Manajenen Perkantoran Dinas aris dan Perpustakaan Indikator : Persentase Pengelolaan Manajemen Perkantoran dinas Arsip dan Perpustakaan',
            'rhk'               => $request->rhk,
            'aspek'             => $request->aspek,
            'indikator_kinerja' => $request->indikator_kinerja,
            'target'            => $request->target,
            'realisasi'         => $request->realisasi,
            'umpan_balik'       => $request->umpan_balik,
        ]);

        // Redirect kembali ke halaman Detail SKP (tampilan tabel yang tadi)
        // Ganti 'skp.show' dengan nama route detail SKP Anda jika berbeda
        return redirect()->route('skp.show', $skpId)->with([
            'message' => 'Rencana Hasil Kerja berhasil ditambahkan.',
            'alert-type' => 'success'
        ]);
    }
}