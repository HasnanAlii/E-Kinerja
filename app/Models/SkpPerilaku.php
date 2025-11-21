<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkpPerilaku extends Model
{
    protected $table = 'skp_perilaku';

    protected $fillable = [
        'skp_id',
        'aspek',
        'perilaku',
        'ekspektasi',
        'umpan_balik',
    ];

    public function skp()
    {
        return $this->belongsTo(Skp::class, 'skp_id');
    }
}
