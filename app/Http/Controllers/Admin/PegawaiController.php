<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Bidang;
use App\Models\PegawaiDetail;
use App\Models\Atasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PegawaiController extends Controller
{
    public function index()
    {
        $data = PegawaiDetail::with([
            'user',
            'bidang',
            'atasan',
            'atasan.user'
        ])
        ->latest()
        ->paginate(20);

        return view('admin.pegawai.index', compact('data'));
    }

    public function create()
    {
        $bidang = Bidang::all();
        $atasan = Atasan::with(['user', 'bidang'])->get();

        return view('admin.pegawai.create', compact('bidang', 'atasan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'         => 'required',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|min:5',
            'bidang_id'    => 'required|exists:bidang,id',
            'atasan_id'    => 'nullable|exists:atasan,id',
            'jabatan'      => 'nullable|string',
            'nip'          => 'nullable|string',
            'masa_kontrak' => 'nullable|date',
            'foto'         => 'nullable|image|mimes:jpg,jpeg,png|max:12048',
        ]);

        // Buat user akun pegawai
        $user = User::create([
            'name'     => $request->nama,
            'email'    => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $user->assignRole('pegawai');

        // Upload foto (jika ada)
        $foto = null;
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto')->store('pegawai', 'public');
        }

        // Simpan detail pegawai
        PegawaiDetail::create([
            'user_id'      => $user->id,
            'bidang_id'    => $request->bidang_id,
            'atasan_id'    => $request->atasan_id,
            'name'     => $request->nama,
            'nip'          => $request->nip,
            'jabatan'      => $request->jabatan,
            'masa_kontrak' => $request->masa_kontrak,
            'foto'         => $foto,
        ]);

        return redirect()->route('admin.pegawai.index')
            ->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data   = PegawaiDetail::with(['user', 'atasan', 'atasan.user'])->findOrFail($id);
        $bidang = Bidang::all();
        $atasan = Atasan::with(['user', 'bidang'])->get();

        return view('admin.pegawai.edit', compact('data', 'bidang', 'atasan'));
    }

    public function update(Request $request, $id)
    {
        $pegawai = PegawaiDetail::findOrFail($id);
        $user    = $pegawai->user;

        $request->validate([
            'nama'         => 'required',
            'email'        => 'required|email|unique:users,email,' . $user->id,
            'password'     => 'nullable|min:5',
            'bidang_id'    => 'required|exists:bidang,id',
            'atasan_id'    => 'nullable|exists:atasan,id',
            'jabatan'      => 'nullable|string',
            'nip'          => 'nullable|string',
            'masa_kontrak' => 'nullable|date',
            'foto'         => 'nullable|image|mimes:jpg,jpeg,png|max:12048',
        ]);

        // Update data user
        $user->update([
            'name'     => $request->nama,
            'email'    => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password
        ]);

        // Foto lama
        $foto = $pegawai->foto;

        // Jika upload foto baru, hapus foto lama
        if ($request->hasFile('foto')) {

            // Hapus foto lama jika ada
            if ($foto && Storage::disk('public')->exists($foto)) {
                Storage::disk('public')->delete($foto);
            }

            // Upload foto baru
            $foto = $request->file('foto')->store('pegawai', 'public');
        }

        // Update data pegawai
        $pegawai->update([
            'bidang_id'    => $request->bidang_id,
            'atasan_id'    => $request->atasan_id,
            'name'         => $request->nama,
            'nip'          => $request->nip,
            'jabatan'      => $request->jabatan,
            'masa_kontrak' => $request->masa_kontrak,
            'foto'         => $foto,
        ]);

        return redirect()->route('admin.pegawai.index')
            ->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $data = PegawaiDetail::findOrFail($id);

        // Hapus foto
        if ($data->foto && Storage::disk('public')->exists($data->foto)) {
            Storage::disk('public')->delete($data->foto);
        }

        // Hapus user
        $data->user->delete();

        // Hapus detail
        $data->delete();

        return back()->with('success', 'Pegawai berhasil dihapus.');
    }
}
