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
    public function index(Request $request)
    {
        $query = PegawaiDetail::with(['user', 'bidang', 'atasan', 'atasan.user'])->latest();

        if ($request->filled('bidang_id')) {
            $query->where('bidang_id', $request->bidang_id);
        }

        $data = $query->paginate(20)->withQueryString();

        $bidang = Bidang::orderBy('nama_bidang')->get();

        return view('admin.pegawai.index', compact('data', 'bidang'));
    }


    public function create()
    {
        $bidang = Bidang::all();
        $atasan = Atasan::with(['user', 'bidang'])->get();

        return view('admin.pegawai.create', compact('bidang', 'atasan'));
    }
public function show($id)
{
    // Ambil periode yang sedang aktif (status_aktif = true)
    $periode = \App\Models\PeriodePenilaian::where('status_aktif', true)->first();

    $data = PegawaiDetail::with([
        'user',
        'bidang',
        'atasan',
        'atasan.user',
        'kehadiran' => function ($q) use ($periode) {
            if ($periode) {
                $q->whereBetween('tanggal', [
                    $periode->tgl_mulai,
                    $periode->tgl_selesai
                ]);
            }
        },
        'izinSakit' => function ($q) use ($periode) {
            if ($periode) {
                $q->whereBetween('tanggal_mulai', [
                    $periode->tgl_mulai,
                    $periode->tgl_selesai
                ]);
            }
        }
    ])->findOrFail($id);

    return view('admin.pegawai.show', compact('data', 'periode'));
}



    public function store(Request $request)
    {
        $request->validate([
           
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

            // PEGAWAI DETAIL
            'bidang_id'       => 'required|exists:bidang,id',
            'atasan_id'       => 'nullable|exists:atasan,id',
            'jabatan'         => 'nullable|string',
            'nip'             => 'nullable|string',
            'status'          => 'nullable|string',
            'tanggal_masuk'   => 'nullable|date',
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

        $user->assignRole('pegawai');

        // Upload Profile Photo
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photo', 'public');
            $user->update(['profile_photo' => $path]);
        }

      

        // === Create Pegawai Detail ===
        PegawaiDetail::create([
            'user_id'      => $user->id,
            'bidang_id'    => $request->bidang_id,
            'atasan_id'    => $request->atasan_id,
            'name'         => $request->nama,
            'nip'          => $request->nip,
            'jabatan'      => $request->jabatan,
            // 'masa_kontrak' => $request->masa_kontrak,
            'status'       => $request->status,
            'tanggal_masuk'=> $request->tanggal_masuk,
        ]);

        return redirect()->route('admin.pegawai.index')
            ->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data   = PegawaiDetail::with(['user', 'atasan'])->findOrFail($id);
        $bidang = Bidang::all();
        $atasan = Atasan::with(['user', 'bidang'])->get();

        return view('admin.pegawai.edit', compact('data', 'bidang', 'atasan'));
    }

    public function update(Request $request, $id)
    {
        $pegawai = PegawaiDetail::findOrFail($id);
        $user    = $pegawai->user;

        $request->validate([
            // USER
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

            // PEGAWAI DETAIL
            'bidang_id'       => 'required|exists:bidang,id',
            'atasan_id'       => 'nullable|exists:atasan,id',
            'jabatan'         => 'nullable|string',
            'nip'             => 'nullable|string',
            'status'          => 'nullable|string',
            'tanggal_masuk'   => 'nullable|date',
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

 

        // === Update Pegawai Detail ===
        $pegawai->update([
            'bidang_id'    => $request->bidang_id,
            'atasan_id'    => $request->atasan_id,
            'name'         => $request->nama,
            'nip'          => $request->nip,
            'jabatan'      => $request->jabatan,
            'status'       => $request->status,
            'tanggal_masuk'=> $request->tanggal_masuk,
        ]);

        return redirect()->route('admin.pegawai.index')
            ->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $data = PegawaiDetail::findOrFail($id);

        if ($data->foto && Storage::disk('public')->exists($data->foto)) {
            Storage::disk('public')->delete($data->foto);
        }

        if ($data->user->profile_photo && Storage::disk('public')->exists($data->user->profile_photo)) {
            Storage::disk('public')->delete($data->user->profile_photo);
        }

        $data->user->delete();
        $data->delete();

        return back()->with('success', 'Pegawai berhasil dihapus.');
    }
}

