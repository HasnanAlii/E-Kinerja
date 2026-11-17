<?php

namespace Tests\Feature;

use App\Models\Atasan;
use App\Models\Bidang;
use App\Models\IzinSakit;
use App\Models\Kehadiran;
use App\Models\PegawaiDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BlackBoxSystemTest extends TestCase
{
    protected User $karyawanUser;
    protected PegawaiDetail $karyawanDetail;
    protected User $atasanUser;
    protected Atasan $atasanDetail;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'atasan']);
        Role::firstOrCreate(['name' => 'pegawai']);

        $bidang = Bidang::firstOrCreate(['nama_bidang' => 'Produksi']);

        // Pastikan user atasan ada
        $this->atasanUser = User::where('email', 'atasan@gmail.com')->first() ?? User::create([
            'name'          => 'Siti Rahmawati',
            'email'         => 'atasan@gmail.com',
            'nik'           => 'PYI-000100',
            'jenis_kelamin' => 'Perempuan',
            'password'      => Hash::make('password'),
        ]);
        if (! $this->atasanUser->hasRole('atasan')) {
            $this->atasanUser->assignRole('atasan');
        }

        $this->atasanDetail = Atasan::firstOrCreate(
            ['user_id' => $this->atasanUser->id],
            [
                'bidang_id' => $bidang->id,
                'name'      => $this->atasanUser->name,
                'nip'       => 'PYI-000100',
                'jabatan'   => 'Supervisor Produksi',
                'status'    => 'aktif',
            ]
        );

        // Pastikan user karyawan utama ada
        $this->karyawanUser = User::where('email', 'pegawai@gmail.com')->first() ?? User::create([
            'name'          => 'Andi Saputra Pratama',
            'email'         => 'pegawai@gmail.com',
            'nik'           => 'PYI-260100',
            'jenis_kelamin' => 'Laki-laki',
            'password'      => Hash::make('password'),
        ]);
        if (! $this->karyawanUser->hasRole('pegawai')) {
            $this->karyawanUser->assignRole('pegawai');
        }

        $this->karyawanDetail = PegawaiDetail::updateOrCreate(
            ['user_id' => $this->karyawanUser->id],
            [
                'bidang_id'    => $bidang->id,
                'atasan_id'    => $this->atasanDetail->id,
                'departemen'   => 'PRODUCTION',
                'divisi'       => '010410 Assembly Line Utama',
                'jabatan'      => 'Operator Assembly',
                'nik_karyawan' => 'PYI-260100',
                'nip'          => 'PYI-260100',
                'status'       => 'Masih Bekerja',
            ]
        );
    }

    /** TC-01: Halaman publik & branding PT Pou Yuen */
    public function test_tc01_public_welcome_and_login_branding(): void
    {
        $resWelcome = $this->get('/');
        $resWelcome->assertStatus(200);
        $resWelcome->assertSee('Pou Yuen');

        $resLogin = $this->get('/login');
        $resLogin->assertStatus(200);
        $resLogin->assertSee('Monitoring Kehadiran');
    }

    /** TC-02: Karyawan — Dashboard & Cek Kehadiran (Harian dan Bulanan) */
    public function test_tc02_karyawan_dashboard_and_kehadiran_harian_bulanan(): void
    {
        Kehadiran::updateOrCreate(
            ['pegawai_id' => $this->karyawanDetail->id, 'tanggal' => Carbon::today()->toDateString()],
            ['jenis' => 'hadir', 'check_in' => '07:55:10', 'check_out' => '16:02:30']
        );

        $dash = $this->actingAs($this->karyawanUser)->get('/dashboard');
        $dash->assertStatus(200);
        $dash->assertSee('Dashboard Karyawan');
        $dash->assertDontSee('Rata-rata Kinerja');

        // Filter bulanan
        $bulanan = $this->actingAs($this->karyawanUser)
            ->get(route('pegawai.kehadiran.index', ['mode' => 'bulanan', 'bulan' => Carbon::today()->format('Y-m')]));
        $bulanan->assertStatus(200);
        $bulanan->assertSee('07:55');
        $bulanan->assertSee('Info Kehadiran');

        // Filter harian
        $harian = $this->actingAs($this->karyawanUser)
            ->get(route('pegawai.kehadiran.index', ['mode' => 'harian', 'tanggal' => Carbon::today()->toDateString()]));
        $harian->assertStatus(200);
        $harian->assertSee('16:02');
    }

    /** TC-03: Karyawan — Absen manual diblokir (403) karena diatur sistem monitoring */
    public function test_tc03_karyawan_manual_checkin_checkout_blocked(): void
    {
        $in = $this->actingAs($this->karyawanUser)->post(route('pegawai.kehadiran.checkin'));
        $in->assertStatus(403);

        $out = $this->actingAs($this->karyawanUser)->post(route('pegawai.kehadiran.checkout'));
        $out->assertStatus(403);
    }

    /** TC-04: Karyawan — Pengajuan Izin, Sakit, Cuti */
    public function test_tc04_karyawan_pengajuan_izin_sakit_cuti(): void
    {
        $page = $this->actingAs($this->karyawanUser)->get(route('pegawai.izin.create'));
        $page->assertStatus(200);
        $page->assertSee('Cuti');

        $targetDate = Carbon::tomorrow()->toDateString();
        $store = $this->actingAs($this->karyawanUser)->post(route('pegawai.izin.store'), [
            'jenis'           => 'cuti',
            'tanggal_mulai'   => $targetDate,
            'tanggal_selesai' => $targetDate,
        ]);
        $store->assertRedirect(route('pegawai.izin.index'));

        $this->assertDatabaseHas('izin_sakit', [
            'pegawai_id'    => $this->karyawanDetail->id,
            'jenis'         => 'cuti',
            'tanggal_mulai' => $targetDate,
            'status'        => 'menunggu',
        ]);
    }

    /** TC-05: Karyawan — Kelola Data Diri & Lihat Laporan PDF Pribadi */
    public function test_tc05_karyawan_kelola_data_diri_dan_cetak_pdf(): void
    {
        $profil = $this->actingAs($this->karyawanUser)->get(route('profil.edit'));
        $profil->assertStatus(200);
        $profil->assertSee('Departemen');
        $profil->assertSee('Divisi');
        $profil->assertSee('NIK Karyawan');

        $upd = $this->actingAs($this->karyawanUser)->patch(route('profil.update'), [
            'name'          => 'Andi Saputra Pratama',
            'email'         => 'pegawai@gmail.com',
            'jenis_kelamin' => 'Laki-laki',
            'nik_karyawan'  => 'PYI-260100',
            'departemen'    => 'PRODUCTION',
            'divisi'        => '010410 Assembly Line Utama',
            'jabatan'       => 'Operator Assembly',
        ]);
        $upd->assertRedirect(route('profil.edit'));

        // Laporan PDF pribadi format Data Karyawan (T1)
        $pdf = $this->actingAs($this->karyawanUser)
            ->get(route('laporan.kehadiran.perpegawai', ['pegawai_id' => $this->karyawanDetail->id]));
        $pdf->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $pdf->headers->get('content-type'));
    }

    /** TC-06: Atasan — Menu Monitoring Terpisah & Kehadiran + Efektivitas */
    public function test_tc06_atasan_monitoring_dan_kehadiran(): void
    {
        $mon = $this->actingAs($this->atasanUser)->get(route('atasan.monitoring.index'));
        $mon->assertStatus(200);
        $mon->assertSee('CCTV Monitoring Kehadiran');
        $mon->assertSee('Zona Belum Dikonfigurasi');
        $mon->assertSee('Layar Penuh');

        $keh = $this->actingAs($this->atasanUser)->get(route('atasan.kehadiran.index', ['cari' => 'Andi']));
        $keh->assertStatus(200);
        $keh->assertSee('Andi Saputra Pratama');
        $keh->assertSee('Efektivitas');
    }

    /** TC-07: Atasan — Kelola Data Karyawan (CRUD 7 atribut Pou Yuen) */
    public function test_tc07_atasan_kelola_data_karyawan_crud(): void
    {
        $list = $this->actingAs($this->atasanUser)->get(route('admin.pegawai.index', ['cari' => 'Hani']));
        $list->assertStatus(200);

        $uniqueEmail = 'test_bb_' . uniqid() . '@pouyuen.co.id';
        $uniqueNik   = 'PYI-BB-' . rand(1000, 9999);

        // Create
        $create = $this->actingAs($this->atasanUser)->post(route('admin.pegawai.store'), [
            'nama'          => 'Karyawan Blackbox Test',
            'email'         => $uniqueEmail,
            'password'      => 'password123',
            'jenis_kelamin' => 'Perempuan',
            'departemen'    => 'WAREHOUSE',
            'divisi'        => '067299 Warehouse Staff',
            'jabatan'       => 'Staff',
            'nik_karyawan'  => $uniqueNik,
            'status'        => 'Masih Bekerja',
        ]);
        $create->assertRedirect(route('admin.pegawai.index'));

        $createdPegawai = PegawaiDetail::where('nik_karyawan', $uniqueNik)->firstOrFail();

        // Show Detail
        $show = $this->actingAs($this->atasanUser)->get(route('admin.pegawai.show', $createdPegawai->id));
        $show->assertStatus(200);
        $show->assertSee('WAREHOUSE');
        $show->assertSee('067299 Warehouse Staff');
        $show->assertSee($uniqueNik);

        // Update
        $update = $this->actingAs($this->atasanUser)->put(route('admin.pegawai.update', $createdPegawai->id), [
            'nama'          => 'Karyawan Blackbox Updated',
            'email'         => $uniqueEmail,
            'jenis_kelamin' => 'Perempuan',
            'departemen'    => 'QUALITY',
            'divisi'        => '020101 Quality Control',
            'jabatan'       => 'Inspector QC',
            'nik_karyawan'  => $uniqueNik,
            'status'        => 'Masih Bekerja',
        ]);
        $update->assertRedirect(route('admin.pegawai.index'));
        $this->assertDatabaseHas('pegawai_details', [
            'id'         => $createdPegawai->id,
            'departemen' => 'QUALITY',
            'jabatan'    => 'Inspector QC',
        ]);

        // Delete
        $del = $this->actingAs($this->atasanUser)->delete(route('admin.pegawai.destroy', $createdPegawai->id));
        $del->assertStatus(302);
        $this->assertDatabaseMissing('pegawai_details', ['id' => $createdPegawai->id]);
    }

    /** TC-08: Atasan — Kelola Izin, Sakit, Cuti (Approve otomatis masuk ke Kehadiran) */
    public function test_tc08_atasan_verifikasi_izin_sakit_cuti(): void
    {
        $tgl = Carbon::today()->addDays(3)->toDateString();
        $izin = IzinSakit::create([
            'pegawai_id'      => $this->karyawanDetail->id,
            'jenis'           => 'sakit',
            'tanggal_mulai'   => $tgl,
            'tanggal_selesai' => $tgl,
            'status'          => 'menunggu',
        ]);

        $idx = $this->actingAs($this->atasanUser)->get(route('atasan.izin.index'));
        $idx->assertStatus(200);

        $detail = $this->actingAs($this->atasanUser)->get(route('atasan.izin.show', $izin->id));
        $detail->assertStatus(200);

        $approve = $this->actingAs($this->atasanUser)->post(route('atasan.izin.approve', $izin->id));
        $approve->assertRedirect(route('atasan.izin.index'));

        $this->assertDatabaseHas('izin_sakit', ['id' => $izin->id, 'status' => 'disetujui']);
        $this->assertDatabaseHas('kehadirans', [
            'pegawai_id' => $this->karyawanDetail->id,
            'tanggal'    => $tgl,
            'jenis'      => 'sakit',
        ]);

        // Cleanup test records
        Kehadiran::where('pegawai_id', $this->karyawanDetail->id)->where('tanggal', $tgl)->delete();
        $izin->delete();
    }

    /** TC-09: Atasan — Laporan Kehadiran Semua Karyawan (Filter Tanggal, Cari, PDF Formal, Excel, PDF Per Orang) */
    public function test_tc09_atasan_laporan_kehadiran_pdf_excel_dan_per_orang(): void
    {
        $dari   = Carbon::today()->startOfMonth()->toDateString();
        $sampai = Carbon::today()->toDateString();

        $lap = $this->actingAs($this->atasanUser)->get(route('atasan.laporan.index', [
            'cari'           => 'Hani',
            'tanggal_dari'   => $dari,
            'tanggal_sampai' => $sampai,
        ]));
        $lap->assertStatus(200);
        $lap->assertSee('Hani Mutiara Anisa');

        // Export PDF Semua Karyawan (Kop Formal PT Pou Yuen)
        $pdfAll = $this->actingAs($this->atasanUser)->get(route('atasan.laporan.export.pdf', [
            'tanggal_dari'   => $dari,
            'tanggal_sampai' => $sampai,
        ]));
        $pdfAll->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $pdfAll->headers->get('content-type'));

        // Export Excel/CSV Semua Karyawan
        $excel = $this->actingAs($this->atasanUser)->get(route('atasan.laporan.export.excel', [
            'tanggal_dari'   => $dari,
            'tanggal_sampai' => $sampai,
        ]));
        $excel->assertStatus(200);
        $this->assertStringContainsString('text/csv', $excel->headers->get('content-type'));

        // Export PDF Per Karyawan (Format Data Karyawan T1)
        $pdfOne = $this->actingAs($this->atasanUser)->get(route('laporan.kehadiran.perpegawai', [
            'pegawai_id'     => $this->karyawanDetail->id,
            'tanggal_dari'   => $dari,
            'tanggal_sampai' => $sampai,
        ]));
        $pdfOne->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $pdfOne->headers->get('content-type'));
    }
}

