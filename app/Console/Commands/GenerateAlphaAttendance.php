<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PegawaiDetail;
use App\Models\Kehadiran;
use Carbon\Carbon;

class GenerateAlphaAttendance extends Command
{
    protected $signature = 'kehadiran:generate-alpha';
    protected $description = 'Generate kehadiran alpha jika pegawai tidak absen hari ini';

    public function handle()
    {
        $today = Carbon::today();

        $pegawais = PegawaiDetail::all();

        foreach ($pegawais as $pegawai) {
            $exists = Kehadiran::where('pegawai_id', $pegawai->id)
                ->whereDate('tanggal', $today)
                ->exists();

            if (!$exists) {
                Kehadiran::create([
                    'pegawai_id' => $pegawai->id,
                    'tanggal'    => $today,
                    'jenis'      => 'alpha',
                    'check_in'   => null,
                    'check_out'  => null,
                ]);
            }
        }

        $this->info('Alpha generated for ' . $today->toDateString());
    }
}
