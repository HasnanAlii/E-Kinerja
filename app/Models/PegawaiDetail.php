<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PegawaiDetail extends Model
{
    use HasFactory;
    protected $table = 'pegawai_details';

    protected $fillable = [
        'user_id',
        'bidang_id',
        'atasan_id',
        'name',
        'nip',
        'jabatan',
        'masa_kontrak',
        'foto',
        'status',
        'tanggal_masuk',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bidang()
    {
        return $this->belongsTo(Bidang::class);
    }

    public function atasan()
    {
        return $this->belongsTo(Atasan::class, 'atasan_id');
    }
    

    public function aktivitas()
    {
        return $this->hasMany(Aktivitas::class, 'pegawai_id');
    }

    public function kehadiran()
    {
        return $this->hasMany(Kehadiran::class, 'pegawai_id');
    }

    public function izinSakit()
    {
        return $this->hasMany(IzinSakit::class, 'pegawai_id');
    }

    public function skpProgress()
    {
        return $this->hasMany(SkpProgress::class, 'pegawai_id');
    }

    public function penilaian()
    {
        return $this->hasMany(Penilaian::class, 'pegawai_id');
    }
}
