<?php

namespace Foundry\LaravelCloudClient\Resources;

use Foundry\LaravelCloudClient\Data\Deployment;
use Foundry\LaravelCloudClient\Http\GetRequest;
use Foundry\LaravelCloudClient\Http\JsonRequest;
use Foundry\LaravelCloudClient\Requests\CreateDeploymentRequest;
use Illuminate\Support\Collection;
use Saloon\Enums\Method;

class DeploymentsResource extends Resource
{
    public function list(string $environmentId, ?string $status = null, ?string $branchName = null, ?string $commitHash = null): Collection
    {
        return $this->paginateAll(
            new GetRequest("/environments/{$environmentId}/deployments", array_filter([
                'filter[status]' => $status,
                'filter[branch_name]' => $branchName,
                'filter[commit_hash]' => $commitHash,
            ], fn ($v) => $v !== null)),
            fn ($item) => Deployment::createFromResponse($item)
        );
    }

    public function get(string $deploymentId): Deployment
    {
        $response = $this->send(new GetRequest("/deployments/{$deploymentId}"));

        return Deployment::createFromResponse($response->json());
    }

    public function initiate(CreateDeploymentRequest $data): Deployment
    {
        $response = $this->send(new JsonRequest(Method::POST, "/environments/{$data->environmentId}/deployments", $data->toArray()));

        return Deployment::createFromResponse($response->json());
    }

    public function logs(string $deploymentId): array
    {
        $response = $this->send(new GetRequest("/deployments/{$deploymentId}/logs"));

        return $response->json();
    }
}
