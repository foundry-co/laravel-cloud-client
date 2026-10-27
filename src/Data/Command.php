<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use FoundryCo\LaravelCloudClient\Enums\CommandStatus;
use FoundryCo\LaravelCloudClient\Resources\CommandsResource;

class Command extends Model
{
    protected static string $resource = CommandsResource::class;

    public function __construct(
        public readonly string $id,
        public readonly string $command,
        public readonly ?string $output,
        public readonly bool $outputTruncated,
        public readonly ?string $outputUrl,
        public readonly ?CommandStatus $status,
        public readonly mixed $exitCode,
        public readonly ?string $failureReason,
        public readonly ?CarbonImmutable $startedAt,
        public readonly ?CarbonImmutable $finishedAt,
        public readonly ?CarbonImmutable $createdAt,
        public readonly ?string $environmentId,
        public readonly ?string $deploymentId,
        public readonly ?string $initiatorId,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? [];
        $relationships = $data['relationships'] ?? [];

        return new static(
            id: Cast::string($data['id'] ?? null),
            command: Cast::string($attrs['command'] ?? null),
            output: Cast::nullableString($attrs['output'] ?? null),
            outputTruncated: Cast::bool($attrs['output_truncated'] ?? null),
            outputUrl: Cast::nullableString($attrs['output_url'] ?? null),
            status: Cast::enum(CommandStatus::class, $attrs['status'] ?? null),
            exitCode: $attrs['exit_code'] ?? null,
            failureReason: Cast::nullableString($attrs['failure_reason'] ?? null),
            startedAt: Cast::date($attrs['started_at'] ?? null),
            finishedAt: Cast::date($attrs['finished_at'] ?? null),
            createdAt: Cast::date($attrs['created_at'] ?? null),
            environmentId: Cast::relation($relationships, 'environment'),
            deploymentId: Cast::relation($relationships, 'deployment'),
            initiatorId: Cast::relation($relationships, 'initiator'),
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
            'output' => $this->output,
            'output_truncated' => $this->outputTruncated,
            'output_url' => $this->outputUrl,
            'status' => $this->status?->value,
            'exit_code' => $this->exitCode,
            'failure_reason' => $this->failureReason,
            'started_at' => $this->startedAt?->toIso8601String(),
            'finished_at' => $this->finishedAt?->toIso8601String(),
            'created_at' => $this->createdAt?->toIso8601String(),
            'environment_id' => $this->environmentId,
            'deployment_id' => $this->deploymentId,
            'initiator_id' => $this->initiatorId,
        ];
    }
}
