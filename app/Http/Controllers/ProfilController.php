<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            'pegawai'  => $user->pegawaiDetail ?? null,   // aman null
            'atasan'   => $user->atasan ?? null,          // aman null
        ]);
    }


    public function update(Request $request): RedirectResponse
{
    $user = $request->user();

    // ============================
    // 1. VALIDASI MANUAL
    // ============================
    $request->validate([
        'name'          => 'required|string|max:255',
        'email'         => 'required|email|max:255',
        'profile_photo' => 'nullable|image|max:12048',
        'nip'           => 'nullable|string|max:255',
        'jabatan'       => 'nullable|string|max:255',
        'masa_kontrak'  => 'nullable|date',
    ]);


    // ============================
    // 2. UPDATE DATA USER
    // ============================
    $user->name  = $request->name;
    $user->email = $request->email;

    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }

    $uploadedPhoto = null;

    // Upload FOTO PROFIL USER
    if ($request->hasFile('profile_photo')) {

        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $uploadedPhoto = $request->file('profile_photo')->store('profile_photos', 'public');
        $user->profile_photo = $uploadedPhoto;
    }

    $user->save();


    // ============================
    // 3. UPDATE DATA PEGAWAI
    // ============================
    if ($user->hasRole('pegawai') && $user->pegawaiDetail) {

        $pegawai = $user->pegawaiDetail;

        // Jika pakai profile_photo → pakai juga untuk pegawai
        if ($uploadedPhoto) {

            if ($pegawai->foto && Storage::disk('public')->exists($pegawai->foto)) {
                Storage::disk('public')->delete($pegawai->foto);
            }

            $pegawai->foto = $uploadedPhoto;
        }
        // Jika upload khusus foto pegawai
        elseif ($request->hasFile('foto')) {

            if ($pegawai->foto && Storage::disk('public')->exists($pegawai->foto)) {
                Storage::disk('public')->delete($pegawai->foto);
            }

            $pegawai->foto = $request->file('foto')->store('pegawai', 'public');
        }

        $pegawai->nip          = $request->nip;
        $pegawai->jabatan      = $request->jabatan;
        $pegawai->masa_kontrak = $request->masa_kontrak;
        $pegawai->save();
    }


    // ============================
    // 4. UPDATE DATA ATASAN
    // ============================
    if ($user->hasRole('atasan') && $user->atasan) {

        $atasan = $user->atasan;

        // Jika pakai profile_photo → pakai juga untuk atasan
        if ($uploadedPhoto) {

            if ($atasan->foto && Storage::disk('public')->exists($atasan->foto)) {
                Storage::disk('public')->delete($atasan->foto);
            }

            $atasan->foto = $uploadedPhoto;
        }
        // Jika upload foto atasan
        elseif ($request->hasFile('foto')) {

            if ($atasan->foto && Storage::disk('public')->exists($atasan->foto)) {
                Storage::disk('public')->delete($atasan->foto);
            }

            $atasan->foto = $request->file('foto')->store('atasan', 'public');
        }

        $atasan->nip          = $request->nip;
        $atasan->jabatan      = $request->jabatan;
        $atasan->masa_kontrak = $request->masa_kontrak;
        $atasan->save();
    }


    return Redirect::route('profil.edit')->with('status', 'profile-updated');
}



  


}
