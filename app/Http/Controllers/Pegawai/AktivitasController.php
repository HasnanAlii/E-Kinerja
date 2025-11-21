<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Aktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // 1. Tambahkan ini untuk mengelola file

class AktivitasController extends Controller
{

    private function getPegawaiId()
    {
        $pegawaiId = Auth::user()->detail?->id; 
        
        if (!$pegawaiId) {

            abort(403, 'Profil pegawai tidak ditemukan.');
        }
        return $pegawaiId;
    }


    private function findAndCheckOwnership($id)
    {
        $data = Aktivitas::findOrFail($id);

        if ($data->pegawai_id !== $this->getPegawaiId()) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses aktivitas ini.');
        }

        return $data;
    }


    public function index()
    {
        $pegawaiId = $this->getPegawaiId(); 

        $data = Aktivitas::where('pegawai_id', $pegawaiId)
            ->latest()
            ->paginate(10);

        return view('pegawai.aktivitas.index', compact('data'));
    }

    public function create()
    {
        return view('pegawai.aktivitas.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tanggal' => 'required|date',
            'uraian_tugas' => 'required|string',
            'waktu_pelaksanaan' => 'required|string|max:100',
            'hasil_pekerjaan' => 'nullable|string',
            'bukti_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:12048'
        ]);

        $file = null;
        if ($request->hasFile('bukti_file')) {
            $file = $request->file('bukti_file')->store('bukti_aktivitas', 'public');
        }

        $pegawai = Auth::user()->detail;

        // 1. Simpan aktivitas
        $aktivitas = Aktivitas::create([
            'pegawai_id' => $pegawai->id,
            'tanggal' => $validatedData['tanggal'],
            'uraian_tugas' => $validatedData['uraian_tugas'],
            'waktu_pelaksanaan' => $validatedData['waktu_pelaksanaan'],
            'hasil_pekerjaan' => $validatedData['hasil_pekerjaan'] ?? null,
            'bukti_file' => $file,
            'status' => 'menunggu'
        ]);


        // ======================================
        //          NOTIFIKASI KE ATASAN
        // ======================================
        $atasanModel = $pegawai->atasan; // ambil dari tabel atasans

        if ($atasanModel && $atasanModel->user) {
            \App\Models\Notification::create([
                'user_id'   => $atasanModel->user->id,
                'aktivitas' => "{$pegawai->user->name} menyetorkan aktivitas harian tanggal {$validatedData['tanggal']}.",
                'waktu'     => now(),
            ]);
        }

        return redirect()->route('pegawai.aktivitas.index')
                        ->with('success', 'Aktivitas berhasil disimpan.');
    }

    public function edit($id)
    {
        $data = $this->findAndCheckOwnership($id);

        if ($data->status !== 'menunggu') {
            return back()->with('error', 'Aktivitas tidak dapat diedit setelah diverifikasi.');
        }

        return view('pegawai.aktivitas.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $data = $this->findAndCheckOwnership($id);

        if ($data->status !== 'menunggu') {
            return back()->with('error', 'Aktivitas sudah diverifikasi.');
        }

        $validatedData = $request->validate([
            'tanggal' => 'required|date',
            'uraian_tugas' => 'required|string',
            'waktu_pelaksanaan' => 'required|string|max:100',
            'hasil_pekerjaan' => 'nullable|string',
            'bukti_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $file = $data->bukti_file;
        if ($request->hasFile('bukti_file')) {
            if ($data->bukti_file) {
                Storage::disk('public')->delete($data->bukti_file);
            }
            $file = $request->file('bukti_file')->store('bukti_aktivitas', 'public');
        }

        $data->update([
            'tanggal' => $validatedData['tanggal'],
            'uraian_tugas' => $validatedData['uraian_tugas'],
            'waktu_pelaksanaan' => $validatedData['waktu_pelaksanaan'],
            'hasil_pekerjaan' => $validatedData['hasil_pekerjaan'] ?? null,
            'bukti_file' => $file
        ]);

        return redirect()->route('pegawai.aktivitas.index')
                         ->with('success', 'Aktivitas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $data = $this->findAndCheckOwnership($id);

        if ($data->status !== 'menunggu') {
            return back()->with('error', 'Tidak bisa menghapus aktivitas yang sudah diverifikasi.');
        }

        if ($data->bukti_file) {
            Storage::disk('public')->delete($data->bukti_file);
        }

        $data->delete();

        return back()->with('success', 'Aktivitas berhasil dihapus.');
    }
}