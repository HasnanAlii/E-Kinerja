<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atasan extends Model
{
    use HasFactory;

    protected $table = 'atasan';

    protected $fillable = [
        'user_id',
        'name',
        'bidang_id',
        'nip',
        'jabatan',
        'masa_kontrak',
        'foto',
    ];

    /**
     * Relasi ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke bidang
     */
    public function bidang()
    {
        return $this->belongsTo(Bidang::class);
    }
}
