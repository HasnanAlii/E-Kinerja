<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan role tersedia
        $adminRole   = Role::firstOrCreate(['name' => 'admin']);
        $pegawaiRole = Role::firstOrCreate(['name' => 'pegawai']);

        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */
        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'              => 'Admin Monitoring Pou Yuen',
                'nik'               => 'PYI-000001',
                'jenis_kelamin'     => 'Laki-laki',
                'profile_photo'     => null,
                'password'          => Hash::make('password'),
                'email_verified_at' => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ]
        );

        // Assign role admin
        if (! $admin->hasRole('admin')) {
            $admin->assignRole($adminRole);
        }

        /*
        |--------------------------------------------------------------------------
        | KARYAWAN UTAMA (pegawai@gmail.com)
        |--------------------------------------------------------------------------
        */
        $pegawai = User::updateOrCreate(
            ['email' => 'pegawai@gmail.com'],
            [
                'name'              => 'Andi Saputra Pratama',
                'nik'               => 'PYI-260100',
                'jenis_kelamin'     => 'Laki-laki',
                'profile_photo'     => null,
                'password'          => Hash::make('password'),
                'email_verified_at' => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ]
        );

        // Assign role pegawai
        if (! $pegawai->hasRole('pegawai')) {
            $pegawai->assignRole($pegawaiRole);
        }
    }
}
