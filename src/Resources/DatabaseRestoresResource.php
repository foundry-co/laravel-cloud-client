<?php

namespace Foundry\LaravelCloudClient\Resources;

use Foundry\LaravelCloudClient\Data\DatabaseCluster;
use Foundry\LaravelCloudClient\Http\JsonRequest;
use Foundry\LaravelCloudClient\Requests\CreateDatabaseRestoreRequest;
use Saloon\Enums\Method;

class DatabaseRestoresResource extends Resource
{
    public function create(CreateDatabaseRestoreRequest $data): DatabaseCluster
    {
        $response = $this->send(new JsonRequest(Method::POST, "/snapshots/{$data->snapshotId}/restore", $data->toArray()));

        return DatabaseCluster::createFromResponse($response->json());
    }
}
