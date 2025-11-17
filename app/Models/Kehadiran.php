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
          'jenis'
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
    public function izin()
    {
        return $this->hasMany(IzinSakit::class, 'pegawai_id', 'pegawai_id')
            ->where('status', 'disetujui');
    }



}
