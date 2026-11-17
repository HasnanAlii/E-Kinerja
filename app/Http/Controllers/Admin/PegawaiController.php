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

        if ($request->filled('cari')) {
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', '%' . $request->cari . '%'));
        }

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
public function show(Request $request, $id)
{
    $periode = \App\Models\PeriodePenilaian::where('status_aktif', true)->first();

    $data = PegawaiDetail::with([
        'user',
        'bidang',
        'atasan',
        'atasan.user',
        'kehadiran' => function ($q) use ($request) {
            if ($request->filled('tanggal_dari')) {
                $q->whereDate('tanggal', '>=', $request->tanggal_dari);
            }
            if ($request->filled('tanggal_sampai')) {
                $q->whereDate('tanggal', '<=', $request->tanggal_sampai);
            }
            $q->orderByDesc('tanggal')->orderByDesc('id');
        },
        'izinSakit' => function ($q) {
            $q->orderByDesc('tanggal_mulai');
        }
    ])->findOrFail($id);

    $cctvLogs = \Illuminate\Support\Facades\DB::table('cctv_monitoring_logs')
        ->where('pegawai_id', $data->id)
        ->get()
        ->keyBy(fn ($l) => \Carbon\Carbon::parse($l->tanggal)->toDateString());

    return view('admin.pegawai.show', compact('data', 'periode', 'cctvLogs'));
}



    public function store(Request $request)
    {
        $request->validate([
            'nama'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'password'        => 'required|min:5',
            'jenis_kelamin'   => 'required|string',
            'departemen'      => 'required|string|max:255',
            'divisi'          => 'required|string|max:255',
            'jabatan'         => 'required|string|max:255',
            'nik_karyawan'    => 'required|string|max:255',
            'status'          => 'required|string|max:255',
            'profile_photo'   => 'nullable|image|max:2048',
        ]);

        // === Create USER ===
        $user = User::create([
            'name'          => $request->nama,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'nik'           => $request->nik_karyawan,
            'jenis_kelamin' => $request->jenis_kelamin,
        ]);

        $user->assignRole('pegawai');

        // Upload Profile Photo
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photo', 'public');
            $user->update(['profile_photo' => $path]);
        }

        $defaultBidangId = $request->bidang_id ?: Bidang::value('id');
        $defaultAtasanId = $request->atasan_id ?: Atasan::value('id');

        // === Create Pegawai Detail ===
        PegawaiDetail::create([
            'user_id'      => $user->id,
            'bidang_id'    => $defaultBidangId,
            'atasan_id'    => $defaultAtasanId,
            'departemen'   => $request->departemen,
            'divisi'       => $request->divisi,
            'jabatan'      => $request->jabatan,
            'nik_karyawan' => $request->nik_karyawan,
            'nip'          => $request->nik_karyawan,
            'status'       => $request->status,
        ]);

        return redirect()->route('admin.pegawai.index')
            ->with('success', 'Data karyawan berhasil ditambahkan.');
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
            'nama'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email,' . $user->id,
            'password'        => 'nullable|min:5',
            'jenis_kelamin'   => 'required|string',
            'departemen'      => 'required|string|max:255',
            'divisi'          => 'required|string|max:255',
            'jabatan'         => 'required|string|max:255',
            'nik_karyawan'    => 'required|string|max:255',
            'status'          => 'required|string|max:255',
            'profile_photo'   => 'nullable|image|max:2048',
        ]);

        // === Update USER ===
        $user->update([
            'name'          => $request->nama,
            'email'         => $request->email,
            'password'      => $request->password ? Hash::make($request->password) : $user->password,
            'nik'           => $request->nik_karyawan,
            'jenis_kelamin' => $request->jenis_kelamin,
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
            'departemen'   => $request->departemen,
            'divisi'       => $request->divisi,
            'jabatan'      => $request->jabatan,
            'nik_karyawan' => $request->nik_karyawan,
            'nip'          => $request->nik_karyawan,
            'status'       => $request->status,
        ]);

        return redirect()->route('admin.pegawai.index')
            ->with('success', 'Data karyawan berhasil diperbarui.');
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

