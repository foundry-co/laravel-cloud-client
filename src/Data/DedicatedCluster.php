<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use FoundryCo\LaravelCloudClient\Resources\DedicatedClustersResource;

class DedicatedCluster extends Model
{
    protected static string $resource = DedicatedClustersResource::class;

    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $region,
        public readonly string $type,
        public readonly string $tenancyType,
        public readonly string $status,
        public readonly ?CarbonImmutable $createdAt,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? [];
        $relationships = $data['relationships'] ?? [];

        return new static(
            id: Cast::string($data['id'] ?? null),
            name: Cast::string($attrs['name'] ?? null),
            region: Cast::string($attrs['region'] ?? null),
            type: Cast::string($attrs['type'] ?? null),
            tenancyType: Cast::string($attrs['tenancy_type'] ?? null),
            status: Cast::string($attrs['status'] ?? null),
            createdAt: Cast::date($attrs['created_at'] ?? null),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'region' => $this->region,
            'type' => $this->type,
            'tenancy_type' => $this->tenancyType,
            'status' => $this->status,
            'created_at' => $this->createdAt?->toIso8601String(),
        ];
    }
}
