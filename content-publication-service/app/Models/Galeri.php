<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Galeri extends Model
{
    use HasFactory;

    protected $table = 't_galeri';
    // Tambahkan 'kategori' ke dalam array fillable
    protected $fillable = ['judul', 'kategori', 'deskripsi', 'tanggal_kegiatan', 'path_foto', 'id_pengunggah'];

    protected function casts(): array
    {
        return [
            'tanggal_kegiatan' => 'date',
        ];
    }
}