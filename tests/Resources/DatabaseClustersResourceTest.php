<?php

use Foundry\LaravelCloudClient\CloudClient;
use Foundry\LaravelCloudClient\Data\DatabaseCluster;
use Foundry\LaravelCloudClient\Exceptions\AuthenticationException;
use Foundry\LaravelCloudClient\Http\GetRequest;
use Foundry\LaravelCloudClient\Http\JsonRequest;
use Foundry\LaravelCloudClient\Requests\CreateDatabaseClusterRequest;
use Illuminate\Support\Collection;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

beforeEach(function () {
    $this->client = app(CloudClient::class);
});

function dbClusterPayload(string $id = 'db-1'): array
{
    return [
        'id' => $id,
        'type' => 'databases',
        'attributes' => [
            'name' => 'my-db',
            'type' => 'mysql',
            'status' => 'available',
            'region' => 'us-east-1',
            'config' => [],
            'connection' => [],
        ],
        'relationships' => [],
    ];
}

it('lists database clusters', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make([
            'data' => [dbClusterPayload()],
            'links' => ['next' => null],
        ], 200),
    ]);
    $this->client->withMockClient($mockClient);

    $result = $this->client->databaseClusters()->list();

    expect($result)->toBeInstanceOf(Collection::class)
        ->and($result->first())->toBeInstanceOf(DatabaseCluster::class);
});

it('gets a database cluster', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make([
            'data' => dbClusterPayload(),
        ], 200),
    ]);
    $this->client->withMockClient($mockClient);

    $result = $this->client->databaseClusters()->get('db-1');

    expect($result)->toBeInstanceOf(DatabaseCluster::class)
        ->and($result->id)->toBe('db-1');
});

it('creates a database cluster', function () {
    $mockClient = new MockClient([
        JsonRequest::class => MockResponse::make([
            'data' => dbClusterPayload('db-new'),
        ], 201),
    ]);
    $this->client->withMockClient($mockClient);

    $request = new CreateDatabaseClusterRequest(
        type: 'mysql',
        name: 'my-db',
        region: 'us-east-1',
        config: [],
    );

    $result = $this->client->databaseClusters()->create($request);

    expect($result)->toBeInstanceOf(DatabaseCluster::class);

    $mockClient->assertSent(function ($request) {
        return $request->getMethod()->value === 'POST';
    });
});

it('deletes a database cluster', function () {
    $mockClient = new MockClient([
        JsonRequest::class => MockResponse::make([], 204),
    ]);
    $this->client->withMockClient($mockClient);

    $this->client->databaseClusters()->delete('db-1');

    $mockClient->assertSent(function ($request) {
        return $request->getMethod()->value === 'DELETE';
    });
});

it('throws AuthenticationException on 401', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make(['message' => 'Unauthenticated.'], 401),
    ]);
    $this->client->withMockClient($mockClient);

    $this->client->databaseClusters()->list();
})->throws(AuthenticationException::class);
