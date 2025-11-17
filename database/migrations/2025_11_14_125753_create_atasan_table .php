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
        Schema::create('atasan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('bidang_id')->constrained('bidang')->onDelete('cascade');
            $table->string('nip')->nullable();
            $table->string('name')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('status')->nullable();
            $table->string('golongan')->nullable();

            $table->date('tanggal_masuk')->nullable();

            $table->date('masa_kontrak')->nullable();
            $table->string('foto')->nullable();  
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atasan');
    }
};
