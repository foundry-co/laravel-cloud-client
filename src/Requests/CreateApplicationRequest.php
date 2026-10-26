<?php

namespace FoundryCo\LaravelCloudClient\Requests;

class CreateApplicationRequest extends CloudRequest
{
    public function __construct(
        public readonly string $repository,
        public readonly string $name,
        public readonly string $region,
    ) {}

    public function toArray(): array
    {
        return [
            'repository' => $this->repository,
            'name' => $this->name,
            'region' => $this->region,
        ];
    }
}
