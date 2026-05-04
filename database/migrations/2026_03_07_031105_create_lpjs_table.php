<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('lpjs', function (Blueprint $table) {
            $table->id();
            $table->string('id_lpj');
            $table->string('tgl_input');
            $table->string('nama_proker');
            $table->string('pemohon');
            // Poin-poin baru
            $table->integer('realisasi_peserta');        // Poin 1
            $table->text('ketercapaian_tujuan');         // Poin 2
            $table->text('realisasi_sasaran');           // Poin 3
            $table->bigInteger('anggaran_sponsor')->default(0); // Poin 7
            $table->bigInteger('realisasi_anggaran');    // Poin 8 (Terpakai)
            $table->string('link_lpj_pdf');              // Poin 9
            $table->string('link_dokumentasi');          // Poin 10
            $table->string('link_evaluasi');             // Poin 4-6 (Dijadikan 1 link PDF/Doc)
            
            $table->boolean('is_checked')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lpjs');
    }
};
