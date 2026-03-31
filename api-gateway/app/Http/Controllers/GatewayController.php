<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GatewayController extends Controller
{
    /**
     * Meneruskan request ke microservice yang ditentukan.
     */
    public function forwardRequest(Request $request, $service)
    {
        // Peta URL layanan berdasarkan parameter
        $services = [
            'user'    => env('USER_SERVICE_URL', 'http://127.0.0.1:8001'),
            'content' => env('CONTENT_SERVICE_URL', 'http://127.0.0.1:8002'),
            'event'   => env('EVENT_SERVICE_URL', 'http://127.0.0.1:8003'),
            'admin'   => env('ADMIN_SERVICE_URL', 'http://127.0.0.1:8004'),
        ];

        if (!array_key_exists($service, $services)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Layanan tujuan tidak valid atau tidak dikonfigurasi.'
            ], 500);
        }

        $baseUrl = rtrim($services[$service], '/');
        $url = $baseUrl . '/' . $request->path();
        $method = $request->method();

        // Meneruskan Headers (khususnya Authorization Bearer Token)
        $headers = ['Accept' => 'application/json'];
        if ($request->hasHeader('Authorization')) {
            $headers['Authorization'] = $request->header('Authorization');
        }

        try {
            // Meneruskan request beserta payload
            if ($method === 'GET') {
                $response = Http::withHeaders($headers)->send($method, $url, ['query' => $request->query()]);
            } else {
                // --- LOGIKA BARU: DETEKSI FILE (MULTIPART FORM DATA) ---
                if (count($request->allFiles()) > 0) {
                    $http = Http::withHeaders($headers);
                    
                    // 1. Lampirkan (Attach) semua file fisik yang diterima
                    foreach ($request->allFiles() as $name => $file) {
                        $http->attach(
                            $name,
                            file_get_contents($file->getPathname()),
                            $file->getClientOriginalName()
                        );
                    }
                    
                    // 2. Teruskan beserta data teks lainnya (judul, deskripsi, dll)
                    $dataTeks = $request->except(array_keys($request->allFiles()));
                    $response = $http->post($url, $dataTeks);
                    
                } else {
                    // --- LOGIKA LAMA: REQUEST BIASA (JSON) ---
                    $response = Http::withHeaders($headers)->send($method, $url, ['json' => $request->all()]);
                }
            }

            // Mengembalikan respons persis seperti yang diberikan oleh microservice
            return response($response->body(), $response->status())
                ->header('Content-Type', 'application/json');

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Layanan tujuan ('. strtoupper($service) .') tidak dapat dijangkau atau sedang down.',
                'error' => $e->getMessage()
            ], 503);
        }
    }
}