<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skp extends Model
{
    use HasFactory;

    protected $table = 'skp';

    protected $fillable = [
        'pegawai_id',
        'bidang_id',
        'nama_target',
        'indikator',
        'periode',
        'target_kuantitas',
        'satuan_kuantitas',
        'target_kualitas',
        'target_waktu',
        'target_biaya',
        'realisasi_kuantitas',
        'realisasi_kualitas',
        'realisasi_waktu',
        'realisasi_biaya',
        'nilai_capaian',
        'status',
        'catatan',
    ];

    public function bidang()
    {
        return $this->belongsTo(Bidang::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(PegawaiDetail::class, 'pegawai_id');
    }

    public function progres()
    {
        return $this->hasMany(SkpProgres::class, 'skp_id');
    }
}
