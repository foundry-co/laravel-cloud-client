<?php

namespace FoundryCo\LaravelCloudClient\Requests;

class CreateDatabaseRequest extends CloudRequest
{
    public function __construct(
        public readonly string $clusterId,
        public readonly string $name,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
