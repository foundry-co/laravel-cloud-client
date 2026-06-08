<?php

namespace Foundry\LaravelCloudClient\Requests;

class CreateBackgroundProcessRequest extends CloudRequest
{
    public function __construct(
        public readonly string $instanceId,
        public readonly string $type,
        public readonly string $command,
        public readonly int $processes = 1,
        public readonly ?array $config = null,
    ) {}

    public function toArray(): array
    {
        return $this->filter([
            'type' => $this->type,
            'command' => $this->command,
            'processes' => $this->processes,
            'config' => $this->config,
        ]);
    }
}
