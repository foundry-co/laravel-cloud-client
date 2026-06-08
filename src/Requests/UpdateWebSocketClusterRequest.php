<?php

namespace Foundry\LaravelCloudClient\Requests;

class UpdateWebSocketClusterRequest extends CloudRequest
{
    public function __construct(
        public readonly string $clusterId,
        public readonly ?string $name = null,
        public readonly ?int $maxConnections = null,
        public readonly ?string $connectionDistributionStrategy = null,
    ) {}

    public function toArray(): array
    {
        return $this->filter([
            'name' => $this->name,
            'max_connections' => $this->maxConnections,
            'connection_distribution_strategy' => $this->connectionDistributionStrategy,
        ]);
    }
}
