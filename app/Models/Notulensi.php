<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notulensi extends Model
{
    protected $fillable = [
    'unit_owner', 'judul_syuro', 'pimpinan_rapat', 'kategori', 
    'waktu_mulai', 'tempat', 'link_daftar_hadir', 'link_google_docs', 'google_drive_file_id'
];
}
