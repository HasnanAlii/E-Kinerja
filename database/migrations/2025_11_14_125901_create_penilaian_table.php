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
    Schema::create('penilaian', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pegawai_id')->constrained('pegawai_details')->onDelete('cascade');
        $table->foreignId('atasan_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('periode_id')->constrained('periode_penilaian')->onDelete('cascade');
        $table->integer('skp');
        $table->boolean('status')->default(false);
        $table->integer('kedisiplinan');
        $table->integer('perilaku');
        $table->integer('komunikasi');
        $table->integer('tanggung_jawab');
        $table->integer('kerja_sama');
        $table->integer('produktivitas');
        $table->float('nilai_total');
        $table->enum('kategori', ['Sangat Baik', 'Baik', 'Cukup', 'Kurang']);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian');
    }
};
