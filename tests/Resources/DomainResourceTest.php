<?php

use FoundryCo\LaravelCloudClient\CloudClient;
use FoundryCo\LaravelCloudClient\Data\Domain;
use FoundryCo\LaravelCloudClient\Exceptions\AuthenticationException;
use FoundryCo\LaravelCloudClient\Exceptions\NotFoundException;
use FoundryCo\LaravelCloudClient\Http\GetRequest;
use FoundryCo\LaravelCloudClient\Http\JsonRequest;
use FoundryCo\LaravelCloudClient\Requests\CreateDomainRequest;
use FoundryCo\LaravelCloudClient\Requests\UpdateDomainRequest;
use Illuminate\Support\Collection;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

beforeEach(function () {
    $this->client = app(CloudClient::class);
});

function domainPayload(string $id = 'dom-1', string $name = 'example.com', string $type = 'root'): array
{
    return [
        'id' => $id,
        'type' => 'domains',
        'attributes' => [
            'name' => $name,
            'type' => $type,
            'hostname_status' => 'active',
            'ssl_status' => 'active',
            'origin_status' => 'active',
        ],
        'relationships' => [],
    ];
}

it('lists domains for an environment', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make([
            'data' => [domainPayload()],
            'links' => ['next' => null],
        ], 200),
    ]);
    $this->client->withMockClient($mockClient);

    $result = $this->client->domains()->list('env-1');

    expect($result)->toBeInstanceOf(Collection::class)
        ->and($result)->toHaveCount(1)
        ->and($result->first())->toBeInstanceOf(Domain::class);
});

it('gets a single domain', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make([
            'data' => domainPayload(),
        ], 200),
    ]);
    $this->client->withMockClient($mockClient);

    $result = $this->client->domains()->get('dom-1');

    expect($result)->toBeInstanceOf(Domain::class)
        ->and($result->id)->toBe('dom-1');
});

it('creates a domain', function () {
    $mockClient = new MockClient([
        JsonRequest::class => MockResponse::make([
            'data' => domainPayload('dom-new', 'new.example.com'),
        ], 201),
    ]);
    $this->client->withMockClient($mockClient);

    $request = new CreateDomainRequest(environmentId: 'env-1', name: 'new.example.com');

    $result = $this->client->domains()->create($request);

    expect($result)->toBeInstanceOf(Domain::class)
        ->and($result->name)->toBe('new.example.com');

    $mockClient->assertSent(function ($request) {
        return $request->getMethod()->value === 'POST';
    });
});

it('updates a domain', function () {
    $mockClient = new MockClient([
        JsonRequest::class => MockResponse::make([
            'data' => domainPayload('dom-1', 'updated.example.com'),
        ], 200),
    ]);
    $this->client->withMockClient($mockClient);

    $request = new UpdateDomainRequest(domainId: 'dom-1', name: 'updated.example.com');

    $result = $this->client->domains()->update($request);

    expect($result)->toBeInstanceOf(Domain::class);

    $mockClient->assertSent(function ($request) {
        return $request->getMethod()->value === 'PATCH';
    });
});

it('verifies a domain', function () {
    $mockClient = new MockClient([
        JsonRequest::class => MockResponse::make([
            'data' => domainPayload(),
        ], 200),
    ]);
    $this->client->withMockClient($mockClient);

    $result = $this->client->domains()->verify('dom-1');

    expect($result)->toBeInstanceOf(Domain::class);

    $mockClient->assertSent(function ($request) {
        return $request->getMethod()->value === 'POST';
    });
});

it('deletes a domain', function () {
    $mockClient = new MockClient([
        JsonRequest::class => MockResponse::make([], 204),
    ]);
    $this->client->withMockClient($mockClient);

    $this->client->domains()->delete('dom-1');

    $mockClient->assertSent(function ($request) {
        return $request->getMethod()->value === 'DELETE';
    });
});

it('isPrimary returns true for root type', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make([
            'data' => domainPayload('dom-1', 'example.com', 'root'),
        ], 200),
    ]);
    $this->client->withMockClient($mockClient);

    $result = $this->client->domains()->get('dom-1');

    expect($result->isPrimary())->toBeTrue();
});

it('throws AuthenticationException on 401', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make(['message' => 'Unauthenticated.'], 401),
    ]);
    $this->client->withMockClient($mockClient);

    $this->client->domains()->list('env-1');
})->throws(AuthenticationException::class);

it('throws NotFoundException on 404', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make(['message' => 'Not Found.'], 404),
    ]);
    $this->client->withMockClient($mockClient);

    $this->client->domains()->get('missing');
})->throws(NotFoundException::class);
