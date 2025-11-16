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
        Schema::create('aktivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai_details')->onDelete('cascade');
            $table->date('tanggal');
            $table->text('uraian_tugas');
            $table->string('waktu_pelaksanaan');
            $table->text('hasil_pekerjaan')->nullable();
            $table->string('bukti_file')->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'revisi', 'ditolak'])->default('menunggu');
            $table->text('komentar_atasan')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aktivitas');
    }
};
