<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PilihanSurat extends Model
{
    use HasFactory;
    
    protected $table = 'm_pilihan_surat';
    protected $fillable = ['nama_surat', 'whatsapp_url', 'is_active'];
    
    // Pastikan is_active diperlakukan sebagai boolean
    protected $casts = [
        'is_active' => 'boolean',
    ];
}