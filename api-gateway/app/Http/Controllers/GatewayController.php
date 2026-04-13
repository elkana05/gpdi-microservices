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
        // Peta URL layanan berdasarkan parameter (Gunakan nama service Docker)
        $services = [
            'user'    => env('USER_SERVICE_URL', 'http://user-account-service:8001'),
            'content' => env('CONTENT_SERVICE_URL', 'http://content-publication-service:8002'),
            'event'   => env('EVENT_SERVICE_URL', 'http://event-rayon-service:8003'),
            'admin'   => env('ADMIN_SERVICE_URL', 'http://administration-utility-service:8004'),
        ];

        if (!array_key_exists($service, $services)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Layanan tujuan tidak valid atau tidak dikonfigurasi.'
            ], 500);
        }

        $baseUrl = rtrim($services[$service], '/');
        $path = $request->path(); // Mengambil path seperti "api/auth/login"

        // PEMBENTUKAN URL YANG TEPAT SASARAN
        // Karena microservice menggunakan Laravel api.php, rute mereka biasanya diawali /api/
        // Kita pastikan URL yang ditembak adalah http://service:port/api/...
        if (!str_starts_with($path, 'api/')) {
            $url = $baseUrl . '/api/' . $path;
        } else {
            $url = $baseUrl . '/' . $path;
        }

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
                // DETEKSI FILE (MULTIPART FORM DATA)
                if (count($request->allFiles()) > 0) {
                    $http = Http::withHeaders($headers);
                    foreach ($request->allFiles() as $name => $file) {
                        $http->attach(
                            $name,
                            file_get_contents($file->getPathname()),
                            $file->getClientOriginalName()
                        );
                    }
                    $dataTeks = $request->except(array_keys($request->allFiles()));
                    $response = $http->post($url, $dataTeks);
                } else {
                    // REQUEST BIASA (JSON)
                    $response = Http::withHeaders($headers)->send($method, $url, ['json' => $request->all()]);
                }
            }

            // Mengembalikan respons persis seperti yang diberikan oleh microservice
            return response($response->body(), $response->status())
                ->header('Content-Type', 'application/json');

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Layanan tujuan ('. strtoupper($service) .') tidak dapat dijangkau.',
                'debug_url' => $url, // Tambahkan debug URL agar tahu mana yang ditembak
                'error' => $e->getMessage()
            ], 503);
        }
    }
}
