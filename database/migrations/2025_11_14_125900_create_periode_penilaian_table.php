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
    Schema::create('periode_penilaian', function (Blueprint $table) {
        $table->id();
        $table->string('nama_periode');
        $table->date('tgl_mulai');
        $table->date('tgl_selesai');
        $table->boolean('status_aktif')->default(false);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periode_penilaian');
    }
};
