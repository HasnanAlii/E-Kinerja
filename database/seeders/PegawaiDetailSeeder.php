<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Bidang;
use Carbon\Carbon;

class PegawaiDetailSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan minimal ada 1 bidang
        $bidang = Bidang::firstOrCreate(['nama_bidang' => 'Umum']);

        // Ambil user sesuai UserSeeder
        $admin   = User::where('email', 'admin@gmail.com')->first();
        $pegawai = User::where('email', 'pegawai@gmail.com')->first();

        $data = [];

        /**
         * 2) PEGAWAI — punya atasan_id
         */
        if ($pegawai) {
            $data[] = [
                'user_id'      => $pegawai->id,
                'bidang_id'    => $bidang->id,
                'atasan_id'    => 1, // Relasi ke tabel atasan
                'nip'          => '2001',
                'jabatan'      => 'Staf Non-PNS',
                'masa_kontrak' => Carbon::now()->addMonths(6),
                'foto'         => null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
        }

        /**
         * Insert / Update
         */
        foreach ($data as $row) {
            DB::table('pegawai_details')->updateOrInsert(
                ['user_id' => $row['user_id']], // cek berdasarkan user_id
                $row
            );
        }
    }
}
