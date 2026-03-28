<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rayon extends Model
{
    use HasFactory;

    protected $table = 'm_rayon';

    protected $fillable = [
        'nama_rayon',
        'id_ketua_rayon',
        'keterangan',
    ];
}