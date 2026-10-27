<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use FoundryCo\LaravelCloudClient\Concerns\DeletesItself;
use FoundryCo\LaravelCloudClient\Concerns\UpdatesItself;
use FoundryCo\LaravelCloudClient\Enums\WebsocketServerConnectionDistributionStrategy;
use FoundryCo\LaravelCloudClient\Enums\WebsocketServerMaxConnection;
use FoundryCo\LaravelCloudClient\Enums\WebsocketServerStatus;
use FoundryCo\LaravelCloudClient\Enums\WebsocketServerType;
use FoundryCo\LaravelCloudClient\Resources\WebSocketApplicationsResource;
use FoundryCo\LaravelCloudClient\Resources\WebSocketClustersResource;

class WebsocketCluster extends Model
{
    use DeletesItself, UpdatesItself;

    protected static string $resource = WebSocketClustersResource::class;

    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?WebsocketServerType $type,
        public readonly string $region,
        public readonly ?WebsocketServerStatus $status,
        public readonly ?WebsocketServerMaxConnection $maxConnections,
        public readonly ?WebsocketServerConnectionDistributionStrategy $connectionDistributionStrategy,
        public readonly string $hostname,
        public readonly ?CarbonImmutable $createdAt,
        public readonly array $applicationsIds,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? [];
        $relationships = $data['relationships'] ?? [];

        return new static(
            id: Cast::string($data['id'] ?? null),
            name: Cast::string($attrs['name'] ?? null),
            type: Cast::enum(WebsocketServerType::class, $attrs['type'] ?? null),
            region: Cast::string($attrs['region'] ?? null),
            status: Cast::enum(WebsocketServerStatus::class, $attrs['status'] ?? null),
            maxConnections: Cast::enum(WebsocketServerMaxConnection::class, $attrs['max_connections'] ?? null),
            connectionDistributionStrategy: Cast::enum(WebsocketServerConnectionDistributionStrategy::class, $attrs['connection_distribution_strategy'] ?? null),
            hostname: Cast::string($attrs['hostname'] ?? null),
            createdAt: Cast::date($attrs['created_at'] ?? null),
            applicationsIds: Cast::relations($relationships, 'applications'),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type?->value,
            'region' => $this->region,
            'status' => $this->status?->value,
            'max_connections' => $this->maxConnections?->value,
            'connection_distribution_strategy' => $this->connectionDistributionStrategy?->value,
            'hostname' => $this->hostname,
            'created_at' => $this->createdAt?->toIso8601String(),
            'applications_ids' => $this->applicationsIds,
        ];
    }

    public function applications(): WebSocketApplicationsResource
    {
        return $this->children(WebSocketApplicationsResource::class);
    }

    public function metrics(?string $period = null): array
    {
        return $this->resource()->metrics($this->id, $period);
    }
}
