<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PeriodePenilaian;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PeriodePenilaianSeeder extends Seeder
{
    public function run(): void
    {
        $periodeList = [
            [
                'nama_periode'  => 'Triwulan 1 - 2025',
                'tgl_mulai'     => '2025-01-01',
                'tgl_selesai'   => '2025-03-31',
                'status_aktif'  => true,  // Hanya satu yang aktif
            ],
            [
                'nama_periode'  => 'Triwulan 2 - 2025',
                'tgl_mulai'     => '2025-04-01',
                'tgl_selesai'   => '2025-06-30',
                'status_aktif'  => false,
            ],
            [
                'nama_periode'  => 'Triwulan 3 - 2025',
                'tgl_mulai'     => '2025-07-01',
                'tgl_selesai'   => '2025-09-30',
                'status_aktif'  => false,
            ],
        ];

        foreach ($periodeList as $periode) {
            PeriodePenilaian::updateOrCreate(
                ['nama_periode' => $periode['nama_periode']],
                $periode
            );
        }
    }
}
