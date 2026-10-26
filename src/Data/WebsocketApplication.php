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
            pingInterval: (int) ($attrs['ping_interval'] ?? 0),
            activityTimeout: (int) ($attrs['activity_timeout'] ?? 0),
            maxMessageSize: (int) ($attrs['max_message_size'] ?? 0),
            maxConnections: (int) ($attrs['max_connections'] ?? 0),
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
            'created_at' => $this->createdAt?->toIso8601String(),
            'server_id' => $this->serverId,
        ];
    }

    public function metrics(?string $period = null): array
    {
        return $this->resource()->metrics($this->id, $period);
    }
}
