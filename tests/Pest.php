<?php

use FoundryCo\LaravelCloudClient\Tests\TestCase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

uses(TestCase::class)->in('.');

beforeEach(function () {
    Http::fake();
    Http::preventStrayRequests();
});

function payload(string $id, array $attributes = [], array $relationships = []): array
{
    return ['id' => $id, 'type' => 'resources', 'attributes' => $attributes + ['name' => 'name'], 'relationships' => $relationships];
}

function fakeCloud(array $routes): void
{
    Http::fake(collect($routes)->mapWithKeys(fn ($response, $url) => [
        'cloud.laravel.com/api'.$url => is_array($response) && isset($response['__status'])
            ? Http::response($response['body'], $response['__status'])
            : Http::response($response),
    ])->all());
}

function assertCloudSent(string $method, string $path, ?callable $assert = null): void
{
    Http::assertSent(fn (Request $request) => $request->method() === $method
        && parse_url($request->url(), PHP_URL_PATH) === '/api'.$path
        && ($assert === null || $assert($request)));
}
