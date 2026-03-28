<?php

namespace App\Services;

use App\Exceptions\GatewayException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

class ProxyService
{
    public function forward(Request $request, string $targetBaseUrl): array
    {
        $path = ltrim($request->path(), '/');
        $targetUrl = rtrim($targetBaseUrl, '/') . '/' . $path;

        $timeout = config('services.gateway.timeout', 10);
        $connectTimeout = config('services.gateway.connect_timeout', 3);

        $headers = $this->buildForwardHeaders($request);

        try {
            $http = Http::acceptJson()
                ->timeout($timeout)
                ->connectTimeout($connectTimeout)
                ->withHeaders($headers);

            $response = $http->send($request->method(), $targetUrl, [
                'query' => $request->query(),
                'json' => $request->all(),
            ]);

            $body = $response->json();

            if (!is_array($body)) {
                throw GatewayException::invalidDownstreamResponse();
            }

            return [
                'status' => $response->status(),
                'body' => $body,
            ];
        } catch (GatewayException $e) {
            throw $e;
        } catch (Throwable $e) {
            $serviceName = ServiceRegistry::serviceName($path) ?? 'unknown_service';
            throw GatewayException::downstreamUnavailable($serviceName);
        }
    }

    protected function buildForwardHeaders(Request $request): array
    {
        $headers = [
            'Accept' => 'application/json',
        ];

        if ($request->header('Authorization')) {
            $headers['Authorization'] = $request->header('Authorization');
        }

        if ($request->attributes->get('gateway_user_id')) {
            $headers['X-User-Id'] = (string) $request->attributes->get('gateway_user_id');
        }

        if ($request->attributes->get('gateway_user_role')) {
            $headers['X-User-Role'] = (string) $request->attributes->get('gateway_user_role');
        }

        if ($request->attributes->get('gateway_user_email')) {
            $headers['X-User-Email'] = (string) $request->attributes->get('gateway_user_email');
        }

        if ($request->attributes->get('gateway_user_name')) {
            $headers['X-User-Name'] = (string) $request->attributes->get('gateway_user_name');
        }

        return $headers;
    }
}