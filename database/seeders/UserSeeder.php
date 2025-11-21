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
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'            => 'Admin E-Kinerja',
                'nik'             => '3275000000000001',
                'jenis_kelamin'   => 'Laki-laki',
                'tanggal_lahir'   => '1990-01-01',
                'tempat_lahir'    => 'Cianjur',
                'agama'           => 'Islam',
                'alamat'          => 'Jl. Raya Cianjur No. 1',
                'telp'            => '081111111111',
                'profile_photo'   => null,
                'password'        => Hash::make('password'),
                'email_verified_at' => Carbon::now(),
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now(),
            ]
        );

        // Assign role admin
        if (! $admin->hasRole('admin')) {
            $admin->assignRole($adminRole);
        }

        /*
        |--------------------------------------------------------------------------
        | PEGAWAI NON-PNS
        |--------------------------------------------------------------------------
        */
        $pegawai = User::firstOrCreate(
            ['email' => 'pegawai@gmail.com'],
            [
                'name'            => 'Pegawai Non-PNS',
                'nik'             => '3275000000000002',
                'jenis_kelamin'   => 'Perempuan',
                'tanggal_lahir'   => '1995-02-10',
                'tempat_lahir'    => 'Cianjur',
                'agama'           => 'Islam',
                'alamat'          => 'Jl. HOS Cokroaminoto, Cianjur',
                'telp'            => '082222222222',
                'profile_photo'   => null,
                'password'        => Hash::make('password'),
                'email_verified_at' => Carbon::now(),
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now(),
            ]
        );

        // Assign role pegawai
        if (! $pegawai->hasRole('pegawai')) {
            $pegawai->assignRole($pegawaiRole);
        }
    }
}
