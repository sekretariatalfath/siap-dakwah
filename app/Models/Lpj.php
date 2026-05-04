<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lpj extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model ini.
     */
    protected $table = 'lpjs';

    /**
     * Kolom yang dapat diisi secara massal.
     * Disesuaikan dengan kolom Google Sheets Lpj_db (A-I).
     */
    protected $fillable = [
        'id_lpj', 'tgl_input', 'nama_proker', 'pemohon', 'realisasi_peserta', 
        'ketercapaian_tujuan', 'realisasi_sasaran', 'anggaran_sponsor', 
        'realisasi_anggaran', 'link_lpj_pdf', 'link_dokumentasi', 'link_evaluasi', 'is_checked'
    ];

    /**
     * Pengaturan tipe data kolom (Casting).
     * Sangat penting agar is_checked diperlakukan sebagai boolean.
     */
    protected $casts = [
        'is_checked' => 'boolean',
        'realisasi_anggaran' => 'integer',
        'realisasi_peserta' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}