<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rayon;
use App\Models\WorshipSchedule;
use App\Models\ActivitySchedule;
use App\Models\RayonSchedule;

class EventController extends Controller
{
    // ==========================================
    // MODULE: MANAJEMEN RAYON
    // ==========================================
    public function getAllRayon()
    {
        $rayons = Rayon::orderBy('nama_rayon', 'asc')->get();
        return response()->json(['status' => 'success', 'data' => $rayons]);
    }

    public function storeRayon(Request $request)
    {
        $request->validate([
            'nama_rayon' => 'required|string|max:100',
            'keterangan' => 'nullable|string'
        ]);

        $rayon = Rayon::create([
            'nama_rayon' => $request->nama_rayon,
            'keterangan' => $request->keterangan,
            'id_ketua_rayon' => null 
        ]);

        return response()->json(['status' => 'success', 'data' => $rayon], 201);
    }

    public function updateRayon(Request $request, $id)
    {
        $rayon = Rayon::find($id);
        if (!$rayon) return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);

        $request->validate([
            'nama_rayon' => 'required|string|max:100',
            'keterangan' => 'nullable|string'
        ]);

        $rayon->update($request->only(['nama_rayon', 'keterangan']));
        return response()->json(['status' => 'success', 'data' => $rayon]);
    }

    public function deleteRayon($id)
    {
        $rayon = Rayon::find($id);
        if (!$rayon) return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        
        $rayon->delete();
        return response()->json(['status' => 'success', 'message' => 'Rayon dihapus']);
    }

    // ==========================================
    // MODULE: JADWAL IBADAH (Admin Panel)
    // ==========================================
    public function getAllWorship()
    {
        $schedules = WorshipSchedule::orderBy('day_of_week', 'asc')->orderBy('start_time', 'asc')->get();
        return response()->json(['status' => 'success', 'data' => $schedules]);
    }

    public function storeWorship(Request $request)
    {
        // PERBAIKAN 1: Sesuaikan validasi dengan 5 kategori persis seperti di React
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|in:Ibadah Raya Minggu,Ibadah Sekolah Minggu,Ibadah Pemuda & Remaja,Ibadah Wanita (Pelwap),Doa Malam Jemaat',
            'day_of_week' => 'required|string|in:Minggu,Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'location' => 'required|string|max:255',
            'event_date' => 'nullable|date',
            'start_time' => 'required',
            'status_publish' => 'required|string'
        ]);

        $schedule = WorshipSchedule::create($request->all());
        return response()->json(['status' => 'success', 'data' => $schedule], 201);
    }

    public function updateWorship(Request $request, $id)
    {
        $schedule = WorshipSchedule::find($id);
        if (!$schedule) return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);

        // PERBAIKAN 1: Sesuaikan validasi
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|in:Ibadah Raya Minggu,Ibadah Sekolah Minggu,Ibadah Pemuda & Remaja,Ibadah Wanita (Pelwap),Doa Malam Jemaat',
            'day_of_week' => 'required|string|in:Minggu,Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'location' => 'required|string|max:255',
            'event_date' => 'nullable|date',
            'start_time' => 'required',
            'status_publish' => 'required|string'
        ]);

        $schedule->update($request->all());
        return response()->json(['status' => 'success', 'data' => $schedule]);
    }

    public function deleteWorship($id)
    {
        $schedule = WorshipSchedule::find($id);
        if (!$schedule) return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        
        $schedule->delete();
        return response()->json(['status' => 'success', 'message' => 'Jadwal dihapus']);
    }

    // ==========================================
    // MODULE: JADWAL KEGIATAN
    // ==========================================
    public function getAllActivity() {
        return response()->json(['status' => 'success', 'data' => ActivitySchedule::orderBy('event_date', 'desc')->get()]);
    }
    
    public function storeActivity(Request $request) {
        try {
            $data = $request->except(['_method']);
            
            // Logika Dekode Gambar Base64
            if ($request->filled('gambar') && preg_match('/^data:image\/(\w+);base64,/', $request->gambar, $type)) {
                $data_without_prefix = substr($request->gambar, strpos($request->gambar, ',') + 1);
                $image_base64 = base64_decode($data_without_prefix);
                $extension = strtolower($type[1]); // mendapatkan jpg/png
                $fileName = 'kegiatan/' . uniqid() . '.' . $extension;
                
                \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $image_base64);
                $data['gambar'] = $fileName;
            } else {
                unset($data['gambar']); // Jangan simpan null jika tidak ada file
            }
            
            $activity = ActivitySchedule::create($data);
            return response()->json(['status' => 'success', 'data' => $activity], 201);
            
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
        }
    }
    
    public function updateActivity(Request $request, $id) {
        try {
            $activity = ActivitySchedule::find($id);
            if (!$activity) return response()->json(['status' => 'error', 'message' => 'Tidak ditemukan'], 404);
            
            $data = $request->except(['_method']);
            
            if ($request->filled('gambar') && preg_match('/^data:image\/(\w+);base64,/', $request->gambar, $type)) {
                $data_without_prefix = substr($request->gambar, strpos($request->gambar, ',') + 1);
                $image_base64 = base64_decode($data_without_prefix);
                $extension = strtolower($type[1]);
                $fileName = 'kegiatan/' . uniqid() . '.' . $extension;
                
                \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $image_base64);
                $data['gambar'] = $fileName;
            } else {
                unset($data['gambar']); // Biarkan gambar lama jika admin tidak mengubahnya
            }
            
            $activity->update($data);
            return response()->json(['status' => 'success', 'data' => $activity]);
            
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal update: ' . $e->getMessage()], 500);
        }
    }
    
    public function deleteActivity($id) {
        ActivitySchedule::destroy($id);
        return response()->json(['status' => 'success']);
    }
}