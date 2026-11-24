<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZonaAktivasiHarian extends Model
{
    use HasFactory;

    protected $table = 'zona_aktivasi_harians';

    protected $fillable = [
        'pegawai_id',
        'tanggal',
        'kode_referal',
        'is_active',
        'activated_at',
        'ciri_visual',
        'snapshot_wajah',
        'first_entered_at',
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'activated_at'     => 'datetime',
        'first_entered_at' => 'datetime',
    ];

    public function pegawai()
    {
        return $this->belongsTo(PegawaiDetail::class, 'pegawai_id');
    }

    /**
     * Generate a deterministic yet unique daily referral code for an employee & date.
     * Resets automatically every new calendar day.
     */
    public static function generateDailyCode(int $pegawaiId, ?string $dateStr = null): string
    {
        $dateStr = $dateStr ?: Carbon::today()->toDateString();
        $hash = strtoupper(substr(hash('sha256', "POUYUEN-REF-{$pegawaiId}-{$dateStr}-" . config('app.key')), 0, 6));
        return "PY-" . $hash;
    }

    /**
     * Get or create today's referral record for an employee.
     */
    public static function getOrCreateToday(int $pegawaiId): self
    {
        $today = Carbon::today()->toDateString();

        return self::firstOrCreate(
            [
                'pegawai_id' => $pegawaiId,
                'tanggal'    => $today,
            ],
            [
                'kode_referal' => self::generateDailyCode($pegawaiId, $today),
                'is_active'    => false,
            ]
        );
    }
}
