<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratMasuk extends Model
{
    use HasFactory;

    /**
     * Nama Tabel di SQLite lokal
     * Pastikan Mas sudah menjalankan migrasi untuk tabel ini.
     */
    protected $table = 'surat_masuks';

    /**
     * Mass Assignment
     * Daftar kolom yang boleh diisi melalui SuratMasuk::create() atau ::update()
     */
    protected $fillable = [
        'no_surat', 
        'pengirim', 
        'jenis_kontak', 
        'detail_kontak', 
        'perihal', 
        'nama_kegiatan', 
        'ditujukan_kepada', 
        'tgl_terima', 
        'penerima_fisik', 
        'link_drive', 
        'uploader', 
        'is_checked'
    ];

    /**
     * Casting Tipe Data
     * Mengubah format dari database ke tipe data PHP secara otomatis.
     */
    protected $casts = [
        'is_checked' => 'boolean', // Di SQLite simpan 0/1, di PHP jadi true/false
        'tgl_terima' => 'date',    // Memudahkan Mas untuk format tgl di Blade
    ];

    /**
     * Catatan Penting:
     * Saya menghapus fungsi getRows() dan sushiShouldCache() karena Mas 
     * sekarang menggunakan sistem "Mirroring" ke SQLite. 
     * Data di web Mas akan jauh lebih cepat diakses dan lebih stabil.
     */
}