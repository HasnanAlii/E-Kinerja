<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\PegawaiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{
    public function index()
    {
        $pegawai = Auth::user()->detail;
        return view('pegawai.profil.index', compact('pegawai'));
    }

    public function update(Request $request)
    {
        $pegawai = Auth::user()->detail;

        $request->validate([
            'nip' => 'nullable|string',
            'jabatan' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpg,png'
        ]);

        $foto = $pegawai->foto;
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto')->store('foto_pegawai', 'public');
        }

        $pegawai->update([
            'nip' => $request->nip,
            'jabatan' => $request->jabatan,
            'foto' => $foto
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
