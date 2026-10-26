<?php

use FoundryCo\LaravelCloudClient\CloudClient;
use FoundryCo\LaravelCloudClient\Data\ObjectStorageBucket;
use FoundryCo\LaravelCloudClient\Exceptions\AuthenticationException;
use FoundryCo\LaravelCloudClient\Http\GetRequest;
use FoundryCo\LaravelCloudClient\Http\JsonRequest;
use FoundryCo\LaravelCloudClient\Requests\CreateObjectStorageBucketRequest;
use Illuminate\Support\Collection;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

beforeEach(function () {
    $this->client = app(CloudClient::class);
});

function bucketPayload(string $id = 'bucket-1'): array
{
    return [
        'id' => $id,
        'type' => 'buckets',
        'attributes' => [
            'name' => 'my-bucket',
            'type' => 'cloudflare_r2',
            'status' => 'available',
            'visibility' => 'private',
            'jurisdiction' => 'default',
        ],
        'relationships' => [],
    ];
}

it('lists buckets', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make([
            'data' => [bucketPayload()],
            'links' => ['next' => null],
        ], 200),
    ]);
    $this->client->withMockClient($mockClient);

    $result = $this->client->objectStorageBuckets()->list();

    expect($result)->toBeInstanceOf(Collection::class)
        ->and($result->first())->toBeInstanceOf(ObjectStorageBucket::class);
});

it('gets a bucket', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make([
            'data' => bucketPayload(),
        ], 200),
    ]);
    $this->client->withMockClient($mockClient);

    $result = $this->client->objectStorageBuckets()->get('bucket-1');

    expect($result)->toBeInstanceOf(ObjectStorageBucket::class)
        ->and($result->id)->toBe('bucket-1');
});

it('creates a bucket', function () {
    $mockClient = new MockClient([
        JsonRequest::class => MockResponse::make([
            'data' => bucketPayload('bucket-new'),
        ], 201),
    ]);
    $this->client->withMockClient($mockClient);

    $request = new CreateObjectStorageBucketRequest(
        name: 'my-bucket',
        region: 'us-east-1',
    );

    $result = $this->client->objectStorageBuckets()->create($request);

    expect($result)->toBeInstanceOf(ObjectStorageBucket::class);

    $mockClient->assertSent(function ($request) {
        return $request->getMethod()->value === 'POST';
    });
});

it('deletes a bucket', function () {
    $mockClient = new MockClient([
        JsonRequest::class => MockResponse::make([], 204),
    ]);
    $this->client->withMockClient($mockClient);

    $this->client->objectStorageBuckets()->delete('bucket-1');

    $mockClient->assertSent(function ($request) {
        return $request->getMethod()->value === 'DELETE';
    });
});

it('throws AuthenticationException on 401', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make(['message' => 'Unauthenticated.'], 401),
    ]);
    $this->client->withMockClient($mockClient);

    $this->client->objectStorageBuckets()->list();
})->throws(AuthenticationException::class);
