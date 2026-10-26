<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Data\Database;
use FoundryCo\LaravelCloudClient\Http\GetRequest;
use FoundryCo\LaravelCloudClient\Http\JsonRequest;
use FoundryCo\LaravelCloudClient\Requests\CreateDatabaseRequest;
use Illuminate\Support\Collection;
use Saloon\Enums\Method;

class DatabasesResource extends Resource
{
    public function list(string $clusterId): Collection
    {
        return $this->paginateAll(
            new GetRequest("/databases/{$clusterId}/databases"),
            fn ($item) => Database::createFromResponse($item)
        );
    }

    public function get(string $clusterId, string $databaseId): Database
    {
        $response = $this->send(new GetRequest("/databases/{$clusterId}/databases/{$databaseId}"));

        return Database::createFromResponse($response->json());
    }

    public function create(CreateDatabaseRequest $data): Database
    {
        $response = $this->send(new JsonRequest(Method::POST, "/databases/{$data->clusterId}/databases", $data->toArray()));

        return Database::createFromResponse($response->json());
    }

    public function delete(string $clusterId, string $databaseId): void
    {
        $this->send(new JsonRequest(Method::DELETE, "/databases/{$clusterId}/databases/{$databaseId}"));
    }
}
