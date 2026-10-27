<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use FoundryCo\LaravelCloudClient\Concerns\DeletesItself;
use FoundryCo\LaravelCloudClient\Resources\DatabaseClustersResource;
use FoundryCo\LaravelCloudClient\Resources\DatabaseSnapshotsResource;

class DatabaseSnapshot extends Model
{
    use DeletesItself;

    protected static string $resource = DatabaseSnapshotsResource::class;

    public function __construct(
        public readonly string $id,
        public readonly ?string $name,
        public readonly ?string $description,
        public readonly string $type,
        public readonly string $status,
        public readonly ?int $storageBytes,
        public readonly ?bool $pitrEnabled,
        public readonly ?CarbonImmutable $pitrEndsAt,
        public readonly ?CarbonImmutable $completedAt,
        public readonly ?CarbonImmutable $createdAt,
        public readonly ?string $databaseId,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? [];
        $relationships = $data['relationships'] ?? [];

        return new static(
            id: Cast::string($data['id'] ?? null),
            name: Cast::nullableString($attrs['name'] ?? null),
            description: Cast::nullableString($attrs['description'] ?? null),
            type: Cast::string($attrs['type'] ?? null),
            status: Cast::string($attrs['status'] ?? null),
            storageBytes: Cast::nullableInt($attrs['storage_bytes'] ?? null),
            pitrEnabled: Cast::nullableBool($attrs['pitr_enabled'] ?? null),
            pitrEndsAt: Cast::date($attrs['pitr_ends_at'] ?? null),
            completedAt: Cast::date($attrs['completed_at'] ?? null),
            createdAt: Cast::date($attrs['created_at'] ?? null),
            databaseId: Cast::relation($relationships, 'database'),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'status' => $this->status,
            'storage_bytes' => $this->storageBytes,
            'pitr_enabled' => $this->pitrEnabled,
            'pitr_ends_at' => $this->pitrEndsAt?->toIso8601String(),
            'completed_at' => $this->completedAt?->toIso8601String(),
            'created_at' => $this->createdAt?->toIso8601String(),
            'database_id' => $this->databaseId,
        ];
    }

    public function restore(string $name): array
    {
        return $this->scoped(DatabaseClustersResource::class, null)->restore($this->databaseId, $name, $this->id);
    }
}
