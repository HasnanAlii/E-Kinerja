<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skp extends Model
{
    protected $table = 'skp';

    protected $fillable = [
        'pegawai_id',
        'atasan_id',
        'bidang_id',
        'periode',
        'tanggal_mulai',
        'tanggal_selesai',
        'capaian_kinerja_organisasi',
        'pola_distribusi',
        'rating',
        'predikat',
        'status',
    ];
        protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    // Pegawai yang dinilai
    public function pegawai()
    {
        return $this->belongsTo(PegawaiDetail::class, 'pegawai_id');
    }

    // Atasan penilai
    public function atasan()
    {
        return $this->belongsTo(PegawaiDetail::class, 'atasan_id');
    }

    // Bidang pegawai
    public function bidang()
    {
        return $this->belongsTo(Bidang::class, 'bidang_id');
    }

    // Banyak baris hasil kerja
    public function hasilKerja()
    {
        return $this->hasMany(SkpHasilKerja::class, 'skp_id');
    }

    // Banyak aspek perilaku
    public function perilaku()
    {
        return $this->hasMany(SkpPerilaku::class, 'skp_id');
    }
}
