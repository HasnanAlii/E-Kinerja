<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skp extends Model
{
    use HasFactory;

    protected $table = 'skp';

    protected $fillable = [
        'bidang_id', 'nama_target', 'indikator', 'periode'
    ];

    public function bidang()
    {
        return $this->belongsTo(Bidang::class);
    }

    public function progress()
    {
        return $this->hasMany(SkpProgress::class, 'skp_id');
    }
}
