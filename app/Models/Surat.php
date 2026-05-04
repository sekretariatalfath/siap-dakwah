<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    // KITA BUANG SUSHI. 
    // Sekarang Model ini baca langsung dari database SQLite di laptop kamu.

    protected $fillable = [
    'no_surat_full', 'jenis', 'lingkup', 'detail', 'nama_acara', 
    'fakultas', 'bidang_fakultas', 'asal_pengisi', 'nama_pengisi', 
    'tanggal', 'nomor_urut', 'bulan_romawi', 'tahun', 
    'perihal', 'penyelenggara', 'catatan', 'link_drive', 'batch_id'
    ];

    protected $casts = [
        'tgl_surat'  => 'date',
        'nomor_urut' => 'integer',
        'tahun'      => 'integer'
    ];
}