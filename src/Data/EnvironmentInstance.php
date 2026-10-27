<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use FoundryCo\LaravelCloudClient\Concerns\DeletesItself;
use FoundryCo\LaravelCloudClient\Concerns\UpdatesItself;
use FoundryCo\LaravelCloudClient\Resources\BackgroundProcessesResource;
use FoundryCo\LaravelCloudClient\Resources\InstancesResource;
use FoundryCo\LaravelCloudClient\Resources\ScheduledCommandsResource;
use Illuminate\Support\Collection;

class EnvironmentInstance extends Model
{
    use DeletesItself, UpdatesItself;

    protected static string $resource = InstancesResource::class;

    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $type,
        public readonly string $size,
        public readonly string $scalingType,
        public readonly int $minReplicas,
        public readonly int $maxReplicas,
        public readonly mixed $queueStatus,
        public readonly ?bool $paused,
        public readonly ?string $retireAt,
        public readonly ?string $retiredAt,
        public readonly ?bool $isDefault,
        public readonly ?int $visibilityTimeout,
        public readonly ?int $pollingInterval,
        public readonly ?int $shutdownTimeout,
        public readonly bool $usesScheduler,
        public readonly ?bool $sleepWithApp,
        public readonly ?int $hibernationTimeout,
        public readonly ?bool $usesOctane,
        public readonly ?bool $usesInertiaSsr,
        public readonly ?array $probes,
        public readonly ?int $scalingCpuThresholdPercentage,
        public readonly ?int $scalingMemoryThresholdPercentage,
        public readonly ?CarbonImmutable $createdAt,
        public readonly ?string $environmentId,
        public readonly array $backgroundProcessesIds,
        public readonly array $scheduledCommandsIds,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? [];
        $relationships = $data['relationships'] ?? [];

        return new static(
            id: Cast::string($data['id'] ?? null),
            name: Cast::string($attrs['name'] ?? null),
            type: Cast::string($attrs['type'] ?? null),
            size: Cast::string($attrs['size'] ?? null),
            scalingType: Cast::string($attrs['scaling_type'] ?? null),
            minReplicas: Cast::int($attrs['min_replicas'] ?? null),
            maxReplicas: Cast::int($attrs['max_replicas'] ?? null),
            queueStatus: $attrs['queue_status'] ?? null,
            paused: Cast::nullableBool($attrs['paused'] ?? null),
            retireAt: Cast::nullableString($attrs['retire_at'] ?? null),
            retiredAt: Cast::nullableString($attrs['retired_at'] ?? null),
            isDefault: Cast::nullableBool($attrs['is_default'] ?? null),
            visibilityTimeout: Cast::nullableInt($attrs['visibility_timeout'] ?? null),
            pollingInterval: Cast::nullableInt($attrs['polling_interval'] ?? null),
            shutdownTimeout: Cast::nullableInt($attrs['shutdown_timeout'] ?? null),
            usesScheduler: Cast::bool($attrs['uses_scheduler'] ?? null),
            sleepWithApp: Cast::nullableBool($attrs['sleep_with_app'] ?? null),
            hibernationTimeout: Cast::nullableInt($attrs['hibernation_timeout'] ?? null),
            usesOctane: Cast::nullableBool($attrs['uses_octane'] ?? null),
            usesInertiaSsr: Cast::nullableBool($attrs['uses_inertia_ssr'] ?? null),
            probes: Cast::nullableArray($attrs['probes'] ?? null),
            scalingCpuThresholdPercentage: Cast::nullableInt($attrs['scaling_cpu_threshold_percentage'] ?? null),
            scalingMemoryThresholdPercentage: Cast::nullableInt($attrs['scaling_memory_threshold_percentage'] ?? null),
            createdAt: Cast::date($attrs['created_at'] ?? null),
            environmentId: Cast::relation($relationships, 'environment'),
            backgroundProcessesIds: Cast::relations($relationships, 'backgroundProcesses'),
            scheduledCommandsIds: Cast::relations($relationships, 'scheduledCommands'),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'size' => $this->size,
            'scaling_type' => $this->scalingType,
            'min_replicas' => $this->minReplicas,
            'max_replicas' => $this->maxReplicas,
            'queue_status' => $this->queueStatus,
            'paused' => $this->paused,
            'retire_at' => $this->retireAt,
            'retired_at' => $this->retiredAt,
            'is_default' => $this->isDefault,
            'visibility_timeout' => $this->visibilityTimeout,
            'polling_interval' => $this->pollingInterval,
            'shutdown_timeout' => $this->shutdownTimeout,
            'uses_scheduler' => $this->usesScheduler,
            'sleep_with_app' => $this->sleepWithApp,
            'hibernation_timeout' => $this->hibernationTimeout,
            'uses_octane' => $this->usesOctane,
            'uses_inertia_ssr' => $this->usesInertiaSsr,
            'probes' => $this->probes,
            'scaling_cpu_threshold_percentage' => $this->scalingCpuThresholdPercentage,
            'scaling_memory_threshold_percentage' => $this->scalingMemoryThresholdPercentage,
            'created_at' => $this->createdAt?->toIso8601String(),
            'environment_id' => $this->environmentId,
            'background_processes_ids' => $this->backgroundProcessesIds,
            'scheduled_commands_ids' => $this->scheduledCommandsIds,
        ];
    }

    public function backgroundProcesses(): BackgroundProcessesResource
    {
        return $this->children(BackgroundProcessesResource::class);
    }

    public function scheduledCommands(): ScheduledCommandsResource
    {
        return $this->children(ScheduledCommandsResource::class);
    }

    public function pause(): static
    {
        return $this->resource()->pause($this->id);
    }

    public function resume(): static
    {
        return $this->resource()->resume($this->id);
    }

    public function purge(): static
    {
        return $this->resource()->purge($this->id);
    }

    public function setDefault(): static
    {
        return $this->resource()->setDefault($this->id);
    }

    public function failedJobs(): Collection
    {
        return $this->resource()->failedJobs($this->id);
    }

    public function deleteFailedJobs(?array $jobIds = null, ?string $failedBefore = null): void
    {
        $this->resource()->deleteFailedJobs($this->id, $jobIds, $failedBefore);
    }

    public function deleteFailedJob(string $jobId): void
    {
        $this->resource()->deleteFailedJob($this->id, $jobId);
    }

    public function retryFailedJob(string $jobId): void
    {
        $this->resource()->retryFailedJob($this->id, $jobId);
    }
}
