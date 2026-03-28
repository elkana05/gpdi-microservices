<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelayanan extends Model
{
    use HasFactory;

    protected $table = 'm_pelayanan';

    protected $fillable = [
        'nama_pelayanan',
        'deskripsi',
        'gambar',
        'id_pembuat'
    ];
}