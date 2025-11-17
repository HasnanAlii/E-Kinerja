<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfilController extends Controller
{
    /**
     * Tampilkan halaman edit profil
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        return view('profile.update', [
            'user'     => $user,
            'pegawai'  => $user->pegawaiDetail ?? null,
            'atasan'   => $user->atasan ?? null,
        ]);
    }



    /**
     * Update profil user, pegawai, dan atasan
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // VALIDASI
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|max:255',
            'profile_photo'  => 'nullable|image|max:12048',
            
            // User biodata
            'nik'            => 'nullable|string|max:255',
            'jenis_kelamin'  => 'nullable|string|max:20',
            'tanggal_lahir'  => 'nullable|date',
            'tempat_lahir'   => 'nullable|string|max:255',
            'agama'          => 'nullable|string|max:50',
            'alamat'         => 'nullable|string|max:255',
            'telp'           => 'nullable|string|max:50',

            // Pegawai & Atasan
            'nip'            => 'nullable|string|max:255',
            'jabatan'        => 'nullable|string|max:255',
            'masa_kontrak'   => 'nullable|date',
        ]);


        // ============================
        // 1. UPDATE DATA USER
        // ============================

        $user->fill($request->only([
            'name',
            'email',
            'nik',
            'jenis_kelamin',
            'tanggal_lahir',
            'tempat_lahir',
            'agama',
            'alamat',
            'telp',
        ]));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Upload foto profile user
        $uploadedPhoto = null;

        if ($request->hasFile('profile_photo')) {

            // Hapus foto lama
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $uploadedPhoto = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo = $uploadedPhoto;
        }

        $user->save();



        // ============================
        // 2. UPDATE DATA PEGAWAI
        // ============================
        if ($user->hasRole('pegawai') && $user->pegawaiDetail) {

            $pegawai = $user->pegawaiDetail;

            // // Foto dari profile user
            // if ($uploadedPhoto) {

            //     if ($pegawai->foto && Storage::disk('public')->exists($pegawai->foto)) {
            //         Storage::disk('public')->delete($pegawai->foto);
            //     }

            //     $pegawai->foto = $uploadedPhoto;
            // }
            // // Upload foto khusus pegawai
            // elseif ($request->hasFile('foto')) {

            //     if ($pegawai->foto && Storage::disk('public')->exists($pegawai->foto)) {
            //         Storage::disk('public')->delete($pegawai->foto);
            //     }

            //     $pegawai->foto = $request->file('foto')->store('pegawai', 'public');
            // }

            $pegawai->fill($request->only([
                'nip',
                'jabatan',
                'masa_kontrak',
            ]));

            $pegawai->save();
        }



        // ============================
        // 3. UPDATE DATA ATASAN
        // ============================
        if ($user->hasRole('atasan') && $user->atasan) {

            $atasan = $user->atasan;

            // Foto dari profile user
            // if ($uploadedPhoto) {

            //     if ($atasan->foto && Storage::disk('public')->exists($atasan->foto)) {
            //         Storage::disk('public')->delete($atasan->foto);
            //     }

            //     $atasan->foto = $uploadedPhoto;
            // }
            // // Upload foto khusus atasan
            // elseif ($request->hasFile('foto')) {

            //     if ($atasan->foto && Storage::disk('public')->exists($atasan->foto)) {
            //         Storage::disk('public')->delete($atasan->foto);
            //     }

            //     $atasan->foto = $request->file('foto')->store('atasan', 'public');
            // }

            $atasan->fill($request->only([
                'nip',
                'jabatan',
                'masa_kontrak',
            ]));

            $atasan->save();
        }


        return Redirect::route('profil.edit')->with('status', 'profile-updated');
    }
}
