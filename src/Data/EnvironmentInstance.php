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
            id: $data['id'] ?? '',
            name: $attrs['name'] ?? '',
            type: $attrs['type'] ?? '',
            size: $attrs['size'] ?? '',
            scalingType: $attrs['scaling_type'] ?? '',
            minReplicas: (int) ($attrs['min_replicas'] ?? 0),
            maxReplicas: (int) ($attrs['max_replicas'] ?? 0),
            queueStatus: $attrs['queue_status'] ?? null,
            paused: isset($attrs['paused']) ? (bool) $attrs['paused'] : null,
            retireAt: $attrs['retire_at'] ?? null,
            retiredAt: $attrs['retired_at'] ?? null,
            isDefault: isset($attrs['is_default']) ? (bool) $attrs['is_default'] : null,
            visibilityTimeout: isset($attrs['visibility_timeout']) ? (int) $attrs['visibility_timeout'] : null,
            pollingInterval: isset($attrs['polling_interval']) ? (int) $attrs['polling_interval'] : null,
            shutdownTimeout: isset($attrs['shutdown_timeout']) ? (int) $attrs['shutdown_timeout'] : null,
            usesScheduler: (bool) ($attrs['uses_scheduler'] ?? false),
            sleepWithApp: isset($attrs['sleep_with_app']) ? (bool) $attrs['sleep_with_app'] : null,
            hibernationTimeout: isset($attrs['hibernation_timeout']) ? (int) $attrs['hibernation_timeout'] : null,
            usesOctane: isset($attrs['uses_octane']) ? (bool) $attrs['uses_octane'] : null,
            usesInertiaSsr: isset($attrs['uses_inertia_ssr']) ? (bool) $attrs['uses_inertia_ssr'] : null,
            probes: $attrs['probes'] ?? null,
            scalingCpuThresholdPercentage: isset($attrs['scaling_cpu_threshold_percentage']) ? (int) $attrs['scaling_cpu_threshold_percentage'] : null,
            scalingMemoryThresholdPercentage: isset($attrs['scaling_memory_threshold_percentage']) ? (int) $attrs['scaling_memory_threshold_percentage'] : null,
            createdAt: isset($attrs['created_at']) ? CarbonImmutable::parse($attrs['created_at']) : null,
            environmentId: $relationships['environment']['data']['id'] ?? null,
            backgroundProcessesIds: collect($relationships['backgroundProcesses']['data'] ?? [])->pluck('id')->all(),
            scheduledCommandsIds: collect($relationships['scheduledCommands']['data'] ?? [])->pluck('id')->all(),
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
