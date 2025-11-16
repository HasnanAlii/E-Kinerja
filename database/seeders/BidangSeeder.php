<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BidangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bidangList = [
            'Administrasi',
            'Perpustakaan',
            'Kearsipan',
            'Layanan Informasi',
            'Teknologi Informasi',
            'Umum'
        ];

        foreach ($bidangList as $b) {
            DB::table('bidang')->updateOrInsert(
                ['nama_bidang' => $b],
                [
                    'nama_bidang' => $b,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]
            );
        }
    }
}
