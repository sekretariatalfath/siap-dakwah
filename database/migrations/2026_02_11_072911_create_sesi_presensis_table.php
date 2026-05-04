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
        Schema::create('sesi_presensis', function (Blueprint $table) {
            $table->id();
            $table->string('id_sesi')->unique(); // ID seperti PRE-XXXXXX[cite: 21]
            $table->string('nama_kegiatan');
            $table->string('kategori');
            $table->string('unit_host');
            $table->string('tgl_pelaksanaan');
            $table->string('is_active')->default('1'); // 1 = Buka, 0 = Tutup[cite: 21]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sesi_presensis');
    }
};
