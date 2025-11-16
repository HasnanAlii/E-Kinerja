<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Atasan;
use App\Models\User;
use App\Models\Bidang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class AdminAtasanController extends Controller
{
    public function index()
    {
        $atasan = Atasan::with(['user', 'bidang'])
            ->latest()
            ->paginate(20);

        return view('admin.atasan.index', compact('atasan'));
    }

    public function create()
    {
        $bidang = Bidang::all();

        return view('admin.atasan.create', compact('bidang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'        => 'required',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|min:5',
            'bidang_id'   => 'required|exists:bidang,id',
            'jabatan'     => 'nullable|string',
            'nip'         => 'nullable|string',
            'masa_kontrak'=> 'nullable|date',
            'foto'        => 'nullable|image|max:2048',
        ]);

        // 1. Buat user baru
        $user = User::create([
            'name'     => $request->nama,
            'email'    => $request->email,
            'password' => Hash::make($request->password)
        ]);

        // Assign role
        $user->assignRole('atasan');

        // 2. Upload foto
        $foto = null;
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto')->store('foto-atasan', 'public');
        }

        // 3. Buat data atasan
        Atasan::create([
            'user_id'      => $user->id,
            'bidang_id'    => $request->bidang_id,
            'name'     => $request->nama,
            'jabatan'      => $request->jabatan,
            'nip'          => $request->nip,
            'masa_kontrak' => $request->masa_kontrak,
            'foto'         => $foto,
        ]);

        return redirect()->route('admin.atasan.index')
            ->with('success', 'Atasan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data   = Atasan::with('user')->findOrFail($id);
        $bidang = Bidang::all();

        return view('admin.atasan.edit', compact('data', 'bidang'));
    }

    public function update(Request $request, $id)
    {
        $atasan = Atasan::findOrFail($id);
        $user   = $atasan->user;

        $request->validate([
            'nama'         => 'required',
            'email'        => 'required|email|unique:users,email,' . $user->id,
            'password'     => 'nullable|min:5',
            'bidang_id'    => 'required|exists:bidang,id',
            'jabatan'      => 'nullable',
            'nip'          => 'nullable',
            'masa_kontrak' => 'nullable|date',
            'foto'         => 'nullable|image|max:2048',
        ]);

        // Update data user
        $user -> update([
            'name' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        // Update foto jika ada
        if ($request->hasFile('foto')) {
            if ($atasan->foto && Storage::disk('public')->exists($atasan->foto)) {
                Storage::disk('public')->delete($atasan->foto);
            }
            $atasan->foto = $request->file('foto')->store('foto-atasan', 'public');
        }

        // Update detail atasan
        $atasan->update([
            'bidang_id'    => $request->bidang_id,
            'jabatan'      => $request->jabatan,
            'nip'          => $request->nip,
            'masa_kontrak' => $request->masa_kontrak,
            'foto'         => $atasan->foto,
        ]);

        return redirect()->route('admin.atasan.index')
        ->with('success', 'Data atasan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $atasan = Atasan::findOrFail($id);
        
        // Hapus foto
        if ($atasan->foto && Storage::disk('public')->exists($atasan->foto)) {
            Storage::disk('public')->delete($atasan->foto);
        }

        // Hapus user
        $atasan->user->delete();

        // Hapus data atasan
        $atasan->delete();

        return back()->with('success', 'Atasan berhasil dihapus.');
    }
}
