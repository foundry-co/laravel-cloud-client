<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Data\ObjectStorageBucket;
use FoundryCo\LaravelCloudClient\Http\GetRequest;
use FoundryCo\LaravelCloudClient\Http\JsonRequest;
use FoundryCo\LaravelCloudClient\Requests\CreateObjectStorageBucketRequest;
use FoundryCo\LaravelCloudClient\Requests\UpdateObjectStorageBucketRequest;
use Illuminate\Support\Collection;
use Saloon\Enums\Method;

class ObjectStorageBucketsResource extends Resource
{
    public function list(?string $type = null, ?string $status = null, ?string $visibility = null): Collection
    {
        return $this->paginateAll(
            new GetRequest('/buckets', array_filter([
                'filter[type]' => $type,
                'filter[status]' => $status,
                'filter[visibility]' => $visibility,
            ], fn ($v) => $v !== null)),
            fn ($item) => ObjectStorageBucket::createFromResponse($item)
        );
    }

    public function get(string $bucketId): ObjectStorageBucket
    {
        $response = $this->send(new GetRequest("/buckets/{$bucketId}"));

        return ObjectStorageBucket::createFromResponse($response->json());
    }

    public function create(CreateObjectStorageBucketRequest $data): ObjectStorageBucket
    {
        $response = $this->send(new JsonRequest(Method::POST, '/buckets', $data->toArray()));

        return ObjectStorageBucket::createFromResponse($response->json());
    }

    public function update(UpdateObjectStorageBucketRequest $data): ObjectStorageBucket
    {
        $response = $this->send(new JsonRequest(Method::PATCH, "/buckets/{$data->bucketId}", $data->toArray()));

        return ObjectStorageBucket::createFromResponse($response->json());
    }

    public function delete(string $bucketId): void
    {
        $this->send(new JsonRequest(Method::DELETE, "/buckets/{$bucketId}"));
    }
}
