<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Data\DatabaseCluster;
use FoundryCo\LaravelCloudClient\Http\JsonRequest;
use FoundryCo\LaravelCloudClient\Requests\CreateDatabaseRestoreRequest;
use Saloon\Enums\Method;

class DatabaseRestoresResource extends Resource
{
    public function create(CreateDatabaseRestoreRequest $data): DatabaseCluster
    {
        $response = $this->send(new JsonRequest(Method::POST, "/snapshots/{$data->snapshotId}/restore", $data->toArray()));

        return DatabaseCluster::createFromResponse($response->json());
    }
}
