<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Data\Cache;
use FoundryCo\LaravelCloudClient\Http\GetRequest;
use FoundryCo\LaravelCloudClient\Http\JsonRequest;
use FoundryCo\LaravelCloudClient\Requests\CreateCacheRequest;
use FoundryCo\LaravelCloudClient\Requests\UpdateCacheRequest;
use Illuminate\Support\Collection;
use Saloon\Enums\Method;

class CachesResource extends Resource
{
    public function list(?string $type = null, ?string $region = null, ?string $status = null): Collection
    {
        return $this->paginateAll(
            new GetRequest('/caches', array_filter([
                'filter[type]' => $type,
                'filter[region]' => $region,
                'filter[status]' => $status,
            ], fn ($v) => $v !== null)),
            fn ($item) => Cache::createFromResponse($item)
        );
    }

    public function get(string $cacheId): Cache
    {
        $response = $this->send(new GetRequest("/caches/{$cacheId}"));

        return Cache::createFromResponse($response->json());
    }

    public function create(CreateCacheRequest $data): Cache
    {
        $response = $this->send(new JsonRequest(Method::POST, '/caches', $data->toArray()));

        return Cache::createFromResponse($response->json());
    }

    public function update(UpdateCacheRequest $data): Cache
    {
        $response = $this->send(new JsonRequest(Method::PATCH, "/caches/{$data->cacheId}", $data->toArray()));

        return Cache::createFromResponse($response->json());
    }

    public function delete(string $cacheId): void
    {
        $this->send(new JsonRequest(Method::DELETE, "/caches/{$cacheId}"));
    }

    public function types(): array
    {
        $response = $this->send(new GetRequest('/caches/types'));

        return $response->json();
    }

    public function metrics(string $cacheId, ?string $period = null): array
    {
        $response = $this->send(new GetRequest("/caches/{$cacheId}/metrics", array_filter(['period' => $period], fn ($v) => $v !== null)));

        return $response->json();
    }
}
