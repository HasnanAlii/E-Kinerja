<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('zona_aktivasi_harians')) {
            Schema::create('zona_aktivasi_harians', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pegawai_id');
                $table->date('tanggal');
                $table->string('kode_referal', 30);
                $table->boolean('is_active')->default(false);
                $table->timestamp('activated_at')->nullable();
                // JSON encoding of daily visual appearance features (color histogram, body proportions, etc.)
                $table->longText('ciri_visual')->nullable();
                // Base64 or small thumbnail snapshot of the first appearance in the zone today
                $table->longText('snapshot_wajah')->nullable();
                $table->timestamp('first_entered_at')->nullable();
                $table->timestamps();

                $table->foreign('pegawai_id')->references('id')->on('pegawai_details')->onDelete('cascade');
                $table->unique(['pegawai_id', 'tanggal']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('zona_aktivasi_harians');
    }
};
