<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class BucketKey implements Arrayable, Jsonable
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $permission,
        public readonly ?CarbonImmutable $createdAt,
        public readonly ?string $accessKeyId,
        public readonly ?string $secretAccessKey,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? [];

        return new static(
            id: $data['id'] ?? '',
            name: $attrs['name'] ?? '',
            permission: $attrs['permission'] ?? '',
            createdAt: isset($attrs['created_at']) ? CarbonImmutable::parse($attrs['created_at']) : null,
            accessKeyId: $attrs['access_key_id'] ?? null,
            secretAccessKey: $attrs['secret_access_key'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'permission' => $this->permission,
            'created_at' => $this->createdAt?->toIso8601String(),
            'access_key_id' => $this->accessKeyId,
            'secret_access_key' => $this->secretAccessKey,
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
