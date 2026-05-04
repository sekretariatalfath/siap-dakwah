<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    protected $fillable = [
        'proposal_id', 'kategori', 'pemohon', 'nama_proker', 'bentuk_kegiatan', 
        'tempat', 'anggaran', 'target_peserta', 'jumlah_panitia', 
        'link_pdf', 'cp_nama', 'cp_wa', 'cp_email', 'cp_line', 'is_checked'
    ];

    protected $casts = [
        'is_checked' => 'boolean',
        'anggaran' => 'integer',
    ];
}