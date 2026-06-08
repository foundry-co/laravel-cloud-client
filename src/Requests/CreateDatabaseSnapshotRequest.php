<?php

namespace Foundry\LaravelCloudClient\Requests;

class CreateDatabaseSnapshotRequest extends CloudRequest
{
    public function __construct(
        public readonly string $clusterId,
        public readonly ?string $name = null,
    ) {}

    public function toArray(): array
    {
        return $this->filter([
            'name' => $this->name,
        ]);
    }
}
