<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model {
    protected $fillable = ['user_id', 'full_name', 'relationship', 'gender', 'birth_date'];
}
