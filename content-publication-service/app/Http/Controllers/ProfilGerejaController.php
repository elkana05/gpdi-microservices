<?php

namespace App\Http\Controllers;

use App\Models\ProfilGereja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilGerejaController extends Controller
{
    // GET /api/profil-gereja — publik
    public function index()
    {
        $profil = ProfilGereja::first();
        if (!$profil) {
            return response()->json(['message' => 'Data profil belum tersedia'], 404);
        }
        return response()->json($profil);
    }

    // PUT /api/profil-gereja — hanya pendeta
    public function update(Request $request)
    {
        $request->validate([
            'nama_gereja'    => 'required|string|max:255',
            'alamat'         => 'required|string',
            'sejarah'        => 'nullable|string',
            'visi'           => 'nullable|string',
            'misi'           => 'nullable|string',
            'pengakuan_iman' => 'nullable|string',
            'no_telepon'     => 'nullable|string|max:20',
            'email'          => 'nullable|email',
            'maps_url'       => 'nullable|url',
            'foto'           => 'nullable|image|max:2048',
        ]);

        $profil = ProfilGereja::firstOrNew([]);
        $data = $request->except('foto');

        if ($request->hasFile('foto')) {
            if ($profil->foto) Storage::delete('public/' . $profil->foto);
            $data['foto'] = $request->file('foto')->store('profil', 'public');
        }

        $profil->fill($data)->save();
        return response()->json(['message' => 'Profil gereja berhasil diperbarui', 'data' => $profil]);
    }
}
