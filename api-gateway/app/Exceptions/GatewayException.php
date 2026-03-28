<?php

namespace App\Exceptions;

use Exception;

class GatewayException extends Exception
{
    public static function serviceNotFound(string $path): self
    {
        return new self("No target service configured for path: {$path}", 404);
    }

    public static function downstreamUnavailable(string $serviceName): self
    {
        return new self("Target service is unavailable: {$serviceName}", 503);
    }

    public static function invalidDownstreamResponse(): self
    {
        return new self("Invalid response from downstream service", 502);
    }
}