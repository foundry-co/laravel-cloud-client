<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class WebsocketApplication implements Arrayable, Jsonable
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $appId,
        public readonly array $allowedOrigins,
        public readonly int $pingInterval,
        public readonly int $activityTimeout,
        public readonly int $maxMessageSize,
        public readonly int $maxConnections,
        public readonly string $key,
        public readonly string $secret,
        public readonly ?CarbonImmutable $createdAt,
        public readonly ?string $serverId,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? [];
        $relationships = $data['relationships'] ?? [];

        return new static(
            id: $data['id'] ?? '',
            name: $attrs['name'] ?? '',
            appId: $attrs['app_id'] ?? '',
            allowedOrigins: $attrs['allowed_origins'] ?? [],
            pingInterval: (int) ($attrs['ping_interval'] ?? 30),
            activityTimeout: (int) ($attrs['activity_timeout'] ?? 30),
            maxMessageSize: (int) ($attrs['max_message_size'] ?? 10240),
            maxConnections: (int) ($attrs['max_connections'] ?? 1000),
            key: $attrs['key'] ?? '',
            secret: $attrs['secret'] ?? '',
            createdAt: isset($attrs['created_at']) ? CarbonImmutable::parse($attrs['created_at']) : null,
            serverId: $relationships['server']['data']['id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'app_id' => $this->appId,
            'allowed_origins' => $this->allowedOrigins,
            'ping_interval' => $this->pingInterval,
            'activity_timeout' => $this->activityTimeout,
            'max_message_size' => $this->maxMessageSize,
            'max_connections' => $this->maxConnections,
            'key' => $this->key,
            'secret' => $this->secret,
            'created_at' => $this->createdAt?->toIso8601String(),
            'server_id' => $this->serverId,
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
