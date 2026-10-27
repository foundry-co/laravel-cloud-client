<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use FoundryCo\LaravelCloudClient\Resources\EdgeNetworksResource;

class EdgeNetwork extends Model
{
    protected static string $resource = EdgeNetworksResource::class;

    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $domain,
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
            domain: Cast::string($attrs['domain'] ?? null),
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
            'domain' => $this->domain,
            'tenancy_type' => $this->tenancyType,
            'status' => $this->status,
            'created_at' => $this->createdAt?->toIso8601String(),
        ];
    }
}
