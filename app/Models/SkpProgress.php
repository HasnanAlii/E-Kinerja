<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkpProgress extends Model
{
    use HasFactory;

    protected $table = 'skp_progres';

    protected $fillable = [
        'pegawai_id',
        'skp_id',
        'persentase',
        'bukti_file',
        'keterangan',
        'tanggal_update',
    ];

    public function pegawai()
    {
        return $this->belongsTo(PegawaiDetail::class, 'pegawai_id');
    }

    public function skp()
    {
        return $this->belongsTo(Skp::class, 'skp_id');
    }
}
