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
    Schema::create('skp_hasil_kerja', function (Blueprint $table) {
        $table->id();

        // Relasi ke SKP
        $table->foreignId('skp_id')
              ->constrained('skp')
              ->onDelete('cascade');

        // Utama / Hasil Kerja Tambahan
        $table->enum('jenis', ['Utama', 'Hasil Kerja'])
              ->default('Utama');

        // Kolom SKP sesuai tabel pada gambar
        $table->string('rhk_pimpinan')->nullable();
        $table->string('rhk')->nullable();
        $table->string('aspek')->nullable(); // Kuantitas / Kualitas / Waktu
        $table->text('indikator_kinerja')->nullable();
        $table->string('target')->nullable();
        $table->string('realisasi')->nullable();
        $table->string('umpan_balik')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skp_hasil_kerja');
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
//     Schema::create('skp_hasil_kerja', function (Blueprint $table) {
//         $table->id();
//         $table->foreignId('skp_id')->constrained('skp')->onDelete('cascade');
//         $table->enum('jenis', ['Utama', 'Hasil Kerja'])->default('Utama');
//         $table->foreignId('pegawai_id')->constrained('pegawai_details')->onDelete('cascade');
//         $table->string('rhk_pimpinan')->nullable();
//         $table->string('rhk')->nullable();
//         $table->string('aspek')->nullable();
//         $table->text('indikator_kinerja')->nullable();
//         $table->string('target')->nullable();
//         $table->string('realisasi')->nullable();
//         $table->string('dokumen')->nullable();
//         $table->string('umpan_balik')->nullable();
//         $table->enum('status', ['Draft', 'Diajukan', 'Dinilai', 'Final'])->default('Draft');
//         $table->timestamps();
//     });
// }

//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::dropIfExists('skp_hasil_kerja');
//     }
// };
