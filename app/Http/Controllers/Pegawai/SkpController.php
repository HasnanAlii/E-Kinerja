<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Skp;
use App\Models\SkpHasilKerja;
use App\Models\SkpPerilaku;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkpController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('pegawai')) {
            $skp = Skp::where('pegawai_id', $user->detail->id)
                      ->latest()
                      ->paginate(10);
        }

        elseif ($user->hasRole('atasan')) {
            $skp = Skp::where('atasan_id', $user->pegawaiDetail->id)
                      ->latest()
                      ->paginate(10);
        }

        else {
            $skp = Skp::latest()->paginate(10);
        }

        return view('pegawai.skp.index', compact('skp'));
    }


    public function create()
    {
        return view('pegawai.skp.create');
    }


    public function cetak($id)
    {
        $data = Skp::with([
            'pegawai.user',
            'pegawai.bidang',
            'pegawai.atasan.user',
            'hasilKerja'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pegawai.skp.cetak', compact('data'))
                ->setPaper('a4', 'portrait');

        return $pdf->stream("SKP-{$data->id}.pdf");
    }

    public function store(Request $request)
    {
        $request->validate([
            'periode' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
        ]);

        $pegawai = Auth::user()->detail;

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


        return redirect()->route('skp.index')
            ->with('success', 'SKP berhasil dibuat.');
    }


    public function show($id)
    {
        $data = Skp::with(['hasilKerja', 'pegawai.user', 'bidang'])
                   ->findOrFail($id);

        return view('pegawai.skp.show', compact('data'));
    }


    public function edit($id)
        {
            $skp = Skp::with(['pegawai.user', 'hasilKerja'])->findOrFail($id);

            
            return view('pegawai.skp.edit', compact('skp'));
        }


        public function update(Request $request, $id)
        {
            $skp = Skp::findOrFail($id);

            $skp->update([
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'capaian_kinerja_organisasi' => $request->capaian_kinerja_organisasi,
                'pola_distribusi' => $request->pola_distribusi,
            ]);

            if ($request->hasil_kerja) {
                foreach ($request->hasil_kerja as $idRow => $item) {
                    SkpHasilKerja::where('id', $idRow)->update([
                        'rhk'                => $item['rhk'],
                        'aspek'              => $item['aspek'],
                        'indikator_kinerja'  => $item['indikator_kinerja'],
                        'target'             => $item['target'],
                        'realisasi'          => $item['realisasi'],
                    ]);
                }
            }

            return redirect()->route('skp.show', $id)
                ->with('success', 'SKP berhasil diperbarui.');
        }


    public function destroy($id)
    {
        Skp::findOrFail($id)->delete();
        return redirect()->route('skp.index')
                         ->with('success', 'Data SKP berhasil dihapus.');
    }


    public function ajukan($id)
    {
        $skp = Skp::findOrFail($id);

        $skp->update(['status' => 'Diajukan']);

        return back()->with('success', 'SKP berhasil diajukan.');
    }

  
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
