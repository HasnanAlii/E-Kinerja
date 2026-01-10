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
    Schema::create('izin_sakit', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pegawai_id')->constrained('pegawai_details')->onDelete('cascade');
        $table->enum('jenis', ['izin', 'sakit', 'cuti']);
        $table->date('tanggal_mulai');
        $table->date('tanggal_selesai');
        $table->string('file_surat')->nullable();
        $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('izin_sakit');
    }
};
