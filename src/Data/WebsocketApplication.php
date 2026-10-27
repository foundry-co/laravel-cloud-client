<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use FoundryCo\LaravelCloudClient\Concerns\DeletesItself;
use FoundryCo\LaravelCloudClient\Concerns\UpdatesItself;
use FoundryCo\LaravelCloudClient\Resources\WebSocketApplicationsResource;

class WebsocketApplication extends Model
{
    use DeletesItself, UpdatesItself;

    protected static string $resource = WebSocketApplicationsResource::class;

    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $appId,
        public readonly array $allowedOrigins,
        public readonly int $pingInterval,
        public readonly int $activityTimeout,
        public readonly int $maxMessageSize,
        public readonly int $maxConnections,
        public readonly ?CarbonImmutable $createdAt,
        public readonly ?string $key,
        public readonly ?string $secret,
        public readonly ?string $serverId,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? [];
        $relationships = $data['relationships'] ?? [];

        return new static(
            id: Cast::string($data['id'] ?? null),
            name: Cast::string($attrs['name'] ?? null),
            appId: Cast::string($attrs['app_id'] ?? null),
            allowedOrigins: Cast::array($attrs['allowed_origins'] ?? null),
            pingInterval: Cast::int($attrs['ping_interval'] ?? null),
            activityTimeout: Cast::int($attrs['activity_timeout'] ?? null),
            maxMessageSize: Cast::int($attrs['max_message_size'] ?? null),
            maxConnections: Cast::int($attrs['max_connections'] ?? null),
            createdAt: Cast::date($attrs['created_at'] ?? null),
            key: Cast::nullableString($attrs['key'] ?? null),
            secret: Cast::nullableString($attrs['secret'] ?? null),
            serverId: Cast::relation($relationships, 'server'),
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
            'created_at' => $this->createdAt?->toIso8601String(),
            'key' => $this->key,
            'secret' => $this->secret,
            'server_id' => $this->serverId,
        ];
    }

    public function metrics(?string $period = null): array
    {
        return $this->resource()->metrics($this->id, $period);
    }
}
