<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('skp_progres', function (Blueprint $table) {
            $table->id();

            // Relasi Pegawai & SKP
            $table->foreignId('pegawai_id')
                ->constrained('pegawai_details')
                ->onDelete('cascade');

            $table->foreignId('skp_id')
                ->constrained('skp')
                ->onDelete('cascade');

            // Progres SKP
            $table->integer('persentase')->default(0);  // 0–100
            $table->string('bukti_file')->nullable();   // path file bukti
            $table->text('keterangan')->nullable();     // deskripsi kegiatan

            // Waktu update progres
            $table->date('tanggal_update')->useCurrent(); // default tanggal hari ini

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skp_progres');
    }
};
