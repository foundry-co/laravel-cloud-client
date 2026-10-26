<?php

namespace FoundryCo\LaravelCloudClient\Requests;

class CreateDeploymentRequest extends CloudRequest
{
    public function __construct(
        public readonly string $environmentId,
    ) {}

    public function toArray(): array
    {
        return [];
    }
}
