<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pegawai\AktivitasController;
use App\Http\Controllers\Pegawai\KehadiranController;
use App\Http\Controllers\Pegawai\IzinSakitController;
use App\Http\Controllers\Pegawai\SkpController;
use App\Http\Controllers\Pegawai\SkpProgressController;
use App\Http\Controllers\Atasan\VerifikasiAktivitasController;
use App\Http\Controllers\Atasan\PenilaianController as PenilaianAtasan;
use App\Http\Controllers\Atasan\LaporanController;
use App\Http\Controllers\Admin\PegawaiController;
use App\Http\Controllers\Admin\BidangController;
use App\Http\Controllers\Admin\PenilaianController as PenilaianAdmin;
use App\Http\Controllers\Admin\PeriodeController;
use App\Http\Controllers\Admin\PenggunaController;
use App\Http\Controllers\Admin\AktivitasController as AktivitasAdmin;
use App\Http\Controllers\Atasan\AtasanIzinSakitController;
use App\Http\Controllers\Atasan\AtasanKehadiranController;
use App\Http\Controllers\Admin\AdminAtasanController;
use App\Http\Controllers\ProfilController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::get('/profil', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::patch('/profil', [ProfilController::class, 'update'])->name('profil.update');
});

Route::middleware(['auth', 'role:pegawai'])->prefix('pegawai')->name('pegawai.')->group(function () {

    // Aktivitas Harian
    Route::resource('aktivitas', AktivitasController::class);

    // Kehadiran
    Route::get('kehadiran', [KehadiranController::class, 'index'])->name('kehadiran.index');
    Route::post('kehadiran/check-in', [KehadiranController::class, 'checkIn'])->name('kehadiran.checkin');
    Route::post('kehadiran/check-out', [KehadiranController::class, 'checkOut'])->name('kehadiran.checkout');

    // Izin / Sakit
    Route::resource('izin', IzinSakitController::class)->only(['index', 'create', 'store']);

    // SKP Target
    Route::get('skp', [SkpController::class, 'index'])->name('skp.index');

    // SKP Progress / Capaian
    Route::resource('skp-progress', SkpProgressController::class)->only(['index', 'create', 'store']);

   
});

Route::middleware(['auth', 'role:atasan'])->prefix('atasan')->name('atasan.')->group(function () {

    // Verifikasi Aktivitas Pegawai
    Route::get('verifikasi', [VerifikasiAktivitasController::class, 'index'])->name('verifikasi.index');
    Route::get('verifikasi/{id}', [VerifikasiAktivitasController::class, 'show'])->name('verifikasi.show');
    Route::post('verifikasi/{id}/approve', [VerifikasiAktivitasController::class, 'approve'])->name('verifikasi.approve');
    Route::post('verifikasi/{id}/reject', [VerifikasiAktivitasController::class, 'reject'])->name('verifikasi.reject');
    Route::post('verifikasi/{id}/revisi', [VerifikasiAktivitasController::class, 'revisi'])->name('verifikasi.revisi');

    // Penilaian Kinerja Pegawai
    Route::get('penilaian', [PenilaianAtasan::class, 'index'])->name('penilaian.index');
    Route::get('penilaian/{pegawai}/create', [PenilaianAtasan::class, 'create'])->name('penilaian.create');
    Route::post('penilaian/store', [PenilaianAtasan::class, 'store'])->name('penilaian.store');



    Route::get('/kehadiran', [AtasanKehadiranController::class, 'index'])
        ->name('kehadiran.index');

    Route::get('/kehadiran/{id}', [AtasanKehadiranController::class, 'show'])
        ->name('kehadiran.show');

    Route::put('/kehadiran/{id}', [AtasanKehadiranController::class, 'update'])
        ->name('kehadiran.update');

    // Optional: tambah manual
    Route::post('/kehadiran/manual', [AtasanKehadiranController::class, 'storeManual'])
        ->name('kehadiran.storeManual');

        Route::get('/izin', [AtasanIzinSakitController::class, 'index'])
        ->name('izin.index');

    Route::get('/izin/{id}', [AtasanIzinSakitController::class, 'show'])
        ->name('izin.show');

    Route::post('/izin/{id}/approve', [AtasanIzinSakitController::class, 'approve'])
        ->name('izin.approve');

    Route::post('/izin/{id}/reject', [AtasanIzinSakitController::class, 'reject'])
        ->name('izin.reject');



    // Laporan Penilaian
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/{id}', [LaporanController::class, 'show'])->name('laporan.show');

    // Export (tempatkan nanti jika sudah ada PDF/Excel)
    Route::get('laporan/export/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');
    Route::get('laporan/export/excel', [LaporanController::class, 'exportExcel'])->name('laporan.export.excel');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Manajemen Pegawai
    Route::resource('pegawai', PegawaiController::class);

    // Manajemen Bidang
    Route::resource('bidang', BidangController::class);

    Route::resource('atasan', AdminAtasanController::class);

    // Melihat Penilaian (Admin)
    Route::resource('penilaian', PenilaianAdmin::class)->only(['index', 'show']);

    // Manajemen Periode Penilaian
    Route::resource('periode', PeriodeController::class);
    Route::post('periode/{id}/aktifkan', [PeriodeController::class, 'aktifkan'])->name('periode.aktifkan');

    // Manajemen User & Role Spatie
    Route::resource('pengguna', PenggunaController::class)->only(['index', 'edit', 'update']);

    // Monitoring Aktivitas
    Route::resource('aktivitas', AktivitasAdmin::class)->only(['index', 'show']);
});

require __DIR__.'/auth.php';




// <?php

// use App\Http\Controllers\ProfileController;
// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Pegawai\AktivitasController;
// use App\Http\Controllers\Pegawai\KehadiranController;
// use App\Http\Controllers\Pegawai\IzinSakitController;
// use App\Http\Controllers\Pegawai\SkpController;
// use App\Http\Controllers\Pegawai\ProfilController;
// use App\Http\Controllers\Pegawai\SkpProgressController;
// use App\Http\Controllers\Atasan\VerifikasiAktivitasController;
// use App\Http\Controllers\Atasan\PenilaianController as PenilaianAtasan;
// use App\Http\Controllers\Atasan\LaporanController;
// use App\Http\Controllers\Admin\PegawaiController;
// use App\Http\Controllers\Admin\BidangController;
// use App\Http\Controllers\Admin\PenilaianController as PenilaianAdmin;
// use App\Http\Controllers\Admin\PeriodeController;
// use App\Http\Controllers\Admin\PenggunaController;
// use App\Http\Controllers\Admin\AktivitasController as AktivitasAdmin;
// use Illuminate\Support\Facades\Auth;

// Route::get('/', function () {
//     return view('welcome');
// });


// Route::get('/dashboard', function () {

//     $user = Auth::user();

//     if ($user->hasRole('admin')) {
//         return redirect()->route('admin.pegawai.index');
//     }

//     if ($user->hasRole('atasan')) {
//         return redirect()->route('atasan.verifikasi.index');
//     }

//     if ($user->hasRole('pegawai')) {
//         return redirect()->route('pegawai.aktivitas.index');
//     }

//     return abort(403); // jika tidak punya role
// })->middleware(['auth', 'verified'])->name('dashboard');


// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// Route::middleware(['auth', 'role:pegawai'])->prefix('pegawai')->name('pegawai.')->group(function () {

//     // Aktivitas Harian
//     Route::resource('aktivitas', AktivitasController::class);

//     // Kehadiran
//     Route::get('kehadiran', [KehadiranController::class, 'index'])->name('kehadiran.index');
//     Route::post('kehadiran/check-in', [KehadiranController::class, 'checkIn'])->name('kehadiran.checkin');
//     Route::post('kehadiran/check-out', [KehadiranController::class, 'checkOut'])->name('kehadiran.checkout');

//     // Izin / Sakit
//     Route::resource('izin', IzinSakitController::class)->only(['index', 'create', 'store']);

//     // SKP Target
//     Route::get('skp', [SkpController::class, 'index'])->name('skp.index');

//     // SKP Progress / Capaian
//     Route::resource('skp-progress', SkpProgressController::class)->only(['index', 'create', 'store']);

//     Route::get('profil', [ProfilController::class, 'index'])->name('profil.index');
//     Route::post('profil/update', [ProfilController::class, 'update'])->name('profil.update');

   
// });

// Route::middleware(['auth', 'role:atasan'])->prefix('atasan')->name('atasan.')->group(function () {

//     // Verifikasi Aktivitas Pegawai
//     Route::get('verifikasi', [VerifikasiAktivitasController::class, 'index'])->name('verifikasi.index');
//     Route::get('verifikasi/{id}', [VerifikasiAktivitasController::class, 'show'])->name('verifikasi.show');
//     Route::post('verifikasi/{id}/approve', [VerifikasiAktivitasController::class, 'approve'])->name('verifikasi.approve');
//     Route::post('verifikasi/{id}/reject', [VerifikasiAktivitasController::class, 'reject'])->name('verifikasi.reject');
//     Route::post('verifikasi/{id}/revisi', [VerifikasiAktivitasController::class, 'revisi'])->name('verifikasi.revisi');

//     // Penilaian Kinerja Pegawai
//     Route::get('penilaian', [PenilaianAtasan::class, 'index'])->name('penilaian.index');
//     Route::get('penilaian/create/{id}', [PenilaianAtasan::class, 'create'])->name('penilaian.create');
//     Route::post('penilaian/store', [PenilaianAtasan::class, 'store'])->name('penilaian.store');

//     // Laporan Penilaian
//     Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
//     Route::get('laporan/{id}', [LaporanController::class, 'show'])->name('laporan.show');

//     // Export (tempatkan nanti jika sudah ada PDF/Excel)
//     Route::get('laporan/export/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');
//     Route::get('laporan/export/excel', [LaporanController::class, 'exportExcel'])->name('laporan.export.excel');
// });

// Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

//     // Manajemen Pegawai
//     Route::resource('pegawai', PegawaiController::class);

//     // Manajemen Bidang
//     Route::resource('bidang', BidangController::class);

//     // Melihat Penilaian (Admin)
//     Route::resource('penilaian', PenilaianAdmin::class)->only(['index', 'show']);

//     // Manajemen Periode Penilaian
//     Route::resource('periode', PeriodeController::class);
//     Route::post('periode/{id}/aktifkan', [PeriodeController::class, 'aktifkan'])->name('periode.aktifkan');

//     // Manajemen User & Role Spatie
//     Route::resource('pengguna', PenggunaController::class)->only(['index', 'edit', 'update']);

//     // Monitoring Aktivitas
//     Route::resource('aktivitas', AktivitasAdmin::class)->only(['index', 'show']);
// });

// require __DIR__.'/auth.php';
