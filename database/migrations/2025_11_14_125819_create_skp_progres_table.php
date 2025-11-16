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
    Schema::create('skp_progres', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pegawai_id')->constrained('pegawai_details')->onDelete('cascade');
        $table->foreignId('skp_id')->constrained('skp')->onDelete('cascade');
        $table->integer('persentase')->default(0);
        $table->string('bukti_file')->nullable();
        $table->date('tanggal_update');
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
