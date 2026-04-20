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
        $services = [
            'user'    => env('USER_SERVICE_URL', 'http://user-account-service:8001'),
            'content' => env('CONTENT_SERVICE_URL', 'http://content-publication-service:8002'),
            'event'   => env('EVENT_SERVICE_URL', 'http://event-rayon-service:8003'),
            'admin'   => env('ADMIN_SERVICE_URL', 'http://administration-utility-service:8004'),
        ];

        if (!array_key_exists($service, $services)) {
            return response()->json(['status' => 'error', 'message' => 'Layanan tidak ditemukan.'], 500);
        }

        $baseUrl = rtrim($services[$service], '/');
        $path = $request->path();

        // Pastikan URL selalu mengarah ke /api/ di microservice tujuan
        $apiUrl = str_replace('api/', '', $path);
        $url = $baseUrl . '/api/' . ltrim($apiUrl, '/');

        $method = $request->method();

        // Ambil token Bearer dari request asli
        $token = $request->bearerToken();

        // Siapkan HTTP Client dengan timeout agar tidak lelet saat service mati
        $http = Http::withHeaders(['Accept' => 'application/json'])->timeout(60);

        if ($token) {
            $http = $http->withToken($token);
        }

        try {
            if ($method === 'GET') {
                $response = $http->get($url, $request->query());
            } else {
                // Penanganan Multipart (jika ada file)
                if ($request->allFiles()) {
                    foreach ($request->allFiles() as $name => $file) {
                        $http->attach($name, fopen($file->getRealPath(), 'r'), $file->getClientOriginalName());
                    }
                    $response = $http->post($url, $request->except(array_keys($request->allFiles())));
                } else {
                    $response = $http->send($method, $url, ['json' => $request->all()]);
                }
            }

            return response($response->body(), $response->status())
                ->header('Content-Type', 'application/json');

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghubungi service ' . strtoupper($service),
                'error' => $e->getMessage()
            ], 503);
        }
    }
}
