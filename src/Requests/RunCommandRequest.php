<?php

namespace Foundry\LaravelCloudClient\Requests;

class RunCommandRequest extends CloudRequest
{
    public function __construct(
        public readonly string $environmentId,
        public readonly string $command,
    ) {}

    public function toArray(): array
    {
        return [
            'command' => $this->command,
        ];
    }
}
