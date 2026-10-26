<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use FoundryCo\LaravelCloudClient\Concerns\DeletesItself;
use FoundryCo\LaravelCloudClient\Resources\DatabasesResource;

class Database extends Model
{
    use DeletesItself;

    protected static string $resource = DatabasesResource::class;

    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $status,
        public readonly ?CarbonImmutable $createdAt,
        public readonly ?string $databaseId,
        public readonly array $environmentsIds,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? [];
        $relationships = $data['relationships'] ?? [];

        return new static(
            id: $data['id'] ?? '',
            name: $attrs['name'] ?? '',
            status: $attrs['status'] ?? '',
            createdAt: isset($attrs['created_at']) ? CarbonImmutable::parse($attrs['created_at']) : null,
            databaseId: $relationships['database']['data']['id'] ?? null,
            environmentsIds: collect($relationships['environments']['data'] ?? [])->pluck('id')->all(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'created_at' => $this->createdAt?->toIso8601String(),
            'database_id' => $this->databaseId,
            'environments_ids' => $this->environmentsIds,
        ];
    }
}
