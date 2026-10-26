<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use FoundryCo\LaravelCloudClient\Enums\CommandStatus;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Command implements Arrayable, Jsonable
{
    public function __construct(
        public readonly string $id,
        public readonly string $command,
        public readonly CommandStatus $status,
        public readonly ?string $output,
        public readonly ?int $exitCode,
        public readonly ?CarbonImmutable $startedAt,
        public readonly ?CarbonImmutable $finishedAt,
        public readonly ?CarbonImmutable $createdAt,
        public readonly ?CarbonImmutable $updatedAt,
        public readonly ?string $environmentId,
        public readonly ?string $instanceId,
        public readonly ?string $initiatorId,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? [];
        $relationships = $data['relationships'] ?? [];

        return new static(
            id: $data['id'] ?? '',
            command: $attrs['command'] ?? '',
            status: CommandStatus::from($attrs['status'] ?? 'pending'),
            output: $attrs['output'] ?? null,
            exitCode: isset($attrs['exit_code']) ? (int) $attrs['exit_code'] : null,
            startedAt: isset($attrs['started_at']) ? CarbonImmutable::parse($attrs['started_at']) : null,
            finishedAt: isset($attrs['finished_at']) ? CarbonImmutable::parse($attrs['finished_at']) : null,
            createdAt: isset($attrs['created_at']) ? CarbonImmutable::parse($attrs['created_at']) : null,
            updatedAt: isset($attrs['updated_at']) ? CarbonImmutable::parse($attrs['updated_at']) : null,
            environmentId: $relationships['environment']['data']['id'] ?? null,
            instanceId: $relationships['instance']['data']['id'] ?? null,
            initiatorId: $relationships['initiator']['data']['id'] ?? null,
        );
    }

    public function isFinished(): bool
    {
        return in_array($this->status, [
            CommandStatus::CommandSuccess,
            CommandStatus::CommandFailure,
        ]);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'command' => $this->command,
            'status' => $this->status->value,
            'output' => $this->output,
            'exit_code' => $this->exitCode,
            'started_at' => $this->startedAt?->toIso8601String(),
            'finished_at' => $this->finishedAt?->toIso8601String(),
            'created_at' => $this->createdAt?->toIso8601String(),
            'updated_at' => $this->updatedAt?->toIso8601String(),
            'environment_id' => $this->environmentId,
            'instance_id' => $this->instanceId,
            'initiator_id' => $this->initiatorId,
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
