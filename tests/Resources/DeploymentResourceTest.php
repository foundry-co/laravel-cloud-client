<?php

use FoundryCo\LaravelCloudClient\CloudClient;
use FoundryCo\LaravelCloudClient\Data\Deployment;
use FoundryCo\LaravelCloudClient\Exceptions\AuthenticationException;
use FoundryCo\LaravelCloudClient\Exceptions\NotFoundException;
use FoundryCo\LaravelCloudClient\Http\GetRequest;
use FoundryCo\LaravelCloudClient\Http\JsonRequest;
use FoundryCo\LaravelCloudClient\Requests\CreateDeploymentRequest;
use Illuminate\Support\Collection;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

beforeEach(function () {
    $this->client = app(CloudClient::class);
});

function deploymentPayload(string $id = 'dep-1', string $status = 'deployment.succeeded'): array
{
    return [
        'id' => $id,
        'type' => 'deployments',
        'attributes' => [
            'status' => $status,
            'branch_name' => 'main',
            'php_major_version' => '8.3',
        ],
        'relationships' => [],
    ];
}

it('lists deployments for an environment', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make([
            'data' => [deploymentPayload()],
            'links' => ['next' => null],
        ], 200),
    ]);
    $this->client->withMockClient($mockClient);

    $result = $this->client->deployments()->list('env-1');

    expect($result)->toBeInstanceOf(Collection::class)
        ->and($result)->toHaveCount(1)
        ->and($result->first())->toBeInstanceOf(Deployment::class);
});

it('gets a single deployment', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make([
            'data' => deploymentPayload(),
        ], 200),
    ]);
    $this->client->withMockClient($mockClient);

    $result = $this->client->deployments()->get('dep-1');

    expect($result)->toBeInstanceOf(Deployment::class)
        ->and($result->id)->toBe('dep-1');
});

it('initiates a deployment', function () {
    $mockClient = new MockClient([
        JsonRequest::class => MockResponse::make([
            'data' => deploymentPayload('dep-new', 'pending'),
        ], 201),
    ]);
    $this->client->withMockClient($mockClient);

    $request = new CreateDeploymentRequest(environmentId: 'env-1');

    $result = $this->client->deployments()->initiate($request);

    expect($result)->toBeInstanceOf(Deployment::class);

    $mockClient->assertSent(function ($request) {
        return $request->getMethod()->value === 'POST';
    });
});

it('marks deployment as succeeded', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make([
            'data' => deploymentPayload('dep-1', 'deployment.succeeded'),
        ], 200),
    ]);
    $this->client->withMockClient($mockClient);

    $result = $this->client->deployments()->get('dep-1');

    expect($result->succeeded())->toBeTrue()
        ->and($result->failed())->toBeFalse()
        ->and($result->isFinished())->toBeTrue();
});

it('marks deployment as failed', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make([
            'data' => deploymentPayload('dep-1', 'failed'),
        ], 200),
    ]);
    $this->client->withMockClient($mockClient);

    $result = $this->client->deployments()->get('dep-1');

    expect($result->failed())->toBeTrue()
        ->and($result->succeeded())->toBeFalse();
});

it('throws AuthenticationException on 401', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make(['message' => 'Unauthenticated.'], 401),
    ]);
    $this->client->withMockClient($mockClient);

    $this->client->deployments()->list('env-1');
})->throws(AuthenticationException::class);

it('throws NotFoundException on 404', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make(['message' => 'Not Found.'], 404),
    ]);
    $this->client->withMockClient($mockClient);

    $this->client->deployments()->get('missing');
})->throws(NotFoundException::class);
