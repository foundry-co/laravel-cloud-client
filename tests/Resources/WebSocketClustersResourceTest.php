<?php

use FoundryCo\LaravelCloudClient\CloudClient;
use FoundryCo\LaravelCloudClient\Data\WebsocketCluster;
use FoundryCo\LaravelCloudClient\Exceptions\AuthenticationException;
use FoundryCo\LaravelCloudClient\Http\GetRequest;
use FoundryCo\LaravelCloudClient\Http\JsonRequest;
use FoundryCo\LaravelCloudClient\Requests\CreateWebSocketClusterRequest;
use Illuminate\Support\Collection;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

beforeEach(function () {
    $this->client = app(CloudClient::class);
});

function wsClusterPayload(string $id = 'ws-1'): array
{
    return [
        'id' => $id,
        'type' => 'websocket-servers',
        'attributes' => [
            'name' => 'my-ws',
            'type' => 'reverb',
            'region' => 'us-east-1',
            'status' => 'available',
            'max_connections' => 100,
            'connection_distribution_strategy' => 'evenly',
            'hostname' => 'ws.example.com',
        ],
        'relationships' => [],
    ];
}

it('lists websocket clusters', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make([
            'data' => [wsClusterPayload()],
            'links' => ['next' => null],
        ], 200),
    ]);
    $this->client->withMockClient($mockClient);

    $result = $this->client->websocketClusters()->list();

    expect($result)->toBeInstanceOf(Collection::class)
        ->and($result->first())->toBeInstanceOf(WebsocketCluster::class);
});

it('gets a websocket cluster', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make([
            'data' => wsClusterPayload(),
        ], 200),
    ]);
    $this->client->withMockClient($mockClient);

    $result = $this->client->websocketClusters()->get('ws-1');

    expect($result)->toBeInstanceOf(WebsocketCluster::class)
        ->and($result->id)->toBe('ws-1');
});

it('creates a websocket cluster', function () {
    $mockClient = new MockClient([
        JsonRequest::class => MockResponse::make([
            'data' => wsClusterPayload('ws-new'),
        ], 201),
    ]);
    $this->client->withMockClient($mockClient);

    $request = new CreateWebSocketClusterRequest(
        name: 'my-ws',
        region: 'us-east-1',
    );

    $result = $this->client->websocketClusters()->create($request);

    expect($result)->toBeInstanceOf(WebsocketCluster::class);

    $mockClient->assertSent(function ($request) {
        return $request->getMethod()->value === 'POST';
    });
});

it('deletes a websocket cluster', function () {
    $mockClient = new MockClient([
        JsonRequest::class => MockResponse::make([], 204),
    ]);
    $this->client->withMockClient($mockClient);

    $this->client->websocketClusters()->delete('ws-1');

    $mockClient->assertSent(function ($request) {
        return $request->getMethod()->value === 'DELETE';
    });
});

it('throws AuthenticationException on 401', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make(['message' => 'Unauthenticated.'], 401),
    ]);
    $this->client->withMockClient($mockClient);

    $this->client->websocketClusters()->list();
})->throws(AuthenticationException::class);
