<?php

namespace Foundry\LaravelCloudClient\Requests;

class CreateBucketKeyRequest extends CloudRequest
{
    public function __construct(
        public readonly string $bucketId,
        public readonly string $name,
        public readonly string $permission = 'read_write',
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'permission' => $this->permission,
        ];
    }
}
