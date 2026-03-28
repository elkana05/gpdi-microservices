<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    // GET /api/pengumuman — publik (hanya cakupan publik)
    public function indexPublik()
    {
        $data = Pengumuman::published()->publik()->orderByDesc('published_at')->get();
        return response()->json($data);
    }

    // GET /api/pengumuman/jemaat — jemaat & pendeta (publik + jemaat)
    public function indexJemaat(Request $request)
    {
        $data = Pengumuman::published()
            ->whereIn('cakupan', ['publik', 'jemaat'])
            ->orderByDesc('published_at')
            ->get();
        return response()->json($data);
    }

    // GET /api/pengumuman/rayon/{rayon_id} — jemaat rayon tertentu
    public function indexRayon($rayonId)
    {
        $data = Pengumuman::published()
            ->where(function ($q) use ($rayonId) {
                $q->whereIn('cakupan', ['publik', 'jemaat'])
                  ->orWhere(fn($q2) => $q2->where('cakupan', 'rayon')->where('rayon_id', $rayonId));
            })
            ->orderByDesc('published_at')
            ->get();
        return response()->json($data);
    }

    // GET /api/pengumuman/all — semua (pendeta/admin)
    public function indexAll()
    {
        return response()->json(Pengumuman::orderByDesc('created_at')->paginate(15));
    }

    // GET /api/pengumuman/{id}
    public function show($id)
    {
        return response()->json(Pengumuman::findOrFail($id));
    }

    // POST /api/pengumuman — pendeta / ketua rayon
    public function store(Request $request)
    {
        $request->validate([
            'judul'    => 'required|string|max:255',
            'isi'      => 'required|string',
            'cakupan'  => 'required|in:publik,jemaat,rayon',
            'rayon_id' => 'required_if:cakupan,rayon|nullable|integer',
            'status'   => 'sometimes|in:draft,published',
        ]);

        $data = $request->all();
        $data['dibuat_oleh'] = $request->header('X-User-Id', 0);
        if (($data['status'] ?? 'draft') === 'published') {
            $data['published_at'] = now();
        }

        $pengumuman = Pengumuman::create($data);
        return response()->json(['message' => 'Pengumuman berhasil dibuat', 'data' => $pengumuman], 201);
    }

    // PUT /api/pengumuman/{id}
    public function update(Request $request, $id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        $request->validate([
            'judul'   => 'sometimes|string|max:255',
            'isi'     => 'sometimes|string',
            'cakupan' => 'sometimes|in:publik,jemaat,rayon',
            'status'  => 'sometimes|in:draft,published,archived',
        ]);

        $data = $request->all();
        if (isset($data['status']) && $data['status'] === 'published' && !$pengumuman->published_at) {
            $data['published_at'] = now();
        }

        $pengumuman->update($data);
        return response()->json(['message' => 'Pengumuman berhasil diperbarui', 'data' => $pengumuman]);
    }

    // DELETE /api/pengumuman/{id}
    public function destroy($id)
    {
        Pengumuman::findOrFail($id)->delete();
        return response()->json(['message' => 'Pengumuman berhasil dihapus']);
    }
}
