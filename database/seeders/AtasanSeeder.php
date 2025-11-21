<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Atasan;
use App\Models\User;
use App\Models\Bidang;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class AtasanSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * 1. Pastikan role 'atasan' ada
         */
        $atasanRole = Role::firstOrCreate(['name' => 'atasan']);


        /**
         * 2. Pastikan bidang tersedia
         */
        if (Bidang::count() == 0) {
            Bidang::create(['nama_bidang' => 'Umum']);
            Bidang::create(['nama_bidang' => 'Keuangan']);
            Bidang::create(['nama_bidang' => 'Perencanaan']);
        }


        /**
         * 3. Data atasan (tambahkan sesuka Anda)
         */
        $atasanList = [
            [
                'name'           => 'Siti Rahmawati',
                'jabatan'        => 'Kepala Bidang Keuangan',
                'nip'            => '19820414 200601 2 002',
                'nik'            => '3275004104820002',
                'jenis_kelamin'  => 'Perempuan',
                'tanggal_lahir'  => '1982-04-14',
                'tempat_lahir'   => 'Cianjur',
                'agama'          => 'Islam',
                'alamat'         => 'Jl. Veteran Cianjur',
                'telp'           => '081234567890',
                'bidang'         => 'Keuangan',
                'golongan'       => 'III/a',
                'status'         => 'aktif',
                'tanggal_masuk'  => Carbon::now()->subYears(5),
                'masa_kontrak'   => Carbon::now()->addYear(),
                'foto'           => null,
            ],
        ];


        /**
         * 4. Proses user + data atasan
         */
        foreach ($atasanList as $a) {

            // Email berdasarkan nama
            $email = strtolower(str_replace(' ', '', $a['name'])) . '@example.com';

            /**
             * ➤ Buat user lengkap di tabel users
             */
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'            => $a['name'],
                    'nik'             => $a['nik'],
                    'jenis_kelamin'   => $a['jenis_kelamin'],
                    'tanggal_lahir'   => $a['tanggal_lahir'],
                    'tempat_lahir'    => $a['tempat_lahir'],
                    'agama'           => $a['agama'],
                    'alamat'          => $a['alamat'],
                    'telp'            => $a['telp'],
                    'profile_photo'   => null,
                    'password'        => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            // Berikan role atasan
            if (! $user->hasRole('atasan')) {
                $user->assignRole($atasanRole);
            }

            // Cari bidang
            $bidang_id = Bidang::where('nama_bidang', $a['bidang'])->value('id') ?? 1;

            /**
             * ➤ Simpan ke tabel atasan lengkap
             */
            Atasan::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'user_id'       => $user->id,
                    'bidang_id'     => $bidang_id,
                    'nip'           => $a['nip'],
                    'name'          => $a['name'],
                    'jabatan'       => $a['jabatan'],
                    'status'        => $a['status'],
                    'golongan'      => $a['golongan'],
                    'tanggal_masuk' => $a['tanggal_masuk'],
                    'foto'          => $a['foto'],
                ]
            );
        }
    }
}
