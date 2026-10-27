<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use FoundryCo\LaravelCloudClient\Concerns\DeletesItself;
use FoundryCo\LaravelCloudClient\Concerns\UpdatesItself;
use FoundryCo\LaravelCloudClient\Resources\DatabaseClustersResource;
use FoundryCo\LaravelCloudClient\Resources\DatabaseSnapshotsResource;
use FoundryCo\LaravelCloudClient\Resources\DatabasesResource;

class DatabaseCluster extends Model
{
    use DeletesItself, UpdatesItself;

    protected static string $resource = DatabaseClustersResource::class;

    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $type,
        public readonly string $status,
        public readonly string $region,
        public readonly ?CarbonImmutable $createdAt,
        public readonly mixed $config,
        public readonly string $supportsPitr,
        public readonly ?string $earliestRestoreTime,
        public readonly array $connection,
        public readonly array $databasesIds,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? [];
        $relationships = $data['relationships'] ?? [];

        return new static(
            id: Cast::string($data['id'] ?? null),
            name: Cast::string($attrs['name'] ?? null),
            type: Cast::string($attrs['type'] ?? null),
            status: Cast::string($attrs['status'] ?? null),
            region: Cast::string($attrs['region'] ?? null),
            createdAt: Cast::date($attrs['created_at'] ?? null),
            config: $attrs['config'] ?? null,
            supportsPitr: Cast::string($attrs['supports_pitr'] ?? null),
            earliestRestoreTime: Cast::nullableString($attrs['earliest_restore_time'] ?? null),
            connection: Cast::array($attrs['connection'] ?? null),
            databasesIds: Cast::relations($relationships, 'databases'),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'status' => $this->status,
            'region' => $this->region,
            'created_at' => $this->createdAt?->toIso8601String(),
            'config' => $this->config,
            'supports_pitr' => $this->supportsPitr,
            'earliest_restore_time' => $this->earliestRestoreTime,
            'connection' => $this->connection,
            'databases_ids' => $this->databasesIds,
        ];
    }

    public function snapshots(): DatabaseSnapshotsResource
    {
        return $this->children(DatabaseSnapshotsResource::class);
    }

    public function databases(): DatabasesResource
    {
        return $this->children(DatabasesResource::class);
    }

    public function snapshot(string $name, ?string $description = null): DatabaseSnapshot
    {
        return $this->snapshots()->create(name: $name, description: $description);
    }

    public function metrics(?string $period = null): array
    {
        return $this->resource()->metrics($this->id, $period);
    }

    public function restore(string $name, ?string $snapshotId = null, ?string $restoreTime = null): array
    {
        return $this->resource()->restore($this->id, $name, $snapshotId, $restoreTime);
    }
}
