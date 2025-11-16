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
            $table->foreignId('bidang_id')->constrained('bidang')->onDelete('cascade');
            $table->string('nama_target');
            $table->text('indikator')->nullable();
            $table->string('periode');
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
