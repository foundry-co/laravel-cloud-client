<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use FoundryCo\LaravelCloudClient\Concerns\DeletesItself;
use FoundryCo\LaravelCloudClient\Concerns\UpdatesItself;
use FoundryCo\LaravelCloudClient\Enums\FilesystemJurisdiction;
use FoundryCo\LaravelCloudClient\Enums\FilesystemStatus;
use FoundryCo\LaravelCloudClient\Enums\FilesystemType;
use FoundryCo\LaravelCloudClient\Enums\FilesystemVisibility;
use FoundryCo\LaravelCloudClient\Resources\BucketKeysResource;
use FoundryCo\LaravelCloudClient\Resources\ObjectStorageBucketsResource;

class ObjectStorageBucket extends Model
{
    use DeletesItself, UpdatesItself;

    protected static string $resource = ObjectStorageBucketsResource::class;

    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?FilesystemType $type,
        public readonly ?FilesystemStatus $status,
        public readonly ?FilesystemVisibility $visibility,
        public readonly ?FilesystemJurisdiction $jurisdiction,
        public readonly mixed $locationHint,
        public readonly ?string $endpoint,
        public readonly ?string $url,
        public readonly mixed $allowedOrigins,
        public readonly mixed $corsSettings,
        public readonly ?CarbonImmutable $createdAt,
        public readonly array $keysIds,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? [];
        $relationships = $data['relationships'] ?? [];

        return new static(
            id: Cast::string($data['id'] ?? null),
            name: Cast::string($attrs['name'] ?? null),
            type: Cast::enum(FilesystemType::class, $attrs['type'] ?? null),
            status: Cast::enum(FilesystemStatus::class, $attrs['status'] ?? null),
            visibility: Cast::enum(FilesystemVisibility::class, $attrs['visibility'] ?? null),
            jurisdiction: Cast::enum(FilesystemJurisdiction::class, $attrs['jurisdiction'] ?? null),
            locationHint: $attrs['location_hint'] ?? null,
            endpoint: Cast::nullableString($attrs['endpoint'] ?? null),
            url: Cast::nullableString($attrs['url'] ?? null),
            allowedOrigins: $attrs['allowed_origins'] ?? null,
            corsSettings: $attrs['cors_settings'] ?? null,
            createdAt: Cast::date($attrs['created_at'] ?? null),
            keysIds: Cast::relations($relationships, 'keys'),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type?->value,
            'status' => $this->status?->value,
            'visibility' => $this->visibility?->value,
            'jurisdiction' => $this->jurisdiction?->value,
            'location_hint' => $this->locationHint,
            'endpoint' => $this->endpoint,
            'url' => $this->url,
            'allowed_origins' => $this->allowedOrigins,
            'cors_settings' => $this->corsSettings,
            'created_at' => $this->createdAt?->toIso8601String(),
            'keys_ids' => $this->keysIds,
        ];
    }

    public function keys(): BucketKeysResource
    {
        return $this->children(BucketKeysResource::class);
    }
}
