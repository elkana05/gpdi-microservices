<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilGereja extends Model
{
    use HasFactory;

    protected $table = 'm_profil_gereja';

    protected $fillable = [
        'nama_gereja',
        'ayat_tahunan',
        'sejarah',
        'visi_misi',
        'pengakuan_iman',
        'struktur_pelayanan',
        'alamat',
        'nomor_kontak',
        'link_maps',
        'link_sosmed',
        'foto_pendeta',
        'banner_beranda',
        'id_pengubah'
    ];
}