<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Atasan;
use App\Models\User;
use App\Models\Bidang;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AtasanSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * 1. Pastikan role 'atasan' ada
         */
        Role::firstOrCreate(['name' => 'atasan', 'guard_name' => 'web']);

        /**
         * 2. Pastikan bidang tersedia
         */
        if (Bidang::count() == 0) {
            Bidang::create(['nama_bidang' => 'Umum']);
            Bidang::create(['nama_bidang' => 'Keuangan']);
            Bidang::create(['nama_bidang' => 'Perencanaan']);
        }

        // Ambil ulang setelah dibuat
        $bidang = Bidang::all();


        /**
         * 3. Daftar atasan
         */
        $atasanList = [

            [
                'name' => 'Siti Rahmawati',
                'jabatan' => 'Kepala Bidang Keuangan',
                'nip' => '19820414 200601 2 002',
                'bidang' => 'Keuangan',
            ],
       
          
        ];


        /**
         * 4. Proses pembuatan user + atasan
         */
        foreach ($atasanList as $a) {

            // Generate email otomatis
            $email = strtolower(str_replace(' ', '', $a['name'])) . '@example.com';

            // Buat user
            $user = User::create([
                'name' => $a['name'],
                'email' => $email,
                'password' => Hash::make('password'),
            ]);

            // Assign role
            $user->assignRole('atasan');

            // Dapatkan bidang sesuai nama (jika ada)
            $bidang_id = 1;

            if ($a['bidang']) {
                $bid = Bidang::where('nama_bidang', $a['bidang'])->first();
                if ($bid) {
                    $bidang_id = $bid->id;
                }
            }

            // Simpan ke tabel atasan
            Atasan::create([
                'user_id'      => $user->id,
                'bidang_id'    => $bidang_id,     // bisa null
                'nip'          => $a['nip'],
                'name'         => $a['name'],
                'jabatan'      => $a['jabatan'],
                'masa_kontrak' => now()->addYear(),
                'foto'         => null,
            ]);
        }
    }
}
