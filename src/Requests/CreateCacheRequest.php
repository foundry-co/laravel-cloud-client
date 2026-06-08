<?php

namespace Foundry\LaravelCloudClient\Requests;

class CreateCacheRequest extends CloudRequest
{
    public function __construct(
        public readonly string $type,
        public readonly string $name,
        public readonly string $region,
        public readonly string $size,
        public readonly bool $autoUpgradeEnabled = true,
    ) {}

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'name' => $this->name,
            'region' => $this->region,
            'size' => $this->size,
            'auto_upgrade_enabled' => $this->autoUpgradeEnabled,
        ];
    }
}
