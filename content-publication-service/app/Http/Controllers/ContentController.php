<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Pengumuman;
use App\Models\Renungan;

class ContentController extends Controller
{
    /* =========================================================
       MANAJEMEN PENGUMUMAN
       ========================================================= */
    public function indexAnnouncement(Request $request)
    {
        // Semua user yang login (termasuk jemaat) bisa melihat pengumuman internal
        $pengumuman = Pengumuman::orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => 'success', 'message' => 'Announcements retrieved',
            'data' => $pengumuman, 'meta' => null
        ], 200);
    }

    public function storeAnnouncement(Request $request)
    {
        $user = $request->auth_user;
        if (!in_array($user['role'], ['pendeta', 'ketua_rayon'])) {
            return response()->json(['status' => 'error', 'message' => 'You do not have permission to access this resource'], 403);
        }

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'scope' => 'required|in:public,internal',
            'status' => 'required|in:draft,published',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['id_pembuat'] = $user['id'];
        $data['published_at'] = $data['status'] === 'published' ? now() : null;

        $pengumuman = Pengumuman::create($data);

        return response()->json([
            'status' => 'success', 'message' => 'Announcement created successfully',
            'data' => $pengumuman, 'meta' => null
        ], 201);
    }

    public function updateAnnouncement(Request $request, $id)
    {
        $user = $request->auth_user;
        $pengumuman = Pengumuman::find($id);

        if (!$pengumuman) return response()->json(['status' => 'error', 'message' => 'Resource not found'], 404);

        // Validasi Otoritas: Ketua Rayon hanya bisa edit miliknya sendiri, Pendeta bisa semua
        if ($user['role'] === 'ketua_rayon' && $pengumuman->id_pembuat !== $user['id']) {
            return response()->json(['status' => 'error', 'message' => 'You do not have permission to modify this resource'], 403);
        } elseif ($user['role'] !== 'pendeta' && $user['role'] !== 'ketua_rayon') {
            return response()->json(['status' => 'error', 'message' => 'Forbidden'], 403);
        }

        $pengumuman->update($request->all());

        return response()->json([
            'status' => 'success', 'message' => 'Announcement updated',
            'data' => $pengumuman, 'meta' => null
        ], 200);
    }

    public function destroyAnnouncement(Request $request, $id)
    {
        $user = $request->auth_user;
        $pengumuman = Pengumuman::find($id);

        if (!$pengumuman) return response()->json(['status' => 'error', 'message' => 'Resource not found'], 404);

        if ($user['role'] === 'ketua_rayon' && $pengumuman->id_pembuat !== $user['id']) {
            return response()->json(['status' => 'error', 'message' => 'Forbidden'], 403);
        } elseif ($user['role'] !== 'pendeta' && $user['role'] !== 'ketua_rayon') {
            return response()->json(['status' => 'error', 'message' => 'Forbidden'], 403);
        }

        $pengumuman->delete();

        return response()->json([
            'status' => 'success', 'message' => 'Announcement deleted',
            'data' => (object)[], 'meta' => null
        ], 200);
    }

    /* =========================================================
       MANAJEMEN RENUNGAN HARIAN (KHUSUS PENDETA)
       ========================================================= */
    public function indexDevotional()
    {
        $renungan = Renungan::orderBy('published_at', 'desc')->get();
        return response()->json([
            'status' => 'success', 'message' => 'Devotionals retrieved',
            'data' => $renungan, 'meta' => null
        ], 200);
    }

    public function storeDevotional(Request $request)
    {
        $user = $request->auth_user;
        if ($user['role'] !== 'pendeta') {
            return response()->json(['status' => 'error', 'message' => 'You do not have permission to access this resource'], 403);
        }

        $validator = Validator::make($request->all(), [
            'tema' => 'required|string|max:255',
            'ayat_pokok' => 'required|string|max:255',
            'isi' => 'required|string',
            'status' => 'required|in:draft,published',
        ]);

        if ($validator->fails()) return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);

        $data = $validator->validated();
        $data['id_penulis'] = $user['id'];
        $data['published_at'] = $data['status'] === 'published' ? now() : null;

        $renungan = Renungan::create($data);

        return response()->json([
            'status' => 'success', 'message' => 'Devotional created successfully',
            'data' => $renungan, 'meta' => null
        ], 201);
    }

    public function updateDevotional(Request $request, $id)
    {
        $user = $request->auth_user;
        if ($user['role'] !== 'pendeta') return response()->json(['status' => 'error', 'message' => 'Forbidden'], 403);

        $renungan = Renungan::find($id);
        if (!$renungan) return response()->json(['status' => 'error', 'message' => 'Resource not found'], 404);

        $renungan->update($request->all());

        return response()->json([
            'status' => 'success', 'message' => 'Devotional updated',
            'data' => $renungan, 'meta' => null
        ], 200);
    }

    public function destroyDevotional(Request $request, $id)
    {
        $user = $request->auth_user;
        if ($user['role'] !== 'pendeta') return response()->json(['status' => 'error', 'message' => 'Forbidden'], 403);

        $renungan = Renungan::find($id);
        if (!$renungan) return response()->json(['status' => 'error', 'message' => 'Resource not found'], 404);

        $renungan->delete();

        return response()->json([
            'status' => 'success', 'message' => 'Devotional deleted',
            'data' => (object)[], 'meta' => null
        ], 200);
    }
} 