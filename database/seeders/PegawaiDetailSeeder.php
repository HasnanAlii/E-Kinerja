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

        // Ambil user
        $pegawai = User::where('email', 'pegawai@gmail.com')->first();

        if (! $pegawai) {
            return; // hentikan jika user belum dibuat
        }

        // Data pegawai detail lengkap sesuai tabel
        $data = [
            'user_id'        => $pegawai->id,
            'bidang_id'      => $bidang->id,
            'atasan_id'      => 1, // id pada tabel atasan (pastikan ada)
            'nip'            => '2001',
            'jabatan'        => 'Staf Non-PNS',
            'foto'           => null,
            'status'         => 'aktif',
            'tanggal_masuk'  => Carbon::now()->subMonths(3),
            'created_at'     => now(),
            'updated_at'     => now(),
        ];

        // Insert / update berdasarkan user_id
        DB::table('pegawai_details')->updateOrInsert(
            ['user_id' => $pegawai->id],
            $data
        );
    }
}
