<?php

namespace Foundry\LaravelCloudClient\Resources;

use Foundry\LaravelCloudClient\Data\EnvironmentInstance;
use Foundry\LaravelCloudClient\Http\GetRequest;
use Foundry\LaravelCloudClient\Http\JsonRequest;
use Foundry\LaravelCloudClient\Requests\CreateInstanceRequest;
use Foundry\LaravelCloudClient\Requests\UpdateInstanceRequest;
use Illuminate\Support\Collection;
use Saloon\Enums\Method;

class InstancesResource extends Resource
{
    public function list(string $environmentId): Collection
    {
        return $this->paginateAll(
            new GetRequest("/environments/{$environmentId}/instances"),
            fn ($item) => EnvironmentInstance::createFromResponse($item)
        );
    }

    public function get(string $instanceId): EnvironmentInstance
    {
        $response = $this->send(new GetRequest("/instances/{$instanceId}"));

        return EnvironmentInstance::createFromResponse($response->json());
    }

    public function create(CreateInstanceRequest $data): EnvironmentInstance
    {
        $response = $this->send(new JsonRequest(Method::POST, "/environments/{$data->environmentId}/instances", $data->toArray()));

        return EnvironmentInstance::createFromResponse($response->json());
    }

    public function update(UpdateInstanceRequest $data): EnvironmentInstance
    {
        $response = $this->send(new JsonRequest(Method::PATCH, "/instances/{$data->instanceId}", $data->toArray()));

        return EnvironmentInstance::createFromResponse($response->json());
    }

    public function delete(string $instanceId): void
    {
        $this->send(new JsonRequest(Method::DELETE, "/instances/{$instanceId}"));
    }

    public function sizes(): array
    {
        $response = $this->send(new GetRequest('/instances/sizes'));

        return $response->json();
    }

    public function pause(string $instanceId): EnvironmentInstance
    {
        $response = $this->send(new JsonRequest(Method::POST, "/instances/{$instanceId}/managed-queue/pause"));

        return EnvironmentInstance::createFromResponse($response->json());
    }

    public function resume(string $instanceId): EnvironmentInstance
    {
        $response = $this->send(new JsonRequest(Method::POST, "/instances/{$instanceId}/managed-queue/resume"));

        return EnvironmentInstance::createFromResponse($response->json());
    }

    public function purge(string $instanceId): EnvironmentInstance
    {
        $response = $this->send(new JsonRequest(Method::POST, "/instances/{$instanceId}/managed-queue/purge"));

        return EnvironmentInstance::createFromResponse($response->json());
    }

    public function setDefault(string $instanceId): EnvironmentInstance
    {
        $response = $this->send(new JsonRequest(Method::POST, "/instances/{$instanceId}/default"));

        return EnvironmentInstance::createFromResponse($response->json());
    }

    public function failedJobs(string $instanceId): Collection
    {
        $response = $this->send(new GetRequest("/instances/{$instanceId}/managed-queue/failed-jobs"));
        $data = $response->json();

        return collect($data['data'] ?? $data);
    }

    public function deleteFailedJob(string $instanceId, string $jobId): void
    {
        $this->send(new JsonRequest(Method::DELETE, "/instances/{$instanceId}/managed-queue/failed-jobs/{$jobId}"));
    }

    public function retryFailedJob(string $instanceId, string $jobId): void
    {
        $this->send(new JsonRequest(Method::POST, "/instances/{$instanceId}/managed-queue/failed-jobs/{$jobId}/retry"));
    }
}
