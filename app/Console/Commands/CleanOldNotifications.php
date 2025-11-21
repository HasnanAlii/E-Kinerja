<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class CleanOldNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:clean-old 
                            {--days=5 : Hapus notifikasi lebih tua dari X hari}
                            {--dry : Hanya tampilkan jumlah tanpa menghapus}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus notifikasi yang lebih tua dari beberapa hari (default 5 hari).';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $dry  = (bool) $this->option('dry');

        $cutoff = now()->subDays($days);

        $query = Notification::where(function($q) use ($cutoff) {
            $q->where('waktu', '<', $cutoff)
              ->orWhere(function($q2) use ($cutoff) {
                  $q2->whereNull('waktu')->where('created_at', '<', $cutoff);
              });
        });

        $count = $query->count();

        if ($dry) {
            $this->info("Dry-run: ditemukan {$count} notifikasi lebih tua dari {$days} hari (sebelum {$cutoff}).");
            return 0;
        }

        if ($count === 0) {
            $this->info("Tidak ada notifikasi yang perlu dihapus (cutoff: {$cutoff}).");
            return 0;
        }

        $deleted = $query->delete();

        $this->info("Berhasil menghapus {$deleted} notifikasi yang lebih tua dari {$days} hari.");
        Log::info("CleanOldNotifications: menghapus {$deleted} notifikasi lebih tua dari {$days} hari.");

        return 0;
    }
}
