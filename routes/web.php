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
use App\Http\Controllers\Atasan\SkpController as AtasanSkpController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfilController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
    
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
    Route::get('/pegawai/penilaian/download-all', [PenilaianAtasan::class, 'download'])->name('penilaian.download');

   
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

    Route::get('/pegawai', [AtasanSkpController::class, 'index'])->name('pegawai.index');
    Route::get('/pegawai/{id}', [AtasanSkpController::class, 'showPegawai'])->name('pegawai.show');

    Route::get('/skp/{id}', [AtasanSkpController::class, 'showSkp'])->name('skp.show');
    Route::post('/skp/{id}/status', [AtasanSkpController::class, 'updateStatus'])->name('skp.updateStatus');
    Route::post('/skp/{id}/nilai', [AtasanSkpController::class, 'nilai'])->name('skp.nilai');

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
    // Tambah SKP
    Route::get('/atasan/skp/create', [AtasanSKPController::class, 'create'])
        ->name('skp.create');

    // Unduh laporan penilaian semua pegawai aktif
    Route::get('/atasan/penilaian/download-all', [PenilaianAtasan::class, 'downloadAll'])
        ->name('penilaian.download_all');



    // Laporan Penilaian
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/{id}', [LaporanController::class, 'show'])->name('laporan.show');

    Route::get('laporan/export/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');
    Route::get('laporan/export/excel', [LaporanController::class, 'exportExcel'])->name('laporan.export.excel');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::resource('pegawai', PegawaiController::class);

    Route::resource('bidang', BidangController::class);

    Route::resource('atasan', AdminAtasanController::class);

    Route::get('/kehadiran', [AtasanKehadiranController::class, 'indexx'])->name('kehadiran.index');

    Route::resource('penilaian', PenilaianAdmin::class)->only(['index', 'show']);

    Route::get('/admin/penilaian/validasi', [PenilaianAdmin::class, 'validasi'])->name('penilaian.validasi');   


    Route::resource('periode', PeriodeController::class);
    Route::post('periode/{id}/aktifkan', [PeriodeController::class, 'aktifkan'])->name('periode.aktifkan');
    Route::resource('pengguna', PenggunaController::class)->only(['index', 'edit', 'update']);

    // Monitoring Aktivitas
    Route::resource('aktivitas', AktivitasAdmin::class)->only(['index', 'show']);
});

require __DIR__.'/auth.php';


