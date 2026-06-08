<?php

namespace Foundry\LaravelCloudClient\Resources;

use Foundry\LaravelCloudClient\Data\Application;
use Foundry\LaravelCloudClient\Http\GetRequest;
use Foundry\LaravelCloudClient\Http\JsonRequest;
use Foundry\LaravelCloudClient\Http\MultipartRequest;
use Foundry\LaravelCloudClient\Requests\CreateApplicationRequest;
use Foundry\LaravelCloudClient\Requests\UpdateApplicationRequest;
use Illuminate\Support\Collection;
use Saloon\Enums\Method;

class ApplicationsResource extends Resource
{
    public function list(?string $name = null, ?string $region = null, ?string $slug = null): Collection
    {
        return $this->paginateAll(
            new GetRequest('/applications', array_filter([
                'filter[name]' => $name,
                'filter[region]' => $region,
                'filter[slug]' => $slug,
            ], fn ($v) => $v !== null)),
            fn ($item) => Application::createFromResponse($item)
        );
    }

    public function get(string $applicationId): Application
    {
        $response = $this->send(new GetRequest("/applications/{$applicationId}"));

        return Application::createFromResponse($response->json());
    }

    public function create(CreateApplicationRequest $data): Application
    {
        $response = $this->send(new JsonRequest(Method::POST, '/applications', $data->toArray()));

        return Application::createFromResponse($response->json());
    }

    public function update(UpdateApplicationRequest $data): Application
    {
        $response = $this->send(new JsonRequest(Method::PATCH, "/applications/{$data->applicationId}", $data->toArray()));

        return Application::createFromResponse($response->json());
    }

    public function delete(string $applicationId): void
    {
        $this->send(new JsonRequest(Method::DELETE, "/applications/{$applicationId}"));
    }

    public function uploadAvatar(string $applicationId, string $filePath): Application
    {
        $request = new MultipartRequest(
            "/applications/{$applicationId}/avatar",
            [['name' => 'avatar', 'contents' => fopen($filePath, 'r'), 'filename' => basename($filePath)]]
        );
        $response = $this->send($request);

        return Application::createFromResponse($response->json());
    }
}
