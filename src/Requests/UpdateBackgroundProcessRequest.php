<?php

namespace Foundry\LaravelCloudClient\Requests;

class UpdateBackgroundProcessRequest extends CloudRequest
{
    public function __construct(
        public readonly string $backgroundProcessId,
        public readonly ?string $command = null,
        public readonly ?int $processes = null,
        public readonly ?array $config = null,
    ) {}

    public function toArray(): array
    {
        return $this->filter([
            'command' => $this->command,
            'processes' => $this->processes,
            'config' => $this->config,
        ]);
    }
}
