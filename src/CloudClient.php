<?php

namespace FoundryCo\LaravelCloudClient;

use FoundryCo\LaravelCloudClient\Resources\ApplicationsResource;
use FoundryCo\LaravelCloudClient\Resources\BackgroundProcessesResource;
use FoundryCo\LaravelCloudClient\Resources\BucketKeysResource;
use FoundryCo\LaravelCloudClient\Resources\CachesResource;
use FoundryCo\LaravelCloudClient\Resources\CommandsResource;
use FoundryCo\LaravelCloudClient\Resources\DatabaseClustersResource;
use FoundryCo\LaravelCloudClient\Resources\DatabaseSnapshotsResource;
use FoundryCo\LaravelCloudClient\Resources\DatabasesResource;
use FoundryCo\LaravelCloudClient\Resources\DedicatedClustersResource;
use FoundryCo\LaravelCloudClient\Resources\DeploymentsResource;
use FoundryCo\LaravelCloudClient\Resources\DomainsResource;
use FoundryCo\LaravelCloudClient\Resources\EdgeNetworksResource;
use FoundryCo\LaravelCloudClient\Resources\EnvironmentsResource;
use FoundryCo\LaravelCloudClient\Resources\InstancesResource;
use FoundryCo\LaravelCloudClient\Resources\MetaResource;
use FoundryCo\LaravelCloudClient\Resources\ObjectStorageBucketsResource;
use FoundryCo\LaravelCloudClient\Resources\ResendDomainsResource;
use FoundryCo\LaravelCloudClient\Resources\ScheduledCommandsResource;
use FoundryCo\LaravelCloudClient\Resources\SecretsResource;
use FoundryCo\LaravelCloudClient\Resources\UsageResource;
use FoundryCo\LaravelCloudClient\Resources\WebSocketApplicationsResource;
use FoundryCo\LaravelCloudClient\Resources\WebSocketClustersResource;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class CloudClient
{
    public function __construct(
        #[\SensitiveParameter]
        protected readonly string $token,
        protected readonly string $baseUrl = 'https://cloud.laravel.com/api',
    ) {}

    public function get(string $path, array $query = []): array
    {
        return $this->respond($this->request()->get($path, $query));
    }

    public function post(string $path, array $data = []): array
    {
        return $this->respond($this->request()->post($path, $data));
    }

    public function put(string $path, array $data = []): array
    {
        return $this->respond($this->request()->put($path, $data));
    }

    public function patch(string $path, array $data = []): array
    {
        return $this->respond($this->request()->patch($path, $data));
    }

    public function delete(string $path, array $query = []): array
    {
        return $this->respond($this->request()->withQueryParameters($query)->delete($path));
    }

    public function upload(string $path, string $field, string $file): array
    {
        $request = $this->pending()->attach($field, fopen($file, 'r'), basename($file));

        return $this->respond($request->post($path));
    }

    public function paginate(string $path, array $query = []): Collection
    {
        $items = collect();
        $page = 1;

        do {
            $body = $this->get($path, $query + ['page' => $page]);

            foreach ($body['data'] ?? [] as $item) {
                $items->push(['data' => $item, 'included' => $body['included'] ?? []]);
            }
        } while (($body['links']['next'] ?? null) !== null && $page++);

        return $items;
    }

    protected function pending(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->withToken($this->token)
            ->accept('application/vnd.api+json');
    }

    protected function request(): PendingRequest
    {
        return $this->pending()->asJson();
    }

    protected function respond(Response $response): array
    {
        if ($response->failed()) {
            throw $this->exception($response);
        }

        return $response->json() ?? [];
    }

    protected function exception(Response $response): \Throwable
    {
        $message = $response->json('message') ?? "Request failed with status {$response->status()}";

        return match ($response->status()) {
            401 => new Exceptions\AuthenticationException($message),
            404 => new Exceptions\NotFoundException($message),
            422 => new Exceptions\ValidationException($message, $response->json('errors') ?? []),
            default => new Exceptions\CloudApiException($message),
        };
    }

    public function applications(): ApplicationsResource
    {
        return new ApplicationsResource($this);
    }

    public function environments(?string $applicationId = null): EnvironmentsResource
    {
        return new EnvironmentsResource($this, $applicationId);
    }

    public function deployments(?string $environmentId = null): DeploymentsResource
    {
        return new DeploymentsResource($this, $environmentId);
    }

    public function domains(?string $environmentId = null): DomainsResource
    {
        return new DomainsResource($this, $environmentId);
    }

    public function instances(?string $environmentId = null): InstancesResource
    {
        return new InstancesResource($this, $environmentId);
    }

    public function commands(?string $environmentId = null): CommandsResource
    {
        return new CommandsResource($this, $environmentId);
    }

    public function backgroundProcesses(?string $instanceId = null): BackgroundProcessesResource
    {
        return new BackgroundProcessesResource($this, $instanceId);
    }

    public function databaseClusters(): DatabaseClustersResource
    {
        return new DatabaseClustersResource($this);
    }

    public function databases(?string $clusterId = null): DatabasesResource
    {
        return new DatabasesResource($this, $clusterId);
    }

    public function databaseSnapshots(?string $clusterId = null): DatabaseSnapshotsResource
    {
        return new DatabaseSnapshotsResource($this, $clusterId);
    }

    public function objectStorageBuckets(): ObjectStorageBucketsResource
    {
        return new ObjectStorageBucketsResource($this);
    }

    public function bucketKeys(?string $bucketId = null): BucketKeysResource
    {
        return new BucketKeysResource($this, $bucketId);
    }

    public function caches(): CachesResource
    {
        return new CachesResource($this);
    }

    public function websocketClusters(): WebSocketClustersResource
    {
        return new WebSocketClustersResource($this);
    }

    public function websocketApplications(?string $clusterId = null): WebSocketApplicationsResource
    {
        return new WebSocketApplicationsResource($this, $clusterId);
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

    public function scheduledCommands(?string $instanceId = null): ScheduledCommandsResource
    {
        return new ScheduledCommandsResource($this, $instanceId);
    }

    public function secrets(): SecretsResource
    {
        return new SecretsResource($this);
    }

    public function edgeNetworks(): EdgeNetworksResource
    {
        return new EdgeNetworksResource($this);
    }

    public function resendDomains(): ResendDomainsResource
    {
        return new ResendDomainsResource($this);
    }
}
