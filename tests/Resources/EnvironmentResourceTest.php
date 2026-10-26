<?php

use FoundryCo\LaravelCloudClient\CloudClient;
use FoundryCo\LaravelCloudClient\Data\Environment;
use FoundryCo\LaravelCloudClient\Exceptions\AuthenticationException;
use FoundryCo\LaravelCloudClient\Exceptions\NotFoundException;
use FoundryCo\LaravelCloudClient\Http\GetRequest;
use FoundryCo\LaravelCloudClient\Http\JsonRequest;
use FoundryCo\LaravelCloudClient\Requests\CreateEnvironmentRequest;
use FoundryCo\LaravelCloudClient\Requests\UpdateEnvironmentRequest;
use Illuminate\Support\Collection;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

beforeEach(function () {
    $this->client = app(CloudClient::class);
});

function environmentPayload(string $id = 'env-1', string $name = 'production'): array
{
    return [
        'id' => $id,
        'type' => 'environments',
        'attributes' => [
            'name' => $name,
            'slug' => $name,
            'status' => 'running',
            'vanity_domain' => 'app.laravel.cloud',
            'php_major_version' => '8.3',
        ],
        'relationships' => [],
    ];
}

it('lists environments for an application', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make([
            'data' => [environmentPayload()],
            'links' => ['next' => null],
        ], 200),
    ]);
    $this->client->withMockClient($mockClient);

    $result = $this->client->environments()->list('app-1');

    expect($result)->toBeInstanceOf(Collection::class)
        ->and($result)->toHaveCount(1)
        ->and($result->first())->toBeInstanceOf(Environment::class);
});

it('gets a single environment', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make([
            'data' => environmentPayload(),
        ], 200),
    ]);
    $this->client->withMockClient($mockClient);

    $result = $this->client->environments()->get('env-1');

    expect($result)->toBeInstanceOf(Environment::class)
        ->and($result->id)->toBe('env-1');
});

it('creates an environment', function () {
    $mockClient = new MockClient([
        JsonRequest::class => MockResponse::make([
            'data' => environmentPayload('env-new', 'staging'),
        ], 201),
    ]);
    $this->client->withMockClient($mockClient);

    $request = new CreateEnvironmentRequest(
        applicationId: 'app-1',
        name: 'staging',
        branch: 'main',
    );

    $result = $this->client->environments()->create($request);

    expect($result)->toBeInstanceOf(Environment::class)
        ->and($result->name)->toBe('staging');

    $mockClient->assertSent(function ($request) {
        return $request->getMethod()->value === 'POST';
    });
});

it('updates an environment', function () {
    $mockClient = new MockClient([
        JsonRequest::class => MockResponse::make([
            'data' => environmentPayload('env-1', 'production-updated'),
        ], 200),
    ]);
    $this->client->withMockClient($mockClient);

    $request = new UpdateEnvironmentRequest(environmentId: 'env-1', name: 'production-updated');

    $result = $this->client->environments()->update($request);

    expect($result)->toBeInstanceOf(Environment::class);

    $mockClient->assertSent(function ($request) {
        return $request->getMethod()->value === 'PATCH';
    });
});

it('deletes an environment', function () {
    $mockClient = new MockClient([
        JsonRequest::class => MockResponse::make([], 204),
    ]);
    $this->client->withMockClient($mockClient);

    $this->client->environments()->delete('env-1');

    $mockClient->assertSent(function ($request) {
        return $request->getMethod()->value === 'DELETE';
    });
});

it('throws AuthenticationException on 401', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make(['message' => 'Unauthenticated.'], 401),
    ]);
    $this->client->withMockClient($mockClient);

    $this->client->environments()->list('app-1');
})->throws(AuthenticationException::class);

it('throws NotFoundException on 404', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make(['message' => 'Not Found.'], 404),
    ]);
    $this->client->withMockClient($mockClient);

    $this->client->environments()->get('missing');
})->throws(NotFoundException::class);
