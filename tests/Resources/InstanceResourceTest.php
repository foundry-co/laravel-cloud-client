<?php

use Foundry\LaravelCloudClient\CloudClient;
use Foundry\LaravelCloudClient\Data\EnvironmentInstance;
use Foundry\LaravelCloudClient\Enums\InstanceScalingType;
use Foundry\LaravelCloudClient\Enums\InstanceType;
use Foundry\LaravelCloudClient\Exceptions\AuthenticationException;
use Foundry\LaravelCloudClient\Exceptions\NotFoundException;
use Foundry\LaravelCloudClient\Http\GetRequest;
use Foundry\LaravelCloudClient\Http\JsonRequest;
use Foundry\LaravelCloudClient\Requests\CreateInstanceRequest;
use Illuminate\Support\Collection;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

beforeEach(function () {
    $this->client = app(CloudClient::class);
});

function instancePayload(string $id = 'inst-1'): array
{
    return [
        'id' => $id,
        'type' => 'instances',
        'attributes' => [
            'name' => 'web',
            'type' => 'service',
            'size' => 'small-1x',
            'scaling_type' => 'none',
            'min_replicas' => 1,
            'max_replicas' => 1,
            'uses_scheduler' => false,
        ],
        'relationships' => [],
    ];
}

it('lists instances for an environment', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make([
            'data' => [instancePayload()],
            'links' => ['next' => null],
        ], 200),
    ]);
    $this->client->withMockClient($mockClient);

    $result = $this->client->instances()->list('env-1');

    expect($result)->toBeInstanceOf(Collection::class)
        ->and($result)->toHaveCount(1)
        ->and($result->first())->toBeInstanceOf(EnvironmentInstance::class);
});

it('gets a single instance', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make([
            'data' => instancePayload(),
        ], 200),
    ]);
    $this->client->withMockClient($mockClient);

    $result = $this->client->instances()->get('inst-1');

    expect($result)->toBeInstanceOf(EnvironmentInstance::class)
        ->and($result->id)->toBe('inst-1');
});

it('creates an instance', function () {
    $mockClient = new MockClient([
        JsonRequest::class => MockResponse::make([
            'data' => instancePayload('inst-new'),
        ], 201),
    ]);
    $this->client->withMockClient($mockClient);

    $request = new CreateInstanceRequest(
        environmentId: 'env-1',
        name: 'web',
        type: InstanceType::Service,
        size: 'small-1x',
        scalingType: InstanceScalingType::None,
        minReplicas: 1,
        maxReplicas: 1,
    );

    $result = $this->client->instances()->create($request);

    expect($result)->toBeInstanceOf(EnvironmentInstance::class);

    $mockClient->assertSent(function ($request) {
        return $request->getMethod()->value === 'POST';
    });
});

it('deletes an instance', function () {
    $mockClient = new MockClient([
        JsonRequest::class => MockResponse::make([], 204),
    ]);
    $this->client->withMockClient($mockClient);

    $this->client->instances()->delete('inst-1');

    $mockClient->assertSent(function ($request) {
        return $request->getMethod()->value === 'DELETE';
    });
});

it('throws AuthenticationException on 401', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make(['message' => 'Unauthenticated.'], 401),
    ]);
    $this->client->withMockClient($mockClient);

    $this->client->instances()->list('env-1');
})->throws(AuthenticationException::class);

it('throws NotFoundException on 404', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make(['message' => 'Not Found.'], 404),
    ]);
    $this->client->withMockClient($mockClient);

    $this->client->instances()->get('missing');
})->throws(NotFoundException::class);
