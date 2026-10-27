<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use FoundryCo\LaravelCloudClient\Concerns\DeletesItself;
use FoundryCo\LaravelCloudClient\Concerns\UpdatesItself;
use FoundryCo\LaravelCloudClient\Resources\SecretsResource;

class Secret extends Model
{
    use DeletesItself, UpdatesItself;

    protected static string $resource = SecretsResource::class;

    public function __construct(
        public readonly string $id,
        public readonly string $key,
        public readonly ?string $notes,
        public readonly ?CarbonImmutable $createdAt,
        public readonly ?CarbonImmutable $updatedAt,
        public readonly array $environmentsIds,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? [];
        $relationships = $data['relationships'] ?? [];

        return new static(
            id: Cast::string($data['id'] ?? null),
            key: Cast::string($attrs['key'] ?? null),
            notes: Cast::nullableString($attrs['notes'] ?? null),
            createdAt: Cast::date($attrs['created_at'] ?? null),
            updatedAt: Cast::date($attrs['updated_at'] ?? null),
            environmentsIds: Cast::relations($relationships, 'environments'),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'notes' => $this->notes,
            'created_at' => $this->createdAt?->toIso8601String(),
            'updated_at' => $this->updatedAt?->toIso8601String(),
            'environments_ids' => $this->environmentsIds,
        ];
    }
}
