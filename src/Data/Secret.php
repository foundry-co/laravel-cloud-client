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
            id: $data['id'] ?? '',
            key: $attrs['key'] ?? '',
            notes: $attrs['notes'] ?? null,
            createdAt: isset($attrs['created_at']) ? CarbonImmutable::parse($attrs['created_at']) : null,
            updatedAt: isset($attrs['updated_at']) ? CarbonImmutable::parse($attrs['updated_at']) : null,
            environmentsIds: collect($relationships['environments']['data'] ?? [])->pluck('id')->all(),
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
