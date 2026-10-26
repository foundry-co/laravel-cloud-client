<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Data\BucketKey;
use FoundryCo\LaravelCloudClient\Http\GetRequest;
use FoundryCo\LaravelCloudClient\Http\JsonRequest;
use FoundryCo\LaravelCloudClient\Requests\CreateBucketKeyRequest;
use FoundryCo\LaravelCloudClient\Requests\UpdateBucketKeyRequest;
use Illuminate\Support\Collection;
use Saloon\Enums\Method;

class BucketKeysResource extends Resource
{
    public function list(string $bucketId): Collection
    {
        return $this->paginateAll(
            new GetRequest("/buckets/{$bucketId}/keys"),
            fn ($item) => BucketKey::createFromResponse($item)
        );
    }

    public function get(string $keyId): BucketKey
    {
        $response = $this->send(new GetRequest("/bucket-keys/{$keyId}"));

        return BucketKey::createFromResponse($response->json());
    }

    public function create(CreateBucketKeyRequest $data): BucketKey
    {
        $response = $this->send(new JsonRequest(Method::POST, "/buckets/{$data->bucketId}/keys", $data->toArray()));

        return BucketKey::createFromResponse($response->json());
    }

    public function update(UpdateBucketKeyRequest $data): BucketKey
    {
        $response = $this->send(new JsonRequest(Method::PATCH, "/bucket-keys/{$data->keyId}", $data->toArray()));

        return BucketKey::createFromResponse($response->json());
    }

    public function delete(string $keyId): void
    {
        $this->send(new JsonRequest(Method::DELETE, "/bucket-keys/{$keyId}"));
    }
}
