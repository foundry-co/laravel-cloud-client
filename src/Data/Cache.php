<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use FoundryCo\LaravelCloudClient\Concerns\DeletesItself;
use FoundryCo\LaravelCloudClient\Concerns\UpdatesItself;
use FoundryCo\LaravelCloudClient\Resources\CachesResource;

class Cache extends Model
{
    use DeletesItself, UpdatesItself;

    protected static string $resource = CachesResource::class;

    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $type,
        public readonly string $status,
        public readonly string $region,
        public readonly string $size,
        public readonly bool $autoUpgradeEnabled,
        public readonly bool $isPublic,
        public readonly mixed $evictionPolicy,
        public readonly bool $usesHibernation,
        public readonly mixed $hibernationTimeout,
        public readonly ?CarbonImmutable $createdAt,
        public readonly array $connection,
        public readonly array $environmentsIds,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? [];
        $relationships = $data['relationships'] ?? [];

        return new static(
            id: Cast::string($data['id'] ?? null),
            name: Cast::string($attrs['name'] ?? null),
            type: Cast::string($attrs['type'] ?? null),
            status: Cast::string($attrs['status'] ?? null),
            region: Cast::string($attrs['region'] ?? null),
            size: Cast::string($attrs['size'] ?? null),
            autoUpgradeEnabled: Cast::bool($attrs['auto_upgrade_enabled'] ?? null),
            isPublic: Cast::bool($attrs['is_public'] ?? null),
            evictionPolicy: $attrs['eviction_policy'] ?? null,
            usesHibernation: Cast::bool($attrs['uses_hibernation'] ?? null),
            hibernationTimeout: $attrs['hibernation_timeout'] ?? null,
            createdAt: Cast::date($attrs['created_at'] ?? null),
            connection: Cast::array($attrs['connection'] ?? null),
            environmentsIds: Cast::relations($relationships, 'environments'),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'status' => $this->status,
            'region' => $this->region,
            'size' => $this->size,
            'auto_upgrade_enabled' => $this->autoUpgradeEnabled,
            'is_public' => $this->isPublic,
            'eviction_policy' => $this->evictionPolicy,
            'uses_hibernation' => $this->usesHibernation,
            'hibernation_timeout' => $this->hibernationTimeout,
            'created_at' => $this->createdAt?->toIso8601String(),
            'connection' => $this->connection,
            'environments_ids' => $this->environmentsIds,
        ];
    }

    public function metrics(?string $period = null): array
    {
        return $this->resource()->metrics($this->id, $period);
    }
}
