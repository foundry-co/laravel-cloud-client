<?php

use FoundryCo\LaravelCloudClient\CloudClient;
use FoundryCo\LaravelCloudClient\Data\Application;
use FoundryCo\LaravelCloudClient\Exceptions\AuthenticationException;
use FoundryCo\LaravelCloudClient\Exceptions\NotFoundException;
use FoundryCo\LaravelCloudClient\Http\GetRequest;
use FoundryCo\LaravelCloudClient\Http\JsonRequest;
use FoundryCo\LaravelCloudClient\Requests\CreateApplicationRequest;
use FoundryCo\LaravelCloudClient\Requests\UpdateApplicationRequest;
use Illuminate\Support\Collection;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

beforeEach(function () {
    $this->client = app(CloudClient::class);
});

it('lists applications', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make([
            'data' => [
                [
                    'id' => '1',
                    'type' => 'applications',
                    'attributes' => ['name' => 'My App', 'slug' => 'my-app', 'region' => 'us-east-1'],
                    'relationships' => [],
                ],
            ],
            'links' => ['next' => null],
        ], 200),
    ]);
    $this->client->withMockClient($mockClient);

    $result = $this->client->applications()->list();

    expect($result)->toBeInstanceOf(Collection::class)
        ->and($result)->toHaveCount(1)
        ->and($result->first())->toBeInstanceOf(Application::class)
        ->and($result->first()->name)->toBe('My App');
});

it('gets an application', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make([
            'data' => [
                'id' => 'app-123',
                'type' => 'applications',
                'attributes' => ['name' => 'My App', 'slug' => 'my-app', 'region' => 'us-east-1'],
                'relationships' => [],
            ],
        ], 200),
    ]);
    $this->client->withMockClient($mockClient);

    $result = $this->client->applications()->get('app-123');

    expect($result)->toBeInstanceOf(Application::class)
        ->and($result->id)->toBe('app-123');
});

it('creates an application', function () {
    $mockClient = new MockClient([
        JsonRequest::class => MockResponse::make([
            'data' => [
                'id' => '1',
                'type' => 'applications',
                'attributes' => ['name' => 'New App', 'slug' => 'new-app', 'region' => 'us-east-1'],
                'relationships' => [],
            ],
        ], 201),
    ]);
    $this->client->withMockClient($mockClient);

    $request = new CreateApplicationRequest(
        repository: 'acme/app',
        name: 'New App',
        region: 'us-east-1',
    );

    $result = $this->client->applications()->create($request);

    expect($result)->toBeInstanceOf(Application::class)
        ->and($result->id)->toBe('1');

    $mockClient->assertSent(function ($request, $response) {
        return $request->getMethod()->value === 'POST';
    });
});

it('updates an application', function () {
    $mockClient = new MockClient([
        JsonRequest::class => MockResponse::make([
            'data' => [
                'id' => 'app-123',
                'type' => 'applications',
                'attributes' => ['name' => 'Updated App', 'slug' => 'updated-app', 'region' => 'us-east-1'],
                'relationships' => [],
            ],
        ], 200),
    ]);
    $this->client->withMockClient($mockClient);

    $request = new UpdateApplicationRequest(applicationId: 'app-123', name: 'Updated App');

    $result = $this->client->applications()->update($request);

    expect($result)->toBeInstanceOf(Application::class)
        ->and($result->id)->toBe('app-123');

    $mockClient->assertSent(function ($request) {
        return $request->getMethod()->value === 'PATCH';
    });
});

it('deletes an application', function () {
    $mockClient = new MockClient([
        JsonRequest::class => MockResponse::make([], 204),
    ]);
    $this->client->withMockClient($mockClient);

    $this->client->applications()->delete('app-123');

    $mockClient->assertSent(function ($request) {
        return $request->getMethod()->value === 'DELETE';
    });
});

it('throws AuthenticationException on 401', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make(['message' => 'Unauthenticated.'], 401),
    ]);
    $this->client->withMockClient($mockClient);

    $this->client->applications()->list();
})->throws(AuthenticationException::class);

it('throws NotFoundException on 404', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make(['message' => 'Not Found.'], 404),
    ]);
    $this->client->withMockClient($mockClient);

    $this->client->applications()->get('missing');
})->throws(NotFoundException::class);

it('auto-paginates applications', function () {
    $mockClient = new MockClient([
        MockResponse::make([
            'data' => [
                ['id' => '1', 'type' => 'applications', 'attributes' => ['name' => 'App 1', 'slug' => 'app-1', 'region' => 'us-east-1'], 'relationships' => []],
            ],
            'links' => ['next' => 'https://cloud.laravel.com/api/applications?page=2'],
        ], 200),
        MockResponse::make([
            'data' => [
                ['id' => '2', 'type' => 'applications', 'attributes' => ['name' => 'App 2', 'slug' => 'app-2', 'region' => 'us-east-1'], 'relationships' => []],
            ],
            'links' => ['next' => null],
        ], 200),
    ]);
    $this->client->withMockClient($mockClient);

    $result = $this->client->applications()->list();

    expect($result)->toHaveCount(2);
});
