<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\PegawaiDetail;
use App\Models\Bidang;
use App\Models\Atasan;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class KaryawanPouYuenSeeder extends Seeder
{
    /**
     * Seed 30 data karyawan PT Pou Yuen Indonesia
     * dengan atribut: Nama, Jenis Kelamin, Departemen, Divisi, Jabatan, NIK Karyawan, Status (Masih Bekerja).
     */
    public function run(): void
    {
        $pegawaiRole = Role::firstOrCreate(['name' => 'pegawai']);
        $bidang      = Bidang::firstOrCreate(['nama_bidang' => 'Produksi']);
        $atasanId    = Atasan::value('id') ?? 1;

        $karyawans = [
            ['nama' => 'Hani Mutiara Anisa',     'jk' => 'Perempuan', 'dept' => 'WAREHOUSE',   'divisi' => '067299 Warehouse Staff',     'jabatan' => 'Staff',              'nik' => 'PYI-260101'],
            ['nama' => 'Rizky Pratama Putra',    'jk' => 'Laki-laki', 'dept' => 'PRODUCTION',  'divisi' => '010201 Cutting Line A',      'jabatan' => 'Operator Cutting',   'nik' => 'PYI-260102'],
            ['nama' => 'Siti Nurhaliza Putri',   'jk' => 'Perempuan', 'dept' => 'PRODUCTION',  'divisi' => '010305 Sewing Line B',       'jabatan' => 'Operator Sewing',    'nik' => 'PYI-260103'],
            ['nama' => 'Ahmad Fauzi Hidayat',    'jk' => 'Laki-laki', 'dept' => 'PRODUCTION',  'divisi' => '010412 Assembly Line 1',     'jabatan' => 'Operator Assembly',  'nik' => 'PYI-260104'],
            ['nama' => 'Dewi Sartika Lestari',   'jk' => 'Perempuan', 'dept' => 'QUALITY',     'divisi' => '020101 Quality Control',     'jabatan' => 'Inspector QC',       'nik' => 'PYI-260105'],
            ['nama' => 'Budi Santoso Wijaya',    'jk' => 'Laki-laki', 'dept' => 'ENGINEERING', 'divisi' => '030102 Maintenance Mesin',   'jabatan' => 'Teknisi Mekanik',    'nik' => 'PYI-260106'],
            ['nama' => 'Nadia Ayu Permatasari',  'jk' => 'Perempuan', 'dept' => 'HRD & GA',    'divisi' => '040101 Payroll & Absensi',   'jabatan' => 'Staff HRD',          'nik' => 'PYI-260107'],
            ['nama' => 'Dedi Kurniawan Saputra', 'jk' => 'Laki-laki', 'dept' => 'WAREHOUSE',   'divisi' => '067301 Material Receiving',  'jabatan' => 'Admin Gudang',       'nik' => 'PYI-260108'],
            ['nama' => 'Rina Amelia Kartika',    'jk' => 'Perempuan', 'dept' => 'PRODUCTION',  'divisi' => '010306 Sewing Line C',       'jabatan' => 'Operator Sewing',    'nik' => 'PYI-260109'],
            ['nama' => 'Eko Prasetyo Nugroho',   'jk' => 'Laki-laki', 'dept' => 'PRODUCTION',  'divisi' => '010501 Stockfit',            'jabatan' => 'Operator Stockfit',  'nik' => 'PYI-260110'],
            ['nama' => 'Fitriani Rahmawati',     'jk' => 'Perempuan', 'dept' => 'QUALITY',     'divisi' => '020102 Quality Assurance',   'jabatan' => 'Staff QA',           'nik' => 'PYI-260111'],
            ['nama' => 'Hendra Gunawan Setiawan','jk' => 'Laki-laki', 'dept' => 'LOGISTIC',    'divisi' => '050101 Export Shipping',     'jabatan' => 'Staff Ekspor',       'nik' => 'PYI-260112'],
            ['nama' => 'Intan Permata Sari',     'jk' => 'Perempuan', 'dept' => 'FINANCE',     'divisi' => '070101 Accounting & Costing','jabatan' => 'Staff Accounting',   'nik' => 'PYI-260113'],
            ['nama' => 'Joko Susilo Wibowo',     'jk' => 'Laki-laki', 'dept' => 'PRODUCTION',  'divisi' => '010415 Assembly Line 2',     'jabatan' => 'Leader Line',        'nik' => 'PYI-260114'],
            ['nama' => 'Kartika Sari Dewi',      'jk' => 'Perempuan', 'dept' => 'PRODUCTION',  'divisi' => '010308 Sewing Line D',       'jabatan' => 'Operator Sewing',    'nik' => 'PYI-260115'],
            ['nama' => 'Lukman Hakim Ramadhan',  'jk' => 'Laki-laki', 'dept' => 'IT & SYSTEM', 'divisi' => '080101 Infrastructur & CCTV','jabatan' => 'IT Support',         'nik' => 'PYI-260116'],
            ['nama' => 'Mega Puspita Anggraini', 'jk' => 'Perempuan', 'dept' => 'PPIC',        'divisi' => '090101 Production Planning', 'jabatan' => 'Staff PPIC',         'nik' => 'PYI-260117'],
            ['nama' => 'Nanda Pratama Kusuma',   'jk' => 'Laki-laki', 'dept' => 'WAREHOUSE',   'divisi' => '067305 Finished Goods',      'jabatan' => 'Operator Forklift',  'nik' => 'PYI-260118'],
            ['nama' => 'Olivia Putri Maharani',  'jk' => 'Perempuan', 'dept' => 'DEVELOPMENT', 'divisi' => '100101 Sample Room',         'jabatan' => 'Sample Specialist',  'nik' => 'PYI-260119'],
            ['nama' => 'Putra Aditya Mahendra',  'jk' => 'Laki-laki', 'dept' => 'PRODUCTION',  'divisi' => '010202 Cutting Line B',      'jabatan' => 'Operator Cutting',   'nik' => 'PYI-260120'],
            ['nama' => 'Qori Aina Zahra',        'jk' => 'Perempuan', 'dept' => 'HSE / K3',    'divisi' => '110101 Safety & Compliance', 'jabatan' => 'Admin HSE',          'nik' => 'PYI-260121'],
            ['nama' => 'Rama Wijaya Kusuma',     'jk' => 'Laki-laki', 'dept' => 'PRODUCTION',  'divisi' => '010601 Rubber & Outsole',    'jabatan' => 'Operator Pressing',  'nik' => 'PYI-260122'],
            ['nama' => 'Shinta Bella Oktaviani', 'jk' => 'Perempuan', 'dept' => 'QUALITY',     'divisi' => '020103 Final Inspection',    'jabatan' => 'Inspector QC',       'nik' => 'PYI-260123'],
            ['nama' => 'Taufik Hidayatullah',    'jk' => 'Laki-laki', 'dept' => 'ENGINEERING', 'divisi' => '030105 Utility & Listrik',   'jabatan' => 'Teknisi Elektrik',   'nik' => 'PYI-260124'],
            ['nama' => 'Ulfah Khairunnisa',      'jk' => 'Perempuan', 'dept' => 'PURCHASING',  'divisi' => '120101 Material Procurement','jabatan' => 'Staff Purchasing',   'nik' => 'PYI-260125'],
            ['nama' => 'Vino Bastian Alfarizi',  'jk' => 'Laki-laki', 'dept' => 'PRODUCTION',  'divisi' => '010418 Assembly Line 3',     'jabatan' => 'Operator Packing',   'nik' => 'PYI-260126'],
            ['nama' => 'Wulan Guritno Sari',     'jk' => 'Perempuan', 'dept' => 'PRODUCTION',  'divisi' => '010310 Computer Stitching',  'jabatan' => 'Operator Stitching', 'nik' => 'PYI-260127'],
            ['nama' => 'Yoga Pratama Setiadi',   'jk' => 'Laki-laki', 'dept' => 'WAREHOUSE',   'divisi' => '067299 Warehouse Staff',     'jabatan' => 'Checker Gudang',     'nik' => 'PYI-260128'],
            ['nama' => 'Zahra Aulia Rahma',      'jk' => 'Perempuan', 'dept' => 'HRD & GA',    'divisi' => '040102 Recruitment & Train', 'jabatan' => 'Staff Training',     'nik' => 'PYI-260129'],
            ['nama' => 'Zaki Muhammad Iqbal',    'jk' => 'Laki-laki', 'dept' => 'PRODUCTION',  'divisi' => '010420 Assembly Line 4',     'jabatan' => 'Leader Assembly',    'nik' => 'PYI-260130'],
        ];

        foreach ($karyawans as $idx => $item) {
            $num   = str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT);
            $email = "karyawan{$num}@pouyuen.co.id";

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name'              => $item['nama'],
                    'nik'               => $item['nik'],
                    'jenis_kelamin'     => $item['jk'],
                    'password'          => Hash::make('password'),
                    'email_verified_at' => Carbon::now(),
                ]
            );

            if (! $user->hasRole('pegawai')) {
                $user->assignRole($pegawaiRole);
            }

            PegawaiDetail::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'bidang_id'     => $bidang->id,
                    'atasan_id'     => $atasanId,
                    'departemen'    => $item['dept'],
                    'divisi'        => $item['divisi'],
                    'jabatan'       => $item['jabatan'],
                    'nik_karyawan'  => $item['nik'],
                    'nip'           => $item['nik'],
                    'status'        => 'Masih Bekerja',
                    'tanggal_masuk' => Carbon::now()->subMonths(rand(2, 36))->toDateString(),
                ]
            );
        }
    }
}

