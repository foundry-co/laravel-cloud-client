<?php

namespace Foundry\LaravelCloudClient\Requests;

class CreateDatabaseRestoreRequest extends CloudRequest
{
    public function __construct(
        public readonly string $snapshotId,
        public readonly ?string $clusterId = null,
    ) {}

    public function toArray(): array
    {
        return $this->filter([
            'cluster_id' => $this->clusterId,
        ]);
    }
}
