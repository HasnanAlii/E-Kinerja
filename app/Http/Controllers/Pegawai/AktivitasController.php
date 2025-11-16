<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Aktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // 1. Tambahkan ini untuk mengelola file

class AktivitasController extends Controller
{
    /**
     * Mendapatkan ID pegawai yang sedang login.
     * Ini untuk menghindari pengulangan kode dan error jika 'detail' tidak ada.
     */
    private function getPegawaiId()
    {
        // Gunakan nullsafe operator (?) untuk keamanan
        $pegawaiId = Auth::user()->detail?->id; 
        
        if (!$pegawaiId) {
            // Jika user pegawai tidak punya relasi 'detail', lempar error
            // Ini mencegah error lebih lanjut di kode
            abort(403, 'Profil pegawai tidak ditemukan.');
        }
        return $pegawaiId;
    }

    /**
     * Mencari aktivitas dan memastikan kepemilikannya.
     * Ini PERBAIKAN KEAMANAN PENTING.
     */
    private function findAndCheckOwnership($id)
    {
        $data = Aktivitas::findOrFail($id);

        if ($data->pegawai_id !== $this->getPegawaiId()) {
            // Jika aktivitas bukan milik user, larang akses
            abort(403, 'Anda tidak memiliki izin untuk mengakses aktivitas ini.');
        }

        return $data;
    }


    public function index()
    {
        $pegawaiId = $this->getPegawaiId(); // Gunakan helper

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
        // 2. Perbaiki validasi file (tambahkan mimes dan max size)
        $validatedData = $request->validate([
            'tanggal' => 'required|date',
            'uraian_tugas' => 'required|string',
            'waktu_pelaksanaan' => 'required|string|max:100',
            'hasil_pekerjaan' => 'nullable|string',
            'bukti_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048' // maks 2MB
        ]);

        $file = null;
        if ($request->hasFile('bukti_file')) {
            $file = $request->file('bukti_file')->store('bukti_aktivitas', 'public');
        }

        // 3. Gunakan data yang sudah divalidasi
        Aktivitas::create([
            'pegawai_id' => $this->getPegawaiId(), // Gunakan helper
            'tanggal' => $validatedData['tanggal'],
            'uraian_tugas' => $validatedData['uraian_tugas'],
            'waktu_pelaksanaan' => $validatedData['waktu_pelaksanaan'],
            'hasil_pekerjaan' => $validatedData['hasil_pekerjaan'] ?? null,
            'bukti_file' => $file,
            'status' => 'menunggu'
        ]);

        // 4. Arahkan ke index (UX lebih baik)
        return redirect()->route('pegawai.aktivitas.index')
                         ->with('success', 'Aktivitas berhasil disimpan.');
    }

    public function edit($id)
    {
        // 5. Gunakan helper untuk keamanan
        $data = $this->findAndCheckOwnership($id);

        if ($data->status !== 'menunggu') {
            return back()->with('error', 'Aktivitas tidak dapat diedit setelah diverifikasi.');
        }

        return view('pegawai.aktivitas.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        // 6. Gunakan helper untuk keamanan
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
            // 7. Hapus file lama jika ada file baru di-upload
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

        // 8. Arahkan ke index (UX lebih baik)
        return redirect()->route('pegawai.aktivitas.index')
                         ->with('success', 'Aktivitas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        // 9. Gunakan helper untuk keamanan
        $data = $this->findAndCheckOwnership($id);

        if ($data->status !== 'menunggu') {
            return back()->with('error', 'Tidak bisa menghapus aktivitas yang sudah diverifikasi.');
        }

        // 10. Hapus file terkait sebelum menghapus data
        if ($data->bukti_file) {
            Storage::disk('public')->delete($data->bukti_file);
        }

        $data->delete();

        return back()->with('success', 'Aktivitas berhasil dihapus.');
    }
}