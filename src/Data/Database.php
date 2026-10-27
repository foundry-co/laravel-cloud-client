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
            id: Cast::string($data['id'] ?? null),
            name: Cast::string($attrs['name'] ?? null),
            status: Cast::string($attrs['status'] ?? null),
            createdAt: Cast::date($attrs['created_at'] ?? null),
            databaseId: Cast::relation($relationships, 'database'),
            environmentsIds: Cast::relations($relationships, 'environments'),
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
