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
        Schema::create('surat_masuks', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat');
            $table->string('pengirim');
            $table->string('jenis_kontak'); // WHATSAPP, EMAIL, dll
            $table->string('detail_kontak');
            $table->string('perihal');
            $table->string('nama_kegiatan')->nullable();
            $table->string('ditujukan_kepada');
            $table->date('tgl_terima');
            $table->string('penerima_fisik');
            $table->string('link_drive');
            $table->string('uploader')->nullable(); // Nama yang input
            $table->boolean('is_checked')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_masuks');
    }
};
