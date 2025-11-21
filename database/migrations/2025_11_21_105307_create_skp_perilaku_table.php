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
        Schema::create('skp_perilaku', function (Blueprint $table) {
            $table->id();

            // Relasi SKP
            $table->foreignId('skp_id')
                ->constrained('skp')
                ->onDelete('cascade');

            // Aspek perilaku ASN
            $table->string('aspek');  
            // contoh: "Kompeten", "Harmonis", "Loyal", dst.

            $table->text('perilaku')->nullable();     // Uraian perilaku
            $table->text('ekspektasi')->nullable();   // Ekspektasi pimpinan
            $table->text('umpan_balik')->nullable();  // Umpan balik pimpinan

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skp_perilaku');
    }
};




// <?php

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
//     /**
//      * Run the migrations.
//      */
//     public function up()
//     {
//         Schema::create('skp_perilaku', function (Blueprint $table) {
//             $table->id();
//             $table->foreignId('skp_id')->constrained('skp')->onDelete('cascade');
//             $table->string('aspek');  
//             $table->text('perilaku')->nullable();     // Uraian perilaku
//             $table->text('ekspektasi')->nullable();   // Ekspektasi pimpinan
//             $table->text('umpan_balik')->nullable();  // Umpan balik pimpinan

//             $table->timestamps();
//         });
//     }


//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::dropIfExists('skp_perilaku');
//     }
// };

