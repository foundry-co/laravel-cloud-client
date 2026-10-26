<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Data\DatabaseSnapshot;
use FoundryCo\LaravelCloudClient\Http\GetRequest;
use FoundryCo\LaravelCloudClient\Http\JsonRequest;
use FoundryCo\LaravelCloudClient\Requests\CreateDatabaseSnapshotRequest;
use Illuminate\Support\Collection;
use Saloon\Enums\Method;

class DatabaseSnapshotsResource extends Resource
{
    public function list(string $clusterId): Collection
    {
        return $this->paginateAll(
            new GetRequest("/databases/{$clusterId}/snapshots"),
            fn ($item) => DatabaseSnapshot::createFromResponse($item)
        );
    }

    public function get(string $clusterId, string $snapshotId): DatabaseSnapshot
    {
        $response = $this->send(new GetRequest("/snapshots/{$snapshotId}"));

        return DatabaseSnapshot::createFromResponse($response->json());
    }

    public function create(CreateDatabaseSnapshotRequest $data): DatabaseSnapshot
    {
        $response = $this->send(new JsonRequest(Method::POST, "/databases/{$data->clusterId}/snapshots", $data->toArray()));

        return DatabaseSnapshot::createFromResponse($response->json());
    }

    public function delete(string $clusterId, string $snapshotId): void
    {
        $this->send(new JsonRequest(Method::DELETE, "/snapshots/{$snapshotId}"));
    }
}
