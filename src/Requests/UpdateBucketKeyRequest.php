<?php

namespace Foundry\LaravelCloudClient\Requests;

class UpdateBucketKeyRequest extends CloudRequest
{
    public function __construct(
        public readonly string $keyId,
        public readonly ?string $name = null,
        public readonly ?string $permission = null,
    ) {}

    public function toArray(): array
    {
        return $this->filter([
            'name' => $this->name,
            'permission' => $this->permission,
        ]);
    }
}
