<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aktivitas extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id', 'tanggal', 'uraian_tugas', 'waktu_pelaksanaan',
        'hasil_pekerjaan', 'bukti_file', 'status', 'komentar_atasan'
    ];

    public function pegawai()
    {
        return $this->belongsTo(PegawaiDetail::class, 'pegawai_id');
    }
}
