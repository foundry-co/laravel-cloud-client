<?php

namespace Foundry\LaravelCloudClient\Resources;

use Foundry\LaravelCloudClient\Data\Database;
use Foundry\LaravelCloudClient\Http\GetRequest;
use Foundry\LaravelCloudClient\Http\JsonRequest;
use Foundry\LaravelCloudClient\Requests\CreateDatabaseRequest;
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
