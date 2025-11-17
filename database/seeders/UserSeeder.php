<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
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
        // Buat role jika belum ada
        $adminRole   = Role::firstOrCreate(['name' => 'admin']);
        // $atasanRole  = Role::firstOrCreate(['name' => 'atasan']);
        $pegawaiRole = Role::firstOrCreate(['name' => 'pegawai']);

        // ADMIN
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin E-Kinerja',
                'password' => Hash::make('password'),
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );
        $admin->assignRole($adminRole);

;

        // PEGAWAI NON-PNS
        $pegawai = User::firstOrCreate(
            ['email' => 'pegawai@gmail.com'],
            [
                'name' => 'Pegawai Non-PNS',
                'password' => Hash::make('password'),
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );
        $pegawai->assignRole($pegawaiRole);
    }
}
