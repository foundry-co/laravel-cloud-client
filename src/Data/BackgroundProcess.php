<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use FoundryCo\LaravelCloudClient\Concerns\DeletesItself;
use FoundryCo\LaravelCloudClient\Concerns\UpdatesItself;
use FoundryCo\LaravelCloudClient\Resources\BackgroundProcessesResource;

class BackgroundProcess extends Model
{
    use DeletesItself, UpdatesItself;

    protected static string $resource = BackgroundProcessesResource::class;

    public function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly int $processes,
        public readonly string $command,
        public readonly array $config,
        public readonly string $strategyType,
        public readonly ?int $strategyThreshold,
        public readonly ?CarbonImmutable $createdAt,
        public readonly ?string $instanceId,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? [];
        $relationships = $data['relationships'] ?? [];

        return new static(
            id: Cast::string($data['id'] ?? null),
            type: Cast::string($attrs['type'] ?? null),
            processes: Cast::int($attrs['processes'] ?? null),
            command: Cast::string($attrs['command'] ?? null),
            config: Cast::array($attrs['config'] ?? null),
            strategyType: Cast::string($attrs['strategy_type'] ?? null),
            strategyThreshold: Cast::nullableInt($attrs['strategy_threshold'] ?? null),
            createdAt: Cast::date($attrs['created_at'] ?? null),
            instanceId: Cast::relation($relationships, 'instance'),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'processes' => $this->processes,
            'command' => $this->command,
            'config' => $this->config,
            'strategy_type' => $this->strategyType,
            'strategy_threshold' => $this->strategyThreshold,
            'created_at' => $this->createdAt?->toIso8601String(),
            'instance_id' => $this->instanceId,
        ];
    }
}
