<?php

use Foundry\LaravelCloudClient\CloudClient;
use Foundry\LaravelCloudClient\Facades\Cloud;
use Foundry\LaravelCloudClient\Http\GetRequest;
use Foundry\LaravelCloudClient\Resources\ApplicationsResource;
use Foundry\LaravelCloudClient\Resources\BackgroundProcessesResource;
use Foundry\LaravelCloudClient\Resources\BucketKeysResource;
use Foundry\LaravelCloudClient\Resources\CachesResource;
use Foundry\LaravelCloudClient\Resources\CommandsResource;
use Foundry\LaravelCloudClient\Resources\DatabaseClustersResource;
use Foundry\LaravelCloudClient\Resources\DatabaseSnapshotsResource;
use Foundry\LaravelCloudClient\Resources\DedicatedClustersResource;
use Foundry\LaravelCloudClient\Resources\DeploymentsResource;
use Foundry\LaravelCloudClient\Resources\DomainsResource;
use Foundry\LaravelCloudClient\Resources\EnvironmentsResource;
use Foundry\LaravelCloudClient\Resources\InstancesResource;
use Foundry\LaravelCloudClient\Resources\MetaResource;
use Foundry\LaravelCloudClient\Resources\ObjectStorageBucketsResource;
use Foundry\LaravelCloudClient\Resources\UsageResource;
use Foundry\LaravelCloudClient\Resources\WebSocketApplicationsResource;
use Foundry\LaravelCloudClient\Resources\WebSocketClustersResource;
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
