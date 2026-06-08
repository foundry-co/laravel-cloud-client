<?php

namespace Foundry\LaravelCloudClient\Resources;

use Foundry\LaravelCloudClient\Data\Environment;
use Foundry\LaravelCloudClient\Data\EnvironmentLog;
use Foundry\LaravelCloudClient\Http\GetRequest;
use Foundry\LaravelCloudClient\Http\JsonRequest;
use Foundry\LaravelCloudClient\Requests\AddEnvironmentVariablesRequest;
use Foundry\LaravelCloudClient\Requests\CreateEnvironmentRequest;
use Foundry\LaravelCloudClient\Requests\ReplaceEnvironmentVariablesRequest;
use Foundry\LaravelCloudClient\Requests\UpdateEnvironmentRequest;
use Illuminate\Support\Collection;
use Saloon\Enums\Method;

class EnvironmentsResource extends Resource
{
    public function list(string $applicationId, ?string $name = null, ?string $status = null, ?string $slug = null): Collection
    {
        return $this->paginateAll(
            new GetRequest("/applications/{$applicationId}/environments", array_filter([
                'filter[name]' => $name,
                'filter[status]' => $status,
                'filter[slug]' => $slug,
            ], fn ($v) => $v !== null)),
            fn ($item) => Environment::createFromResponse($item)
        );
    }

    public function get(string $environmentId): Environment
    {
        $response = $this->send(new GetRequest("/environments/{$environmentId}"));

        return Environment::createFromResponse($response->json());
    }

    public function create(CreateEnvironmentRequest $data): Environment
    {
        $response = $this->send(new JsonRequest(Method::POST, "/applications/{$data->applicationId}/environments", $data->toArray()));

        return Environment::createFromResponse($response->json());
    }

    public function update(UpdateEnvironmentRequest $data): Environment
    {
        $response = $this->send(new JsonRequest(Method::PATCH, "/environments/{$data->environmentId}", $data->toArray()));

        return Environment::createFromResponse($response->json());
    }

    public function delete(string $environmentId): void
    {
        $this->send(new JsonRequest(Method::DELETE, "/environments/{$environmentId}"));
    }

    public function logs(string $environmentId, string $from, string $to, ?string $cursor = null, ?string $type = null, ?string $query = null): Collection
    {
        $response = $this->send(new GetRequest("/environments/{$environmentId}/logs", array_filter([
            'from' => $from,
            'to' => $to,
            'cursor' => $cursor,
            'type' => $type,
            'query' => $query,
        ], fn ($v) => $v !== null)));
        $data = $response->json();

        return collect($data['data'] ?? $data)->map(fn ($item) => EnvironmentLog::createFromResponse($item));
    }

    public function addVariables(AddEnvironmentVariablesRequest $data): void
    {
        $this->send(new JsonRequest(Method::POST, "/environments/{$data->environmentId}/variables", $data->toArray()));
    }

    public function replaceVariables(ReplaceEnvironmentVariablesRequest $data): void
    {
        $this->send(new JsonRequest(Method::PUT, "/environments/{$data->environmentId}/variables", $data->toArray()));
    }

    public function start(string $environmentId): void
    {
        $this->send(new JsonRequest(Method::POST, "/environments/{$environmentId}/start"));
    }

    public function stop(string $environmentId): void
    {
        $this->send(new JsonRequest(Method::POST, "/environments/{$environmentId}/stop"));
    }

    public function metrics(string $environmentId, ?string $period = null): array
    {
        $response = $this->send(new GetRequest("/environments/{$environmentId}/metrics", array_filter(['period' => $period], fn ($v) => $v !== null)));

        return $response->json();
    }

    public function purgeCache(string $environmentId): void
    {
        $this->send(new JsonRequest(Method::POST, "/environments/{$environmentId}/purge-cache"));
    }
}
