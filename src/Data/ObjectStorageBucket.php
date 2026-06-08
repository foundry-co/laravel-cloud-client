<?php

namespace Foundry\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use Foundry\LaravelCloudClient\Enums\FilesystemJurisdiction;
use Foundry\LaravelCloudClient\Enums\FilesystemStatus;
use Foundry\LaravelCloudClient\Enums\FilesystemType;
use Foundry\LaravelCloudClient\Enums\FilesystemVisibility;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class ObjectStorageBucket implements Arrayable, Jsonable
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly FilesystemType $type,
        public readonly FilesystemStatus $status,
        public readonly FilesystemVisibility $visibility,
        public readonly FilesystemJurisdiction $jurisdiction,
        public readonly ?string $endpoint,
        public readonly ?string $url,
        public readonly ?array $allowedOrigins,
        public readonly ?CarbonImmutable $createdAt,
        public readonly array $keyIds,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? [];
        $relationships = $data['relationships'] ?? [];

        return new static(
            id: $data['id'] ?? '',
            name: $attrs['name'] ?? '',
            type: FilesystemType::from($attrs['type'] ?? 'cloudflare_r2'),
            status: FilesystemStatus::from($attrs['status'] ?? 'unknown'),
            visibility: FilesystemVisibility::from($attrs['visibility'] ?? 'private'),
            jurisdiction: FilesystemJurisdiction::from($attrs['jurisdiction'] ?? 'default'),
            endpoint: $attrs['endpoint'] ?? null,
            url: $attrs['url'] ?? null,
            allowedOrigins: $attrs['allowed_origins'] ?? null,
            createdAt: isset($attrs['created_at']) ? CarbonImmutable::parse($attrs['created_at']) : null,
            keyIds: collect($relationships['keys']['data'] ?? [])->pluck('id')->all(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type->value,
            'status' => $this->status->value,
            'visibility' => $this->visibility->value,
            'jurisdiction' => $this->jurisdiction->value,
            'endpoint' => $this->endpoint,
            'url' => $this->url,
            'allowed_origins' => $this->allowedOrigins,
            'created_at' => $this->createdAt?->toIso8601String(),
            'key_ids' => $this->keyIds,
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
