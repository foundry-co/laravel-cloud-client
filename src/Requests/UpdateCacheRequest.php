<?php

namespace FoundryCo\LaravelCloudClient\Requests;

class UpdateCacheRequest extends CloudRequest
{
    public function __construct(
        public readonly string $cacheId,
        public readonly ?string $name = null,
        public readonly ?string $size = null,
        public readonly ?bool $autoUpgradeEnabled = null,
    ) {}

    public function toArray(): array
    {
        return $this->filter([
            'name' => $this->name,
            'size' => $this->size,
            'auto_upgrade_enabled' => $this->autoUpgradeEnabled,
        ]);
    }
}
