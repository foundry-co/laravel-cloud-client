<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use FoundryCo\LaravelCloudClient\Concerns\DeletesItself;
use FoundryCo\LaravelCloudClient\Concerns\UpdatesItself;
use FoundryCo\LaravelCloudClient\Resources\BucketKeysResource;

class BucketKey extends Model
{
    use DeletesItself, UpdatesItself;

    protected static string $resource = BucketKeysResource::class;

    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $permission,
        public readonly ?string $accessKeyId,
        public readonly ?string $accessKeySecret,
        public readonly ?CarbonImmutable $createdAt,
        public readonly ?string $filesystemId,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? [];
        $relationships = $data['relationships'] ?? [];

        return new static(
            id: Cast::string($data['id'] ?? null),
            name: Cast::string($attrs['name'] ?? null),
            permission: Cast::string($attrs['permission'] ?? null),
            accessKeyId: Cast::nullableString($attrs['access_key_id'] ?? null),
            accessKeySecret: Cast::nullableString($attrs['access_key_secret'] ?? null),
            createdAt: Cast::date($attrs['created_at'] ?? null),
            filesystemId: Cast::relation($relationships, 'filesystem'),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'permission' => $this->permission,
            'access_key_id' => $this->accessKeyId,
            'access_key_secret' => $this->accessKeySecret,
            'created_at' => $this->createdAt?->toIso8601String(),
            'filesystem_id' => $this->filesystemId,
        ];
    }
}
