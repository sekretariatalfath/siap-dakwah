<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hanya buat tabel jika belum ada
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }

        // Isi default setting jika belum ada
        if (DB::table('settings')->where('key', 'auth_source')->doesntExist()) {
            DB::table('settings')->insert([
                'key' => 'auth_source',
                'value' => 'database',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Jangan hapus tabelnya di down() karena tabel ini sudah ada sebelumnya
    }
};
