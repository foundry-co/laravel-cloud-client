<?php

namespace Foundry\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class BackgroundProcess implements Arrayable, Jsonable
{
    public function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly int $processes,
        public readonly string $command,
        public readonly ?array $config,
        public readonly ?string $strategyType,
        public readonly ?int $strategyThreshold,
        public readonly ?CarbonImmutable $createdAt,
        public readonly ?string $instanceId,
        public readonly ?string $queue,
        public readonly ?string $connection,
        public readonly ?int $timeout,
        public readonly ?int $sleep,
        public readonly ?int $tries,
        public readonly ?int $backoff,
        public readonly ?int $rest,
        public readonly ?bool $force,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? [];
        $relationships = $data['relationships'] ?? [];

        return new static(
            id: $data['id'] ?? '',
            type: $attrs['type'] ?? '',
            processes: (int) ($attrs['processes'] ?? 1),
            command: $attrs['command'] ?? '',
            config: $attrs['config'] ?? null,
            strategyType: $attrs['strategy_type'] ?? null,
            strategyThreshold: isset($attrs['strategy_threshold']) ? (int) $attrs['strategy_threshold'] : null,
            createdAt: isset($attrs['created_at']) ? CarbonImmutable::parse($attrs['created_at']) : null,
            instanceId: $relationships['instance']['data']['id'] ?? null,
            queue: $attrs['queue'] ?? null,
            connection: $attrs['connection'] ?? null,
            timeout: isset($attrs['timeout']) ? (int) $attrs['timeout'] : null,
            sleep: isset($attrs['sleep']) ? (int) $attrs['sleep'] : null,
            tries: isset($attrs['tries']) ? (int) $attrs['tries'] : null,
            backoff: isset($attrs['backoff']) ? (int) $attrs['backoff'] : null,
            rest: isset($attrs['rest']) ? (int) $attrs['rest'] : null,
            force: isset($attrs['force']) ? (bool) $attrs['force'] : null,
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
            'queue' => $this->queue,
            'connection' => $this->connection,
            'timeout' => $this->timeout,
            'sleep' => $this->sleep,
            'tries' => $this->tries,
            'backoff' => $this->backoff,
            'rest' => $this->rest,
            'force' => $this->force,
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
