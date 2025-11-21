<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;

class CleanOldNotificationsTest extends Command
{
    /**
     * Perintah untuk test (hapus notifikasi lebih tua dari 1 menit)
     */
    protected $signature = 'notifications:test-clean';

    /**
     * Deskripsi
     */
    protected $description = 'TEST: Hapus notifikasi yang lebih tua dari 1 menit';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // cutoff 1 menit yang lalu
        $cutoff = now()->subMinute();

        // cek notifikasi yang lebih tua dari 1 menit
        $query = Notification::where('created_at', '<', $cutoff);

        $count = $query->count();

        if ($count === 0) {
            $this->info("Tidak ada data lebih tua dari 1 menit. (Cutoff: {$cutoff})");
            return 0;
        }

        $deleted = $query->delete();

        $this->info("TEST SUCCESS: Menghapus {$deleted} notifikasi.");
        return 0;
    }
}
