<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawai_details', function (Blueprint $table) {
            if (!Schema::hasColumn('pegawai_details', 'departemen')) {
                $table->string('departemen')->nullable()->after('bidang_id');
            }
            if (!Schema::hasColumn('pegawai_details', 'divisi')) {
                $table->string('divisi')->nullable()->after('departemen');
            }
            if (!Schema::hasColumn('pegawai_details', 'nik_karyawan')) {
                $table->string('nik_karyawan')->nullable()->after('nip');
            }
        });

        // Make bidang_id nullable so forms without bidang work cleanly
        try {
            DB::statement('ALTER TABLE pegawai_details MODIFY bidang_id BIGINT UNSIGNED NULL');
        } catch (\Throwable $e) {
            // Ignore if DB driver doesn't support MODIFY syntax
        }

        // Backfill nik_karyawan from nip or user.nik, and normalize status to 'Masih Bekerja'
        $rows = DB::table('pegawai_details')->get();
        foreach ($rows as $row) {
            $user = DB::table('users')->where('id', $row->user_id)->first();
            $nikKaryawan = $row->nik_karyawan ?? $row->nip ?? ($user->nik ?? null);
            $status = (!$row->status || $row->status === 'Aktif') ? 'Masih Bekerja' : $row->status;
            DB::table('pegawai_details')->where('id', $row->id)->update([
                'nik_karyawan' => $nikKaryawan,
                'status'       => $status,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('pegawai_details', function (Blueprint $table) {
            $cols = array_filter(['departemen', 'divisi', 'nik_karyawan'], fn ($c) => Schema::hasColumn('pegawai_details', $c));
            if ($cols) {
                $table->dropColumn($cols);
            }
        });
    }
};

