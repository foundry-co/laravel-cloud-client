<?php

namespace Foundry\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class DatabaseSnapshot implements Arrayable, Jsonable
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $status,
        public readonly ?CarbonImmutable $createdAt,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? [];

        return new static(
            id: $data['id'] ?? '',
            name: $attrs['name'] ?? '',
            status: $attrs['status'] ?? null,
            createdAt: isset($attrs['created_at']) ? CarbonImmutable::parse($attrs['created_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'created_at' => $this->createdAt?->toIso8601String(),
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
