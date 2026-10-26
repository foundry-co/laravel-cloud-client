<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class EnvironmentInstance implements Arrayable, Jsonable
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $type,
        public readonly string $size,
        public readonly string $scalingType,
        public readonly int $minReplicas,
        public readonly int $maxReplicas,
        public readonly bool $usesScheduler,
        public readonly ?int $scalingCpuThresholdPercentage,
        public readonly ?int $scalingMemoryThresholdPercentage,
        public readonly ?string $queueStatus,
        public readonly ?bool $paused,
        public readonly ?bool $isDefault,
        public readonly ?int $visibilityTimeout,
        public readonly ?int $pollingInterval,
        public readonly ?CarbonImmutable $createdAt,
        public readonly ?CarbonImmutable $updatedAt,
        public readonly ?string $environmentId,
        public readonly array $backgroundProcessIds,
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
            usesScheduler: (bool) ($attrs['uses_scheduler'] ?? false),
            scalingCpuThresholdPercentage: isset($attrs['scaling_cpu_threshold_percentage']) ? (int) $attrs['scaling_cpu_threshold_percentage'] : null,
            scalingMemoryThresholdPercentage: isset($attrs['scaling_memory_threshold_percentage']) ? (int) $attrs['scaling_memory_threshold_percentage'] : null,
            queueStatus: $attrs['queue_status'] ?? null,
            paused: isset($attrs['paused']) ? (bool) $attrs['paused'] : null,
            isDefault: isset($attrs['is_default']) ? (bool) $attrs['is_default'] : null,
            visibilityTimeout: isset($attrs['visibility_timeout']) ? (int) $attrs['visibility_timeout'] : null,
            pollingInterval: isset($attrs['polling_interval']) ? (int) $attrs['polling_interval'] : null,
            createdAt: isset($attrs['created_at']) ? CarbonImmutable::parse($attrs['created_at']) : null,
            updatedAt: isset($attrs['updated_at']) ? CarbonImmutable::parse($attrs['updated_at']) : null,
            environmentId: $relationships['environment']['data']['id'] ?? null,
            backgroundProcessIds: collect($relationships['backgroundProcesses']['data'] ?? [])->pluck('id')->all(),
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
            'uses_scheduler' => $this->usesScheduler,
            'scaling_cpu_threshold_percentage' => $this->scalingCpuThresholdPercentage,
            'scaling_memory_threshold_percentage' => $this->scalingMemoryThresholdPercentage,
            'queue_status' => $this->queueStatus,
            'paused' => $this->paused,
            'is_default' => $this->isDefault,
            'visibility_timeout' => $this->visibilityTimeout,
            'polling_interval' => $this->pollingInterval,
            'created_at' => $this->createdAt?->toIso8601String(),
            'updated_at' => $this->updatedAt?->toIso8601String(),
            'environment_id' => $this->environmentId,
            'background_process_ids' => $this->backgroundProcessIds,
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
