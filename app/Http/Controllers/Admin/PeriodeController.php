<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodePenilaian;
use Illuminate\Http\Request;

class PeriodeController extends Controller
{
    public function index()
    {
        $data = PeriodePenilaian::paginate(20);
        return view('admin.periode.index', compact('data'));
    }

    public function create()
    {
        return view('admin.periode.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_periode' => 'required',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date'
        ]);

        PeriodePenilaian::create([
            'nama_periode' => $request->nama_periode,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'status_aktif' => false
        ]);

        return redirect()->route('admin.periode.index')
            ->with('success', 'Periode berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit
     */
    public function edit($id)
    {
        $data = PeriodePenilaian::findOrFail($id);

        return view('admin.periode.edit', compact('data'));
    }

    /**
     * Update data periode
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_periode' => 'required',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date'
        ]);

        $periode = PeriodePenilaian::findOrFail($id);

        $periode->update([
            'nama_periode' => $request->nama_periode,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai
        ]);

        return redirect()->route('admin.periode.index')
            ->with('success', 'Periode berhasil diperbarui.');
    }

    public function aktifkan($id)
    {
        PeriodePenilaian::query()->update(['status_aktif' => false]);

        PeriodePenilaian::findOrFail($id)->update([
            'status_aktif' => true
        ]);

        return back()->with('success', 'Periode berhasil diaktifkan.');
    }

    public function destroy($id)
    {
        PeriodePenilaian::findOrFail($id)->delete();

        return back()->with('success', 'Periode berhasil dihapus.');
    }
}
