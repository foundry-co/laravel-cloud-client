<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Cache implements Arrayable, Jsonable
{
    public function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly string $name,
        public readonly string $status,
        public readonly string $region,
        public readonly string $size,
        public readonly bool $autoUpgradeEnabled,
        public readonly bool $isPublic,
        public readonly ?CarbonImmutable $createdAt,
        public readonly array $connection,
        public readonly array $environmentIds,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? [];
        $relationships = $data['relationships'] ?? [];

        return new static(
            id: $data['id'] ?? '',
            type: $attrs['type'] ?? '',
            name: $attrs['name'] ?? '',
            status: $attrs['status'] ?? '',
            region: $attrs['region'] ?? '',
            size: $attrs['size'] ?? '',
            autoUpgradeEnabled: (bool) ($attrs['auto_upgrade_enabled'] ?? false),
            isPublic: (bool) ($attrs['is_public'] ?? false),
            createdAt: isset($attrs['created_at']) ? CarbonImmutable::parse($attrs['created_at']) : null,
            connection: $attrs['connection'] ?? [],
            environmentIds: collect($relationships['environments']['data'] ?? [])->pluck('id')->all(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->name,
            'status' => $this->status,
            'region' => $this->region,
            'size' => $this->size,
            'auto_upgrade_enabled' => $this->autoUpgradeEnabled,
            'is_public' => $this->isPublic,
            'created_at' => $this->createdAt?->toIso8601String(),
            'connection' => $this->connection,
            'environment_ids' => $this->environmentIds,
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
