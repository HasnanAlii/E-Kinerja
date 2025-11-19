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

        // Relasi
        $table->foreignId('pegawai_id')->constrained('pegawai_details')->onDelete('cascade');
        $table->foreignId('bidang_id')->constrained('bidang')->onDelete('cascade');

        // Informasi SKP
        $table->string('nama_target');         // Nama sasaran kerja
        $table->text('indikator')->nullable(); // Indikator keberhasilan

        // Periode
        $table->string('periode'); // contoh: 2025, atau 2025-Semester 1

        // TARGET
        $table->integer('target_kuantitas')->nullable();
        $table->string('satuan_kuantitas')->nullable(); // contoh: laporan, kegiatan, dokumen

        $table->integer('target_kualitas')->nullable(); // 1-100 %
        $table->integer('target_waktu')->nullable();    // dalam bulan
        $table->double('target_biaya')->nullable();

        // REALISASI
        $table->integer('realisasi_kuantitas')->nullable();
        $table->integer('realisasi_kualitas')->nullable();
        $table->integer('realisasi_waktu')->nullable();
        $table->double('realisasi_biaya')->nullable();

        // Perhitungan nilai
        $table->double('nilai_capaian')->nullable(); // (kuantitas + kualitas + waktu) / 3

        // Catatan atasan
        $table->text('catatan')->nullable();
        $table->enum('status', ['Draft', 'Diajukan', 'Dinilai', 'Selesai'])->default('Draft');

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
