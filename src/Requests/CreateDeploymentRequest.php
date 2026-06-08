<?php

namespace Foundry\LaravelCloudClient\Requests;

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
