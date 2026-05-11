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
        // 1. Buat Data Role (Disinkronkan dengan Frontend)
        $roles = ['admin', 'pendeta', 'jemaat', 'ketua_rayon'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // 2. Buat Akun Admin Utama
        $admin = User::firstOrCreate(
            ['email' => 'admin@gpdi.com'],
            [
                'id' => Str::uuid(),
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );
        $admin->roles()->sync([Role::where('name', 'admin')->first()->id]);
        Profile::updateOrCreate(['user_id' => $admin->id], ['full_name' => 'Administrator Utama']);

        // 3. Buat User Dummy: PENDETA
        $pendeta = User::firstOrCreate(
            ['email' => 'pendeta@gpdi.com'],
            [
                'id' => Str::uuid(),
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );
        $pendeta->roles()->sync([Role::where('name', 'pendeta')->first()->id]);
        Profile::updateOrCreate(['user_id' => $pendeta->id], ['full_name' => 'Pdt. Samuel']);
    }
}
