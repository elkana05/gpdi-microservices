<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    // Tambahkan HasUuids agar ID terisi otomatis dengan string UUID saat create data
    use HasFactory, Notifiable, HasUuids;

    // Matikan auto-increment karena kita menggunakan UUID
    public $incrementing = false;
    
    // Set tipe primary key menjadi string
    protected $keyType = 'string';

    protected $fillable = ['email', 'password', 'is_active'];
    protected $hidden = ['password'];

    public function getJWTIdentifier() {
        return $this->getKey();
    }
    
    public function getJWTCustomClaims() {
        // Mengambil nama role (jika tidak ada, default ke 'public')
        $roleName = $this->roles()->first() ? $this->roles()->first()->name : 'public';
        
        // Mengambil nama lengkap dari profil
        $profileName = $this->profile ? $this->profile->full_name : 'User';

        return [
            'role' => $roleName,
            'name' => $profileName,
        ];
    }
    
    // Relasi
    public function roles() {
        return $this->belongsToMany(Role::class, 'user_roles');
    }
    
    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id');
    }
    
    public function familyMembers()
    {
        return $this->hasMany(FamilyMember::class, 'user_id');
    }
}