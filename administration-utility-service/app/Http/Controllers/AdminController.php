<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PilihanSurat;

class AdminController extends Controller
{
    // ==========================================
    // MODULE: LAYANAN SURAT
    // ==========================================
    public function getAllSurat() {
        return response()->json(['status' => 'success', 'data' => PilihanSurat::orderBy('created_at', 'desc')->get()]);
    }

    public function storeSurat(Request $request) {
        $request->validate([
            'nama_surat' => 'required|string|max:255',
            'whatsapp_url' => 'required|string',
            'is_active' => 'required|boolean'
        ]);
        $surat = PilihanSurat::create($request->all());
        return response()->json(['status' => 'success', 'data' => $surat], 201);
    }

    public function updateSurat(Request $request, $id) {
        $surat = PilihanSurat::find($id);
        if (!$surat) return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        
        $request->validate([
            'nama_surat' => 'required|string|max:255',
            'whatsapp_url' => 'required|string',
            'is_active' => 'required|boolean'
        ]);
        $surat->update($request->all());
        return response()->json(['status' => 'success', 'data' => $surat]);
    }

    public function deleteSurat($id) {
        PilihanSurat::destroy($id);
        return response()->json(['status' => 'success']);
    }
}