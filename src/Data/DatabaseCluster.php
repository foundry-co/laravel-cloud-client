<?php

namespace Foundry\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class DatabaseCluster implements Arrayable, Jsonable
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $type,
        public readonly string $status,
        public readonly string $region,
        public readonly array $config,
        public readonly array $connection,
        public readonly ?CarbonImmutable $createdAt,
        public readonly ?CarbonImmutable $updatedAt,
        public readonly array $schemas,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? [];
        $relationships = $data['relationships'] ?? [];

        return new static(
            id: $data['id'] ?? '',
            name: $attrs['name'] ?? '',
            type: $attrs['type'] ?? '',
            status: $attrs['status'] ?? '',
            region: $attrs['region'] ?? '',
            config: $attrs['config'] ?? [],
            connection: $attrs['connection'] ?? [],
            createdAt: isset($attrs['created_at']) ? CarbonImmutable::parse($attrs['created_at']) : null,
            updatedAt: isset($attrs['updated_at']) ? CarbonImmutable::parse($attrs['updated_at']) : null,
            schemas: collect($relationships['databases']['data'] ?? [])->pluck('id')->all(),
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
            'config' => $this->config,
            'connection' => $this->connection,
            'created_at' => $this->createdAt?->toIso8601String(),
            'updated_at' => $this->updatedAt?->toIso8601String(),
            'schemas' => $this->schemas,
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
