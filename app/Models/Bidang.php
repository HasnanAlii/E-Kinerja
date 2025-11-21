<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bidang extends Model
{
    use HasFactory;
  protected $table = 'bidang';
    protected $fillable = ['nama_bidang'];

    public function pegawai()
    {
        return $this->hasMany(PegawaiDetail::class);
    }

    public function skp()
    {
        return $this->hasMany(Skp::class, 'bidang_id');
    }
}
