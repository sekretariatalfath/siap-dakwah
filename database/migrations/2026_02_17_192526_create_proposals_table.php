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
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->string('proposal_id'); 
            $table->string('kategori');
            $table->string('pemohon');
            $table->string('nama_proker');
            $table->string('bentuk_kegiatan'); // <--- PASTIKAN BARIS INI ADA
            $table->string('tempat');
            $table->bigInteger('anggaran');
            $table->integer('target_peserta');
            $table->integer('jumlah_panitia');
            $table->string('link_pdf');
            $table->string('cp_nama');
            $table->string('cp_wa');
            $table->string('cp_email');
            $table->string('cp_line')->nullable();
            $table->boolean('is_checked')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
