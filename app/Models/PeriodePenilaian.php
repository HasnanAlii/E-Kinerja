<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodePenilaian extends Model
{
    use HasFactory;

    protected $table = 'periode_penilaian';

    protected $fillable = [
        'nama_periode', 'tgl_mulai', 'tgl_selesai', 'status_aktif'
    ];

    public function penilaian()
    {
        return $this->hasMany(Penilaian::class, 'periode_id');
    }
}
