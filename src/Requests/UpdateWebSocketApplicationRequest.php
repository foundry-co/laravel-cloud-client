<?php

namespace FoundryCo\LaravelCloudClient\Requests;

class UpdateWebSocketApplicationRequest extends CloudRequest
{
    public function __construct(
        public readonly string $applicationId,
        public readonly ?string $name = null,
        public readonly ?array $allowedOrigins = null,
        public readonly ?int $pingInterval = null,
        public readonly ?int $activityTimeout = null,
        public readonly ?int $maxMessageSize = null,
        public readonly ?int $maxConnections = null,
    ) {}

    public function toArray(): array
    {
        return $this->filter([
            'name' => $this->name,
            'allowed_origins' => $this->allowedOrigins,
            'ping_interval' => $this->pingInterval,
            'activity_timeout' => $this->activityTimeout,
            'max_message_size' => $this->maxMessageSize,
            'max_connections' => $this->maxConnections,
        ]);
    }
}
