<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Atasan;
use App\Models\User;
use App\Models\Bidang;
use Illuminate\Support\Facades\Hash;

class AtasanSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan ada bidang
        if (Bidang::count() == 0) {
            Bidang::create(['nama_bidang' => 'Umum']);
            Bidang::create(['nama_bidang' => 'Keuangan']);
            Bidang::create(['nama_bidang' => 'Perencanaan']);
        }

        $bidang = Bidang::all();

        // Buat akun user atasan + entri tabel atasan
        $atasanList = [
            [
                'name' => 'Budi Santoso',
                'jabatan' => 'Kepala Bidang Umum',
                'nip' => '19791231 200501 1 001',
            ],
            [
                'name' => 'Siti Rahmawati',
                'jabatan' => 'Kepala Bidang Keuangan',
                'nip' => '19820414 200601 2 002',
            ],
            [
                'name' => 'Dedi Permana',
                'jabatan' => 'Kepala Perencanaan',
                'nip' => '19880521 201001 1 003',
            ],
            [
                'name' => 'Nur Aisyah',
                'jabatan' => 'Koordinator Internal',
                'nip' => null,
            ],
            [
                'name' => 'Rizky Maulana',
                'jabatan' => 'Supervisor Operasional',
                'nip' => null,
            ],
        ];

        foreach ($atasanList as $key => $a) {

            // Buat user akun
            $user = User::create([
                'name' => $a['name'],
                'email' => strtolower(str_replace(' ', '', $a['name'])) . '@example.com',
                'password' => Hash::make('password'),
            ]);

            // Assign role atasan (optional)
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('atasan');
            }

            // Buat entri atasan
            Atasan::create([
                'user_id'   => $user->id,
                'bidang_id' => $bidang->random()->id,
                'nip'       => $a['nip'],
                'name'      => $a['name'],
                'jabatan'   => $a['jabatan'],
                'masa_kontrak' => now()->addYear(),
                'foto'      => null,
            ]);
        }
    }
}
