<?php

namespace Foundry\LaravelCloudClient\Requests;

class CreateWebSocketApplicationRequest extends CloudRequest
{
    public function __construct(
        public readonly string $clusterId,
        public readonly string $name,
        public readonly array $allowedOrigins = [],
        public readonly int $pingInterval = 30,
        public readonly int $activityTimeout = 30,
        public readonly int $maxMessageSize = 10240,
        public readonly int $maxConnections = 1000,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'allowed_origins' => $this->allowedOrigins,
            'ping_interval' => $this->pingInterval,
            'activity_timeout' => $this->activityTimeout,
            'max_message_size' => $this->maxMessageSize,
            'max_connections' => $this->maxConnections,
        ];
    }
}
