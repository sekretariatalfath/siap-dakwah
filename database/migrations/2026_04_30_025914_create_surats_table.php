<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surats', function (Blueprint $table) {
            $table->id();
            // Sesuai urutan kolom A - R di Spreadsheet kamu:
            $table->string('no_surat_full');  // A
            $table->string('jenis');          // B
            $table->string('lingkup');        // C
            $table->string('detail');         // D
            $table->string('nama_acara');     // E
            $table->string('fakultas');       // F
            $table->string('bidang_fakultas');// G
            $table->string('asal_pengisi');   // H
            $table->string('nama_pengisi');   // I
            $table->string('tanggal');        // J (Dibuat string dulu biar gampang sync)
            $table->integer('nomor_urut');    // K
            $table->string('bulan_romawi');   // L
            $table->integer('tahun');         // M
            $table->text('perihal');          // N
            $table->string('penyelenggara');  // O
            $table->string('catatan')->nullable(); // P
            $table->string('link_drive')->nullable(); // Q
            $table->string('batch_id');       // R
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surats');
    }
};
