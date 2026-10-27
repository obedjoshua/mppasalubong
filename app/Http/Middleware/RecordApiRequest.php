<?php

namespace App\Http\Middleware;

use App\Models\ApiRequestMetric;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class RecordApiRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $startedAt = microtime(true);

        try {
            $response = $next($request);
        } catch (Throwable $exception) {
            $status = match (true) {
                $exception instanceof HttpExceptionInterface => $exception->getStatusCode(),
                $exception instanceof ValidationException => 422,
                $exception instanceof AuthenticationException => 401,
                default => 500,
            };

            $this->record($request, $status, $startedAt);

            throw $exception;
        }

        $this->record($request, $response->getStatusCode(), $startedAt);

        return $response;
    }

    private function record(Request $request, int $status, float $startedAt): void
    {
        try {
            ApiRequestMetric::create([
                'method' => $request->method(),
                'path' => $request->route()?->uri() ?? $request->path(),
                'status' => $status,
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            ]);
        } catch (Throwable $exception) {
            // Metrics must not make an otherwise successful API request fail.
            report($exception);
        }
    }
}
