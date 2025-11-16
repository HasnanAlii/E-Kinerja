<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kehadiran extends Model
{
    use HasFactory;

    protected $table = 'kehadirans';

    protected $fillable = [
        'pegawai_id',
        'tanggal',
        'check_in',
        'check_out',
        'lokasi_check_in',
        'lokasi_check_out',
        'keterangan',     
        'verified_by',    
    ];

    /**
     * Relasi ke pegawai
     */
    public function pegawai()
    {
        return $this->belongsTo(PegawaiDetail::class, 'pegawai_id');
    }

    /**
     * Relasi ke user yang memverifikasi / koreksi
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
