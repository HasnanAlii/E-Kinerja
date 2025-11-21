<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkpHasilKerja extends Model
{
    protected $table = 'skp_hasil_kerja';

    protected $fillable = [
        'skp_id',
        'jenis',
        'rhk_pimpinan',
        'rhk',
        'aspek',
        'indikator_kinerja',
        'target',
        'realisasi',
        'umpan_balik',
    ];

    public function skp()
    {
        return $this->belongsTo(Skp::class, 'skp_id');
    }
    public function pegawai()
    {
        return $this->hasOneThrough(
            PegawaiDetail::class,      // Tabel tujuan
            Skp::class,                // Perantara
            'id',                      // FK di SKP (skp.id)
            'id',                      // FK di PegawaiDetail (pegawai_detail.id)
            'skp_id',                  // FK di SkpHasilKerja
            'pegawai_detail_id'        // FK di SKP
        );
    }
}
