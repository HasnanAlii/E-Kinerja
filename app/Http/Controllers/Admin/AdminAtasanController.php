<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Atasan;
use App\Models\User;
use App\Models\Bidang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminAtasanController extends Controller

{public function index()
{
    $bidang = Bidang::all(); 

    $atasan = Atasan::with(['user', 'bidang'])
        ->when(request('bidang_id'), function ($query) {
            $query->where('bidang_id', request('bidang_id'));
        })
        ->latest()
        ->paginate(20)
        ->withQueryString(); 

    return view('admin.atasan.index', compact('atasan', 'bidang'));
}

    public function create()
    {
        $bidang = Bidang::all();
        return view('admin.atasan.create', compact('bidang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // USER TABLE
            'nama'            => 'required',
            'email'           => 'required|email|unique:users,email',
            'password'        => 'required|min:5',
            'nik'             => 'nullable|string|unique:users,nik',
            'jenis_kelamin'   => 'nullable|string',
            'tanggal_lahir'   => 'nullable|date',
            'tempat_lahir'    => 'nullable|string',
            'agama'           => 'nullable|string',
            'alamat'          => 'nullable|string',
            'telp'            => 'nullable|string',
            'profile_photo'   => 'nullable|image|max:2048',

            // ATASAN TABLE
            'bidang_id'       => 'required|exists:bidang,id',
            'jabatan'         => 'nullable|string',
            'nip'             => 'nullable|string',
            'golongan'        => 'nullable|string',
            'status'          => 'nullable|string',
            'tanggal_masuk'   => 'nullable|date',
            'masa_kontrak'    => 'nullable|date',
        ]);

        // === Create USER ===
        $user = User::create([
            'name'          => $request->nama,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'nik'           => $request->nik,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'tempat_lahir'  => $request->tempat_lahir,
            'agama'         => $request->agama,
            'alamat'        => $request->alamat,
            'telp'          => $request->telp,
        ]);

        // Assign role
        $user->assignRole('atasan');

        // Upload Profile Photo
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photo', 'public');
            $user->update(['profile_photo' => $path]);
        }


        // === Create Atasan ===
        Atasan::create([
            'user_id'        => $user->id,
            'bidang_id'      => $request->bidang_id,
            'name'           => $request->nama,
            'nip'            => $request->nip,
            'jabatan'        => $request->jabatan,
            'status'         => $request->status,
            'golongan'       => $request->golongan,
            'tanggal_masuk'  => $request->tanggal_masuk,
            'masa_kontrak'   => $request->masa_kontrak,
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
            // USER TABLE
            'nama'            => 'required',
            'email'           => 'required|email|unique:users,email,' . $user->id,
            'password'        => 'nullable|min:5',
            'nik'             => 'nullable|string|unique:users,nik,' . $user->id,
            'jenis_kelamin'   => 'nullable|string',
            'tanggal_lahir'   => 'nullable|date',
            'tempat_lahir'    => 'nullable|string',
            'agama'           => 'nullable|string',
            'alamat'          => 'nullable|string',
            'telp'            => 'nullable|string',
            'profile_photo'   => 'nullable|image|max:2048',

            // ATASAN TABLE
            'bidang_id'       => 'required|exists:bidang,id',
            'jabatan'         => 'nullable|string',
            'nip'             => 'nullable|string',
            'golongan'        => 'nullable|string',
            'status'          => 'nullable|string',
            'tanggal_masuk'   => 'nullable|date',
            'masa_kontrak'    => 'nullable|date',
        ]);

        // === Update USER ===
        $user->update([
            'name'          => $request->nama,
            'email'         => $request->email,
            'password'      => $request->password ? Hash::make($request->password) : $user->password,
            'nik'           => $request->nik,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'tempat_lahir'  => $request->tempat_lahir,
            'agama'         => $request->agama,
            'alamat'        => $request->alamat,
            'telp'          => $request->telp,
        ]);

        // Update Profile Photo
        if ($request->hasFile('profile_photo')) {

            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $profile = $request->file('profile_photo')->store('profile-photo', 'public');
            $user->update(['profile_photo' => $profile]);
        }

 
        // === Update ATASAN ===
        $atasan->update([
            'bidang_id'      => $request->bidang_id,
            'name'           => $request->nama,
            'nip'            => $request->nip,
            'jabatan'        => $request->jabatan,
            'status'         => $request->status,
            'golongan'       => $request->golongan,
            'tanggal_masuk'  => $request->tanggal_masuk,
            'masa_kontrak'   => $request->masa_kontrak,
        ]);

        return redirect()->route('admin.atasan.index')
            ->with('success', 'Data atasan berhasil diperbarui.');
    }
    public function show($id)
    {
        // Cari data atasan berdasarkan ID, sekalian ambil data relasi User dan Bidang
        $atasan = Atasan::with(['user', 'bidang'])->findOrFail($id);

        // Return ke view yang sudah dibuat sebelumnya
        return view('admin.atasan.show', compact('atasan'));
    }
    public function destroy($id)
    {
        $atasan = Atasan::findOrFail($id);

        // Hapus Foto Atasan
        if ($atasan->foto && Storage::disk('public')->exists($atasan->foto)) {
            Storage::disk('public')->delete($atasan->foto);
        }

        // Hapus Foto Profile User
        if ($atasan->user->profile_photo && Storage::disk('public')->exists($atasan->user->profile_photo)) {
            Storage::disk('public')->delete($atasan->user->profile_photo);
        }

        // Delete User
        $atasan->user->delete();

        // Delete Atasan
        $atasan->delete();

        return back()->with('success', 'Atasan berhasil dihapus.');
    }
}
