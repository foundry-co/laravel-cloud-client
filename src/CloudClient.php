<?php

namespace Foundry\LaravelCloudClient;

use Foundry\LaravelCloudClient\Resources\ApplicationsResource;
use Foundry\LaravelCloudClient\Resources\BackgroundProcessesResource;
use Foundry\LaravelCloudClient\Resources\BucketKeysResource;
use Foundry\LaravelCloudClient\Resources\CachesResource;
use Foundry\LaravelCloudClient\Resources\CommandsResource;
use Foundry\LaravelCloudClient\Resources\DatabaseClustersResource;
use Foundry\LaravelCloudClient\Resources\DatabaseRestoresResource;
use Foundry\LaravelCloudClient\Resources\DatabaseSnapshotsResource;
use Foundry\LaravelCloudClient\Resources\DatabasesResource;
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
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\HasPagination;
use Saloon\PaginationPlugin\PagedPaginator;
use Saloon\PaginationPlugin\Paginator;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;

class CloudClient extends Connector implements HasPagination
{
    use AlwaysThrowOnErrors;

    public function __construct(
        #[\SensitiveParameter]
        protected readonly string $token,
    ) {}

    public function resolveBaseUrl(): string
    {
        return config('cloud.base_url', 'https://cloud.laravel.com/api');
    }

    protected function defaultAuth(): TokenAuthenticator
    {
        return new TokenAuthenticator($this->token);
    }

    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ];
    }

    public function paginate(Request $request): Paginator
    {
        return new class($this, $request) extends PagedPaginator
        {
            protected function isLastPage(Response $response): bool
            {
                return is_null($response->json('links.next'));
            }

            protected function getPageItems(Response $response, Request $request): array
            {
                return $response->json('data') ?? [];
            }
        };
    }

    public function applications(): ApplicationsResource
    {
        return new ApplicationsResource($this);
    }

    public function environments(): EnvironmentsResource
    {
        return new EnvironmentsResource($this);
    }

    public function deployments(): DeploymentsResource
    {
        return new DeploymentsResource($this);
    }

    public function domains(): DomainsResource
    {
        return new DomainsResource($this);
    }

    public function instances(): InstancesResource
    {
        return new InstancesResource($this);
    }

    public function commands(): CommandsResource
    {
        return new CommandsResource($this);
    }

    public function backgroundProcesses(): BackgroundProcessesResource
    {
        return new BackgroundProcessesResource($this);
    }

    public function databaseClusters(): DatabaseClustersResource
    {
        return new DatabaseClustersResource($this);
    }

    public function databases(): DatabasesResource
    {
        return new DatabasesResource($this);
    }

    public function databaseSnapshots(): DatabaseSnapshotsResource
    {
        return new DatabaseSnapshotsResource($this);
    }

    public function databaseRestores(): DatabaseRestoresResource
    {
        return new DatabaseRestoresResource($this);
    }

    public function objectStorageBuckets(): ObjectStorageBucketsResource
    {
        return new ObjectStorageBucketsResource($this);
    }

    public function bucketKeys(): BucketKeysResource
    {
        return new BucketKeysResource($this);
    }

    public function caches(): CachesResource
    {
        return new CachesResource($this);
    }

    public function websocketClusters(): WebSocketClustersResource
    {
        return new WebSocketClustersResource($this);
    }

    public function websocketApplications(): WebSocketApplicationsResource
    {
        return new WebSocketApplicationsResource($this);
    }

    public function meta(): MetaResource
    {
        return new MetaResource($this);
    }

    public function dedicatedClusters(): DedicatedClustersResource
    {
        return new DedicatedClustersResource($this);
    }

    public function usage(): UsageResource
    {
        return new UsageResource($this);
    }
}
