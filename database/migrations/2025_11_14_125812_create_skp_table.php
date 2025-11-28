<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('skp', function (Blueprint $table) {
        $table->id();

        // Pegawai yang dinilai
        $table->foreignId('pegawai_id')
              ->constrained('pegawai_details')
              ->onDelete('cascade');

        // Atasan / Pejabat Penilai
        $table->foreignId('atasan_id')
              ->nullable()
              ->constrained('pegawai_details') 
              ->onDelete('set null');

        // Bidang
        $table->foreignId('bidang_id')
              ->nullable()
              ->constrained('bidang')
              ->onDelete('set null');

        // Periode SKP
        $table->string('periode');
        $table->date('tanggal_mulai')->nullable();
        $table->date('tanggal_selesai')->nullable();

        // Capaian Organisasi & Distribusi
        $table->text('capaian_kinerja_organisasi')->nullable();
        $table->text('pola_distribusi')->nullable();

        // Nilai Akhir
        $table->string('rating')->nullable();
        $table->string('predikat')->nullable();
        $table->string('komentar_atasan')->nullable();

        

        // Status SKP
        $table->enum('status', ['Draft', 'Diajukan', 'Disetujui', 'Revisi','Final'])
              ->default('Draft');

        $table->timestamps();
    });
}



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skp');
    }
};

// <?php

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
//     /**
//      * Run the migrations.
//      */
// public function up()
// {
//     Schema::create('skp', function (Blueprint $table) {
//         $table->id();
//         $table->foreignId('atasan_id')
//               ->nullable()
//               ->constrained('pegawai_details')
//               ->onDelete('set null');
//         $table->foreignId('bidang_id')
//               ->nullable()
//               ->constrained('bidang')
//               ->onDelete('set null');
//         $table->string('periode');
//         $table->date('tanggal_mulai')->nullable();
//         $table->date('tanggal_selesai')->nullable();
//         $table->text('capaian_kinerja_organisasi')->nullable();
//         $table->text('pola_distribusi')->nullable();
//         // Nilai Akhir
//         // $table->double('rating')->nullable();
//         // $table->string('predikat')->nullable();


//         $table->timestamps();
//     });
// }



//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::dropIfExists('skp');
//     }
// };
