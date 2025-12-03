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
        Schema::create('pegawai_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('bidang_id')->constrained('bidang')->onDelete('cascade');
            $table->foreignId('atasan_id')->nullable()->constrained('atasan')->onDelete('set null');
            $table->string('nip')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('foto')->nullable();  
            // $table->string('masa_kontrak')->nullable();  
            $table->string('status')->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai_details');
    }
};
