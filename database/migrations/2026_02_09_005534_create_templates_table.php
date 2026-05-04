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
    Schema::create('templates', function (Blueprint $table) {
        $table->id();
        $table->string('judul');        // Contoh: "Surat Peminjaman"
        $table->string('deskripsi')->nullable(); // Penjelasan singkat
        $table->string('kategori');     // Pilihan: surat, proposal, lpj, dll
        $table->string('tipe_file');    // Pilihan: docx, xlsx, pdf (buat nentuin ikon)
        $table->text('link_drive');     // Link Google Drive (Bisa diubah Superadmin)
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
