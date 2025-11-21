<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'aktivitas',
        'waktu',
        'read_at'
    ];

    protected $dates = ['waktu', 'read_at'];
}
