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
        $bidang = Bidang::firstOrCreate(['nama_bidang' => 'Produksi']);

        // Ambil user
        $pegawai = User::where('email', 'pegawai@gmail.com')->first();

        if (! $pegawai) {
            return; // hentikan jika user belum dibuat
        }

        // Data karyawan lengkap sesuai struktur terbaru PT Pou Yuen
        $data = [
            'user_id'        => $pegawai->id,
            'bidang_id'      => $bidang->id,
            'atasan_id'      => 1,
            'departemen'     => 'PRODUCTION',
            'divisi'         => '010410 Assembly Line Utama',
            'jabatan'        => 'Operator Assembly',
            'nik_karyawan'   => 'PYI-260100',
            'nip'            => 'PYI-260100',
            'foto'           => null,
            'status'         => 'Masih Bekerja',
            'tanggal_masuk'  => Carbon::now()->subMonths(6)->toDateString(),
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
