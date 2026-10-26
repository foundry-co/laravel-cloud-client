<?php

namespace FoundryCo\LaravelCloudClient\Requests;

class UpdateDatabaseClusterRequest extends CloudRequest
{
    public function __construct(
        public readonly string $clusterId,
        public readonly ?string $name = null,
        public readonly ?array $config = null,
    ) {}

    public function toArray(): array
    {
        return $this->filter([
            'name' => $this->name,
            'config' => $this->config,
        ]);
    }
}
