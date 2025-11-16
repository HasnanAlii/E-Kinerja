<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IzinSakit extends Model
{
    use HasFactory;

    protected $table = 'izin_sakit';

    protected $fillable = [
        'pegawai_id', 'jenis', 'tanggal_mulai', 'tanggal_selesai',
        'file_surat', 'status'
    ];

    public function pegawai()
    {
        return $this->belongsTo(PegawaiDetail::class, 'pegawai_id');
    }
}
