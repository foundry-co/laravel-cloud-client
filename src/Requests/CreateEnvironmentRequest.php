<?php

namespace FoundryCo\LaravelCloudClient\Requests;

class CreateEnvironmentRequest extends CloudRequest
{
    public function __construct(
        public readonly string $applicationId,
        public readonly string $name,
        public readonly string $branch,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'branch' => $this->branch,
        ];
    }
}
