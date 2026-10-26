<?php

use FoundryCo\LaravelCloudClient\CloudClient;
use FoundryCo\LaravelCloudClient\Facades\Cloud;
use FoundryCo\LaravelCloudClient\Http\GetRequest;
use FoundryCo\LaravelCloudClient\Resources\ApplicationsResource;
use FoundryCo\LaravelCloudClient\Resources\BackgroundProcessesResource;
use FoundryCo\LaravelCloudClient\Resources\BucketKeysResource;
use FoundryCo\LaravelCloudClient\Resources\CachesResource;
use FoundryCo\LaravelCloudClient\Resources\CommandsResource;
use FoundryCo\LaravelCloudClient\Resources\DatabaseClustersResource;
use FoundryCo\LaravelCloudClient\Resources\DatabaseSnapshotsResource;
use FoundryCo\LaravelCloudClient\Resources\DedicatedClustersResource;
use FoundryCo\LaravelCloudClient\Resources\DeploymentsResource;
use FoundryCo\LaravelCloudClient\Resources\DomainsResource;
use FoundryCo\LaravelCloudClient\Resources\EnvironmentsResource;
use FoundryCo\LaravelCloudClient\Resources\InstancesResource;
use FoundryCo\LaravelCloudClient\Resources\MetaResource;
use FoundryCo\LaravelCloudClient\Resources\ObjectStorageBucketsResource;
use FoundryCo\LaravelCloudClient\Resources\UsageResource;
use FoundryCo\LaravelCloudClient\Resources\WebSocketApplicationsResource;
use FoundryCo\LaravelCloudClient\Resources\WebSocketClustersResource;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

it('resolves from the container', function () {
    expect(app(CloudClient::class))->toBeInstanceOf(CloudClient::class);
});

it('resolves via the cloud alias', function () {
    expect(app('cloud'))->toBeInstanceOf(CloudClient::class);
});

it('resolves via the facade', function () {
    expect(Cloud::getFacadeRoot())->toBeInstanceOf(CloudClient::class);
});

it('returns an ApplicationsResource', function () {
    expect(app(CloudClient::class)->applications())->toBeInstanceOf(ApplicationsResource::class);
});

it('returns an EnvironmentsResource', function () {
    expect(app(CloudClient::class)->environments())->toBeInstanceOf(EnvironmentsResource::class);
});

it('returns a DeploymentsResource', function () {
    expect(app(CloudClient::class)->deployments())->toBeInstanceOf(DeploymentsResource::class);
});

it('returns a DomainsResource', function () {
    expect(app(CloudClient::class)->domains())->toBeInstanceOf(DomainsResource::class);
});

it('returns an InstancesResource', function () {
    expect(app(CloudClient::class)->instances())->toBeInstanceOf(InstancesResource::class);
});

it('returns a BackgroundProcessesResource', function () {
    expect(app(CloudClient::class)->backgroundProcesses())->toBeInstanceOf(BackgroundProcessesResource::class);
});

it('returns a CommandsResource', function () {
    expect(app(CloudClient::class)->commands())->toBeInstanceOf(CommandsResource::class);
});

it('returns a DatabaseClustersResource', function () {
    expect(app(CloudClient::class)->databaseClusters())->toBeInstanceOf(DatabaseClustersResource::class);
});

it('returns a DatabaseSnapshotsResource', function () {
    expect(app(CloudClient::class)->databaseSnapshots())->toBeInstanceOf(DatabaseSnapshotsResource::class);
});

it('returns a CachesResource', function () {
    expect(app(CloudClient::class)->caches())->toBeInstanceOf(CachesResource::class);
});

it('returns an ObjectStorageBucketsResource', function () {
    expect(app(CloudClient::class)->objectStorageBuckets())->toBeInstanceOf(ObjectStorageBucketsResource::class);
});

it('returns a BucketKeysResource', function () {
    expect(app(CloudClient::class)->bucketKeys())->toBeInstanceOf(BucketKeysResource::class);
});

it('returns a WebSocketClustersResource', function () {
    expect(app(CloudClient::class)->websocketClusters())->toBeInstanceOf(WebSocketClustersResource::class);
});

it('returns a WebSocketApplicationsResource', function () {
    expect(app(CloudClient::class)->websocketApplications())->toBeInstanceOf(WebSocketApplicationsResource::class);
});

it('returns a DedicatedClustersResource', function () {
    expect(app(CloudClient::class)->dedicatedClusters())->toBeInstanceOf(DedicatedClustersResource::class);
});

it('returns a MetaResource', function () {
    expect(app(CloudClient::class)->meta())->toBeInstanceOf(MetaResource::class);
});

it('returns a UsageResource', function () {
    expect(app(CloudClient::class)->usage())->toBeInstanceOf(UsageResource::class);
});

it('sends bearer token in requests', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make(['data' => [], 'links' => ['next' => null]], 200),
    ]);

    $client = app(CloudClient::class);
    $client->withMockClient($mockClient);

    $client->applications()->list();

    $mockClient->assertSent(function ($request, $response) {
        $headers = $response->getPendingRequest()->headers()->all();

        return isset($headers['Authorization']) && $headers['Authorization'] === 'Bearer test-token';
    });
});

it('sends accept header for json api', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make(['data' => [], 'links' => ['next' => null]], 200),
    ]);

    $client = app(CloudClient::class);
    $client->withMockClient($mockClient);

    $client->applications()->list();

    $mockClient->assertSent(function ($request, $response) {
        $headers = $response->getPendingRequest()->headers()->all();

        return isset($headers['Accept']) && $headers['Accept'] === 'application/vnd.api+json';
    });
});
