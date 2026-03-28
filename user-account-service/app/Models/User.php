<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    protected $fillable = ['email', 'password', 'is_active'];
    protected $hidden = ['password'];

    public function getJWTIdentifier() {
        return $this->getKey();
    }
    public function getJWTCustomClaims() {
        return [];
    }
    
    // Relasi
    public function roles() {
        return $this->belongsToMany(Role::class, 'user_roles');
    }
    public function profile() {
        return $this->hasOne(Profile::class);
    }
    public function familyMembers() {
        return $this->hasMany(FamilyMember::class);
    }
}
