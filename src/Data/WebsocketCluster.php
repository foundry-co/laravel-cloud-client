<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use FoundryCo\LaravelCloudClient\Enums\WebsocketServerConnectionDistributionStrategy;
use FoundryCo\LaravelCloudClient\Enums\WebsocketServerMaxConnection;
use FoundryCo\LaravelCloudClient\Enums\WebsocketServerStatus;
use FoundryCo\LaravelCloudClient\Enums\WebsocketServerType;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class WebsocketCluster implements Arrayable, Jsonable
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly WebsocketServerType $type,
        public readonly string $region,
        public readonly WebsocketServerStatus $status,
        public readonly WebsocketServerMaxConnection $maxConnections,
        public readonly WebsocketServerConnectionDistributionStrategy $connectionDistributionStrategy,
        public readonly string $hostname,
        public readonly ?CarbonImmutable $createdAt,
        public readonly array $applicationIds,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? [];
        $relationships = $data['relationships'] ?? [];

        return new static(
            id: $data['id'] ?? '',
            name: $attrs['name'] ?? '',
            type: WebsocketServerType::from($attrs['type'] ?? 'reverb'),
            region: $attrs['region'] ?? '',
            status: WebsocketServerStatus::from($attrs['status'] ?? 'unknown'),
            maxConnections: WebsocketServerMaxConnection::from((int) ($attrs['max_connections'] ?? 100)),
            connectionDistributionStrategy: WebsocketServerConnectionDistributionStrategy::from($attrs['connection_distribution_strategy'] ?? 'evenly'),
            hostname: $attrs['hostname'] ?? '',
            createdAt: isset($attrs['created_at']) ? CarbonImmutable::parse($attrs['created_at']) : null,
            applicationIds: collect($relationships['applications']['data'] ?? [])->pluck('id')->all(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type->value,
            'region' => $this->region,
            'status' => $this->status->value,
            'max_connections' => $this->maxConnections->value,
            'connection_distribution_strategy' => $this->connectionDistributionStrategy->value,
            'hostname' => $this->hostname,
            'created_at' => $this->createdAt?->toIso8601String(),
            'application_ids' => $this->applicationIds,
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
