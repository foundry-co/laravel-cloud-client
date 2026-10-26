<?php

use FoundryCo\LaravelCloudClient\CloudClient;
use FoundryCo\LaravelCloudClient\Data\Cache;
use FoundryCo\LaravelCloudClient\Exceptions\AuthenticationException;
use FoundryCo\LaravelCloudClient\Http\GetRequest;
use FoundryCo\LaravelCloudClient\Http\JsonRequest;
use FoundryCo\LaravelCloudClient\Requests\CreateCacheRequest;
use Illuminate\Support\Collection;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

beforeEach(function () {
    $this->client = app(CloudClient::class);
});

function cachePayload(string $id = 'cache-1'): array
{
    return [
        'id' => $id,
        'type' => 'caches',
        'attributes' => [
            'type' => 'redis',
            'name' => 'my-cache',
            'status' => 'available',
            'region' => 'us-east-1',
            'size' => 'small',
            'auto_upgrade_enabled' => true,
            'is_public' => false,
        ],
        'relationships' => [],
    ];
}

it('lists caches', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make([
            'data' => [cachePayload()],
            'links' => ['next' => null],
        ], 200),
    ]);
    $this->client->withMockClient($mockClient);

    $result = $this->client->caches()->list();

    expect($result)->toBeInstanceOf(Collection::class)
        ->and($result->first())->toBeInstanceOf(Cache::class);
});

it('gets a cache', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make([
            'data' => cachePayload(),
        ], 200),
    ]);
    $this->client->withMockClient($mockClient);

    $result = $this->client->caches()->get('cache-1');

    expect($result)->toBeInstanceOf(Cache::class)
        ->and($result->id)->toBe('cache-1');
});

it('creates a cache', function () {
    $mockClient = new MockClient([
        JsonRequest::class => MockResponse::make([
            'data' => cachePayload('cache-new'),
        ], 201),
    ]);
    $this->client->withMockClient($mockClient);

    $request = new CreateCacheRequest(
        type: 'redis',
        name: 'my-cache',
        region: 'us-east-1',
        size: 'small',
    );

    $result = $this->client->caches()->create($request);

    expect($result)->toBeInstanceOf(Cache::class);

    $mockClient->assertSent(function ($request) {
        return $request->getMethod()->value === 'POST';
    });
});

it('deletes a cache', function () {
    $mockClient = new MockClient([
        JsonRequest::class => MockResponse::make([], 204),
    ]);
    $this->client->withMockClient($mockClient);

    $this->client->caches()->delete('cache-1');

    $mockClient->assertSent(function ($request) {
        return $request->getMethod()->value === 'DELETE';
    });
});

it('throws AuthenticationException on 401', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make(['message' => 'Unauthenticated.'], 401),
    ]);
    $this->client->withMockClient($mockClient);

    $this->client->caches()->list();
})->throws(AuthenticationException::class);
