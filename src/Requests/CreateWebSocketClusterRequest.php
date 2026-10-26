<?php

namespace FoundryCo\LaravelCloudClient\Requests;

class CreateWebSocketClusterRequest extends CloudRequest
{
    public function __construct(
        public readonly string $name,
        public readonly string $region,
        public readonly string $type = 'reverb',
        public readonly int $maxConnections = 1000,
        public readonly string $connectionDistributionStrategy = 'evenly',
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'region' => $this->region,
            'type' => $this->type,
            'max_connections' => $this->maxConnections,
            'connection_distribution_strategy' => $this->connectionDistributionStrategy,
        ];
    }
}
