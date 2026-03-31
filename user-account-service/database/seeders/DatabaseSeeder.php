<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Profile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Data Role Sesuai Blueprint
        $roles = ['pendeta', 'jemaat_aktif', 'ketua_rayon'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // 2. Buat User Dummy: JEMAAT AKTIF (Bisa mengelola Family Members)
        $jemaat = User::create([
            'id' => Str::uuid(),
            'email' => 'jemaat@gpdi.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
        $jemaat->roles()->attach(Role::where('name', 'jemaat_aktif')->first()->id);
        
        Profile::create([
            'user_id' => $jemaat->id,
            'full_name' => 'Bapak Jemaat Test',
            'phone_number' => '081234567890',
            'address' => 'Jl. Gereja No. 1',
        ]);

        // 3. Buat User Dummy: PENDETA (Akses umum tinggi)
        $pendeta = User::create([
            'id' => Str::uuid(),
            'email' => 'pendeta@gpdi.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
        $pendeta->roles()->attach(Role::where('name', 'pendeta')->first()->id);
        
        Profile::create([
            'user_id' => $pendeta->id,
            'full_name' => 'Pdt. Samuel',
        ]);
    }
}