<?php

namespace FoundryCo\LaravelCloudClient\Requests;

class CreateDatabaseClusterRequest extends CloudRequest
{
    public function __construct(
        public readonly string $type,
        public readonly string $name,
        public readonly string $region,
        public readonly array $config,
        public readonly ?int $clusterId = null,
    ) {}

    public function toArray(): array
    {
        return $this->filter([
            'type' => $this->type,
            'name' => $this->name,
            'region' => $this->region,
            'config' => $this->config,
            'cluster_id' => $this->clusterId,
        ]);
    }
}
