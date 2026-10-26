<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Data\BackgroundProcess;
use FoundryCo\LaravelCloudClient\Http\GetRequest;
use FoundryCo\LaravelCloudClient\Http\JsonRequest;
use FoundryCo\LaravelCloudClient\Requests\CreateBackgroundProcessRequest;
use FoundryCo\LaravelCloudClient\Requests\UpdateBackgroundProcessRequest;
use Illuminate\Support\Collection;
use Saloon\Enums\Method;

class BackgroundProcessesResource extends Resource
{
    public function list(string $instanceId): Collection
    {
        return $this->paginateAll(
            new GetRequest("/instances/{$instanceId}/background-processes"),
            fn ($item) => BackgroundProcess::createFromResponse($item)
        );
    }

    public function get(string $backgroundProcessId): BackgroundProcess
    {
        $response = $this->send(new GetRequest("/background-processes/{$backgroundProcessId}"));

        return BackgroundProcess::createFromResponse($response->json());
    }

    public function create(CreateBackgroundProcessRequest $data): BackgroundProcess
    {
        $response = $this->send(new JsonRequest(Method::POST, "/instances/{$data->instanceId}/background-processes", $data->toArray()));

        return BackgroundProcess::createFromResponse($response->json());
    }

    public function update(UpdateBackgroundProcessRequest $data): BackgroundProcess
    {
        $response = $this->send(new JsonRequest(Method::PATCH, "/background-processes/{$data->backgroundProcessId}", $data->toArray()));

        return BackgroundProcess::createFromResponse($response->json());
    }

    public function delete(string $backgroundProcessId): void
    {
        $this->send(new JsonRequest(Method::DELETE, "/background-processes/{$backgroundProcessId}"));
    }
}
