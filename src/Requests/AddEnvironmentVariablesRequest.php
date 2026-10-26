<?php

namespace FoundryCo\LaravelCloudClient\Requests;

class AddEnvironmentVariablesRequest extends CloudRequest
{
    public function __construct(
        public readonly string $environmentId,
        public readonly array $variables,
    ) {}

    public function toArray(): array
    {
        return [
            'variables' => $this->variables,
        ];
    }
}
