<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use FoundryCo\LaravelCloudClient\Resources\ResendDomainsResource;

class ResendDomain extends Model
{
    protected static string $resource = ResendDomainsResource::class;

    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $region,
        public readonly string $status,
        public readonly ?array $records,
        public readonly ?CarbonImmutable $lastVerifiedAt,
        public readonly ?CarbonImmutable $createdAt,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? [];
        $relationships = $data['relationships'] ?? [];

        return new static(
            id: $data['id'] ?? '',
            name: $attrs['name'] ?? '',
            region: $attrs['region'] ?? '',
            status: $attrs['status'] ?? '',
            records: $attrs['records'] ?? null,
            lastVerifiedAt: isset($attrs['last_verified_at']) ? CarbonImmutable::parse($attrs['last_verified_at']) : null,
            createdAt: isset($attrs['created_at']) ? CarbonImmutable::parse($attrs['created_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'region' => $this->region,
            'status' => $this->status,
            'records' => $this->records,
            'last_verified_at' => $this->lastVerifiedAt?->toIso8601String(),
            'created_at' => $this->createdAt?->toIso8601String(),
        ];
    }
}
