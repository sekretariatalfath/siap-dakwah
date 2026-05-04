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
        Schema::create('notulensis', function (Blueprint $table) {
            $table->id();
            $table->string('unit_owner'); // Contoh: KES, ADM, dll
            $table->string('judul_syuro');
            $table->string('pimpinan_rapat');
            $table->string('kategori'); // Rutin, Proker KM, Proker Non KM
            $table->dateTime('waktu_mulai');
            $table->string('tempat');
            $table->text('link_daftar_hadir')->nullable(); // Link PDF yang di-upload ke Drive
            $table->text('link_google_docs')->nullable();  // Link Docs hasil generate
            $table->string('google_drive_file_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notulensis');
    }
};
