<?php

namespace Foundry\LaravelCloudClient\Resources;

use Foundry\LaravelCloudClient\Data\DatabaseCluster;
use Foundry\LaravelCloudClient\Http\GetRequest;
use Foundry\LaravelCloudClient\Http\JsonRequest;
use Foundry\LaravelCloudClient\Requests\CreateDatabaseClusterRequest;
use Foundry\LaravelCloudClient\Requests\UpdateDatabaseClusterRequest;
use Illuminate\Support\Collection;
use Saloon\Enums\Method;

class DatabaseClustersResource extends Resource
{
    public function list(?string $type = null, ?string $region = null, ?string $status = null): Collection
    {
        return $this->paginateAll(
            new GetRequest('/databases', array_filter([
                'filter[type]' => $type,
                'filter[region]' => $region,
                'filter[status]' => $status,
            ], fn ($v) => $v !== null)),
            fn ($item) => DatabaseCluster::createFromResponse($item)
        );
    }

    public function get(string $clusterId): DatabaseCluster
    {
        $response = $this->send(new GetRequest("/databases/{$clusterId}"));

        return DatabaseCluster::createFromResponse($response->json());
    }

    public function create(CreateDatabaseClusterRequest $data): DatabaseCluster
    {
        $response = $this->send(new JsonRequest(Method::POST, '/databases', $data->toArray()));

        return DatabaseCluster::createFromResponse($response->json());
    }

    public function update(UpdateDatabaseClusterRequest $data): DatabaseCluster
    {
        $response = $this->send(new JsonRequest(Method::PATCH, "/databases/{$data->clusterId}", $data->toArray()));

        return DatabaseCluster::createFromResponse($response->json());
    }

    public function delete(string $clusterId): void
    {
        $this->send(new JsonRequest(Method::DELETE, "/databases/{$clusterId}"));
    }

    public function types(): array
    {
        $response = $this->send(new GetRequest('/databases/types'));

        return $response->json();
    }

    public function metrics(string $clusterId, ?string $period = null): array
    {
        $response = $this->send(new GetRequest("/databases/{$clusterId}/metrics", array_filter(['period' => $period], fn ($v) => $v !== null)));

        return $response->json();
    }
}
