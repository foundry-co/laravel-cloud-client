<?php

namespace FoundryCo\LaravelCloudClient;

use FoundryCo\LaravelCloudClient\Resources\ApplicationsResource;
use FoundryCo\LaravelCloudClient\Resources\BackgroundProcessesResource;
use FoundryCo\LaravelCloudClient\Resources\BucketKeysResource;
use FoundryCo\LaravelCloudClient\Resources\CachesResource;
use FoundryCo\LaravelCloudClient\Resources\CommandsResource;
use FoundryCo\LaravelCloudClient\Resources\DatabaseClustersResource;
use FoundryCo\LaravelCloudClient\Resources\DatabaseRestoresResource;
use FoundryCo\LaravelCloudClient\Resources\DatabaseSnapshotsResource;
use FoundryCo\LaravelCloudClient\Resources\DatabasesResource;
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
