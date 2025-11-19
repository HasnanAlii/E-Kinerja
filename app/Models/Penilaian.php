<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    use HasFactory;

    protected $table = 'penilaian';

    protected $fillable = [
        'pegawai_id', 'atasan_id', 'periode_id',
        'skp', 'kedisiplinan', 'perilaku', 'komunikasi',
        'tanggung_jawab', 'kerja_sama', 'produktivitas',
        'nilai_total', 'kategori','status'
    ];

    public function pegawai()
    {
        return $this->belongsTo(PegawaiDetail::class, 'pegawai_id');
    }

    public function atasan()
    {
        return $this->belongsTo(User::class, 'atasan_id');
    }

    public function periode()
    {
        return $this->belongsTo(PeriodePenilaian::class, 'periode_id');
    }
}
