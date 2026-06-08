<?php

namespace Foundry\LaravelCloudClient\Requests;

class CreateObjectStorageBucketRequest extends CloudRequest
{
    public function __construct(
        public readonly string $name,
        public readonly string $region,
        public readonly string $visibility = 'private',
        public readonly string $jurisdiction = 'default',
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'region' => $this->region,
            'visibility' => $this->visibility,
            'jurisdiction' => $this->jurisdiction,
        ];
    }
}
