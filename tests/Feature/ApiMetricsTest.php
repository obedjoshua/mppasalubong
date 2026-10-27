<?php

namespace Tests\Feature;

use App\Http\Middleware\RecordApiRequest;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ApiMetricsTest extends TestCase
{
    public function test_instrumented_request_records_route_status_and_duration(): void
    {
        $this->createMetricsTable();

        $request = Request::create('/api/vision/extract-colors', 'POST');
        $response = (new RecordApiRequest)->handle($request, fn () => response('accepted', 202));

        $this->assertSame(202, $response->getStatusCode());
        $this->assertDatabaseHas('api_request_metrics', [
            'method' => 'POST',
            'path' => 'api/vision/extract-colors',
            'status' => 202,
        ]);
    }

    public function test_instrumented_request_records_validation_failures(): void
    {
        $this->createMetricsTable();
        $request = Request::create('/api/example', 'POST');

        try {
            (new RecordApiRequest)->handle(
                $request,
                fn () => throw ValidationException::withMessages(['value' => 'invalid'])
            );
        } catch (ValidationException) {
            $this->assertDatabaseHas('api_request_metrics', [
                'method' => 'POST',
                'path' => 'api/example',
                'status' => 422,
            ]);

            return;
        }

        $this->fail('The downstream validation exception should be rethrown.');
    }

    private function createMetricsTable(): void
    {
        Schema::create('api_request_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('method', 10);
            $table->string('path');
            $table->unsignedSmallInteger('status');
            $table->unsignedInteger('duration_ms');
            $table->timestamps();
        });
    }
}
