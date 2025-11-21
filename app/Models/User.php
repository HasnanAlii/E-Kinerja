<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'nik',
        'jenis_kelamin',
        'tanggal_lahir',
        'tempat_lahir',
        'agama',
        'alamat',
        'telp',
        'password',
        'profile_photo',
    ];




    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'tanggal_lahir' => 'date',
        ];
    }
   public function detail()
    {
        return $this->hasOne(PegawaiDetail::class, 'user_id');
    }


    public function bawahan()
    {
        return $this->hasMany(PegawaiDetail::class, 'atasan_id');
    }

    public function penilaianDibuat()
    {
        return $this->hasMany(Penilaian::class, 'atasan_id');
    }

    public function pegawaiDetail()
    {
        return $this->hasOne(PegawaiDetail::class, 'user_id');
    }

    public function atasan()
    {
        return $this->hasOne(Atasan::class, 'user_id');
    }
    public function bidang()
    {
        return $this->belongsTo(Bidang::class);
    }




}
