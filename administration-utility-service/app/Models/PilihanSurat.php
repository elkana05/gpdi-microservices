<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PilihanSurat extends Model
{
    protected $table = 'm_pilihan_surat';
    protected $fillable = ['nama_surat', 'whatsapp_url', 'is_active'];
}