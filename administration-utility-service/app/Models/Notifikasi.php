<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 't_notifikasi';
    protected $fillable = ['id_pengguna', 'judul', 'isi', 'is_read', 'jenis_referensi', 'id_referensi'];
    
    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }
}