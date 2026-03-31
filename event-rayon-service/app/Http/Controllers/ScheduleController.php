<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; 
use App\Models\WorshipSchedule;
use App\Models\ActivitySchedule;
use App\Models\RayonSchedule;

class ScheduleController extends Controller
{
    /* =========================================================
       ENDPOINT PUBLIK (Tanpa Middleware)
       Digunakan oleh JadwalPage.jsx di Frontend
       ========================================================= */
    public function getPublicWorshipSchedules()
    {
        $schedules = WorshipSchedule::where('status_publish', 'published')
            ->orderBy('day_of_week', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();
            
        return response()->json(['status' => 'success', 'data' => $schedules], 200);
    }

    public function getPublicActivitySchedules()
    {
        $schedules = ActivitySchedule::where('status_publish', 'published')
            ->orderBy('event_date', 'asc')
            ->get();
            
        return response()->json(['status' => 'success', 'data' => $schedules], 200);
    }

    /* =========================================================
       ENDPOINT KHUSUS JEMAAT
       ========================================================= */
    public function getJadwalRayonJemaat(Request $request)
    {
        $user = $request->auth_user;
        $rayonId = $user['id_rayon'] ?? null;

        if (!$rayonId) {
            return response()->json([
                'status' => 'success',
                'pesan_debug' => 'Token tidak memiliki rayon_id',
                'data' => [
                    'rayon' => null, 'jadwalAktif' => null, 'riwayat' => [], 'notifikasi' => []
                ]
            ], 200);
        }

        $today = now()->toDateString();

        // 1. Tarik Jadwal Aktif
        $jadwalAktifRaw = RayonSchedule::where('rayon_id', $rayonId)
            ->whereDate('event_date', '>=', $today)
            ->orderBy('event_date', 'asc')
            ->first(); 

        // 2. Tarik Riwayat 
        $riwayatRaw = RayonSchedule::where('rayon_id', $rayonId)
            ->whereDate('event_date', '<', $today)
            ->orderBy('event_date', 'desc')
            ->get();

        // 3. Format Jadwal
        $jadwalAktif = $jadwalAktifRaw ? [
            'id' => $jadwalAktifRaw->id,
            'tanggal_ibadah' => \Carbon\Carbon::parse($jadwalAktifRaw->event_date)->translatedFormat('l, d F Y'),
            'waktu' => substr($jadwalAktifRaw->start_time, 0, 5) . ($jadwalAktifRaw->end_time ? ' - ' . substr($jadwalAktifRaw->end_time, 0, 5) : '') . ' WIB',
            'lokasi' => $jadwalAktifRaw->location,
            'pelayan_firman' => $jadwalAktifRaw->title,
            'penanggung_jawab' => $jadwalAktifRaw->created_by_name ?? '-',
            'status' => 'Berjalan Sesuai Jadwal'
        ] : null;

        $riwayat = $riwayatRaw->map(function($item) {
            return [
                'id' => $item->id,
                'tanggal_ibadah' => \Carbon\Carbon::parse($item->event_date)->translatedFormat('l, d F Y'),
                'lokasi' => $item->location,
                'pelayan_firman' => $item->title,
                'status' => 'Selesai'
            ];
        });

        // 4. Tarik Informasi Rayon 
        $rayonRaw = \Illuminate\Support\Facades\DB::table('m_rayon')->where('id', $rayonId)->first();
        if (!$rayonRaw) {
             $rayonRaw = \Illuminate\Support\Facades\DB::table('rayons')->where('id', $rayonId)->first();
        }

        if (!$rayonRaw) {
            return response()->json([
                'status' => 'success',
                'pesan_debug' => 'Data rayon tidak ditemukan di database.',
                'data' => [
                    'rayon' => null, 'jadwalAktif' => null, 'riwayat' => [], 'notifikasi' => []
                ]
            ], 200);
        }

        // 5. Susun Objek Rayon
        $rayon = [
            'id' => $rayonRaw->id,
            'nama_rayon' => $rayonRaw->nama_rayon ?? $rayonRaw->name ?? '-',
            'keterangan' => $rayonRaw->keterangan ?? $rayonRaw->description ?? '-', 
            'ketua_rayon' => 'Silakan hubungi Admin' 
        ];

        return response()->json([
            'status' => 'success',
            'pesan_debug' => 'Berhasil meload jadwal dengan format yang benar!',
            'data' => [
                'rayon' => $rayon,
                'jadwalAktif' => $jadwalAktif,
                'riwayat' => $riwayat,
                'notifikasi' => [] 
            ]
        ], 200);
    }

    /* =========================================================
       ENDPOINT KHUSUS KETUA RAYON (Manajemen Ibadah Page)
       ========================================================= */
    public function getJadwalByKetuaRayon(Request $request)
    {
        $user = $request->auth_user;
        $rayonId = $user['id_rayon'] ?? null;

        if (!$rayonId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akun Anda belum memiliki Rayon ID. Silakan hubungi Admin.'
            ], 400);
        }

        // Mengambil semua jadwal untuk rayon milik Ketua Rayon yang sedang login
        $schedules = RayonSchedule::where('rayon_id', $rayonId)
            ->orderBy('event_date', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $schedules
        ], 200);
    }


    /* =========================================================
       ENDPOINT PRIVAT: IBADAH RAYA (Hanya Tampilan / Read-Only)
       ========================================================= */
    public function getWorshipSchedules(Request $request)
    {
        $schedules = WorshipSchedule::all();
        return response()->json(['status' => 'success', 'message' => 'Worship schedules retrieved', 'data' => $schedules, 'meta' => null], 200);
    }

    public function getWorshipScheduleById(Request $request, $id)
    {
        $schedule = WorshipSchedule::find($id);
        if (!$schedule) return response()->json(['status' => 'error', 'message' => 'Resource not found'], 404);
        
        return response()->json(['status' => 'success', 'message' => 'Worship schedule retrieved', 'data' => $schedule, 'meta' => null], 200);
    }

    /* =========================================================
       ENDPOINT PRIVAT: JADWAL RAYON (Hak Kelola Ketua Rayon)
       ========================================================= */
    public function getRayonSchedules(Request $request)
    {
        $schedules = RayonSchedule::all();
        return response()->json(['status' => 'success', 'message' => 'Rayon schedules retrieved', 'data' => $schedules, 'meta' => null], 200);
    }

    public function getRayonScheduleById(Request $request, $id)
    {
        $schedule = RayonSchedule::find($id);
        if (!$schedule) return response()->json(['status' => 'error', 'message' => 'Resource not found'], 404);
        
        return response()->json(['status' => 'success', 'message' => 'Rayon schedule retrieved', 'data' => $schedule, 'meta' => null], 200);
    }

    public function storeRayonSchedule(Request $request)
    {
        $user = $request->auth_user;
        
        if (!in_array($user['role'], ['ketua_rayon', 'pendeta', 'admin'])) {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak. Anda tidak memiliki izin.'], 403);
        }

        // PERBAIKAN: Ubah input end_time menjadi null jika kosong agar lolos validasi date_format
        $data = $request->all();
        if (empty($data['end_time'])) {
            $data['end_time'] = null;
        }

        $validator = Validator::make($data, [
            'rayon_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'event_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'nullable',
            // Tambahkan param dari frontend agar tidak ditolak
            'category' => 'nullable|string',
            'day_of_week' => 'nullable|string',
            'status_publish' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            $validatedData = $validator->validated();
            
            // Jaga-jaga jika nama_lengkap tidak tersedia, gunakan name atau default
            $validatedData['created_by_user_id'] = $user['id'];
            $validatedData['created_by_name'] = $user['nama_lengkap'] ?? $user['name'] ?? 'Ketua Rayon';

            $schedule = RayonSchedule::create($validatedData);

            return response()->json(['status' => 'success', 'message' => 'Rayon schedule created successfully', 'data' => $schedule, 'meta' => null], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }

    public function updateRayonSchedule(Request $request, $id)
    {
        $user = $request->auth_user;
        
        if (!in_array($user['role'], ['ketua_rayon', 'pendeta', 'admin'])) {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak. Anda tidak memiliki izin.'], 403);
        }

        $schedule = RayonSchedule::find($id);
        if (!$schedule) {
            return response()->json(['status' => 'error', 'message' => 'Resource not found'], 404);
        }

        // PERBAIKAN: Ubah input end_time menjadi null jika kosong
        $data = $request->all();
        if (empty($data['end_time'])) {
            $data['end_time'] = null;
        }

        $validator = Validator::make($data, [
            'rayon_id' => 'sometimes|required|integer',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'sometimes|required|string|max:255',
            'event_date' => 'sometimes|required|date',
            'start_time' => 'sometimes|required',
            'end_time' => 'nullable',
            // Tambahkan param dari frontend
            'category' => 'nullable|string',
            'day_of_week' => 'nullable|string',
            'status_publish' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            $schedule->update($validator->validated());
            return response()->json(['status' => 'success', 'message' => 'Rayon schedule updated successfully', 'data' => $schedule, 'meta' => null], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal memperbarui data: ' . $e->getMessage()], 500);
        }
    }

    public function destroyRayonSchedule(Request $request, $id)
    {
        $user = $request->auth_user;
        
        if (!in_array($user['role'], ['ketua_rayon', 'pendeta', 'admin'])) {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak. Anda tidak memiliki izin.'], 403);
        }

        $schedule = RayonSchedule::find($id);
        if (!$schedule) {
            return response()->json(['status' => 'error', 'message' => 'Resource not found'], 404);
        }

        try {
            $schedule->delete();
            return response()->json(['status' => 'success', 'message' => 'Rayon schedule deleted successfully', 'data' => (object)[], 'meta' => null], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data: ' . $e->getMessage()], 500);
        }
    }
}