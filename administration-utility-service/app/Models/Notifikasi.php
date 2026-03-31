<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 't_notifikasi';
    protected $fillable = [
        'id_pengguna', 'judul', 'isi', 'is_read', 
        'jenis_referensi', 'id_referensi'
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];
}