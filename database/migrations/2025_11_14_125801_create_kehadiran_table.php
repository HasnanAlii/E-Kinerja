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
    Schema::create('kehadirans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pegawai_id')->constrained('pegawai_details')->onDelete('cascade');
        $table->date('tanggal');
        $table->time('check_in')->nullable();
        $table->time('check_out')->nullable();
        $table->string('lokasi_check_in')->nullable();
        $table->string('lokasi_check_out')->nullable();
        $table->text('keterangan')->nullable();
        $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kehadirans');
    }
};
