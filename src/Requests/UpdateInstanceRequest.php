<?php

namespace Foundry\LaravelCloudClient\Requests;

class UpdateInstanceRequest extends CloudRequest
{
    public function __construct(
        public readonly string $instanceId,
        public readonly ?string $name = null,
        public readonly ?string $size = null,
        public readonly ?string $scalingType = null,
        public readonly ?int $minReplicas = null,
        public readonly ?int $maxReplicas = null,
        public readonly ?bool $usesScheduler = null,
        public readonly ?int $scalingCpuThresholdPercentage = null,
        public readonly ?int $scalingMemoryThresholdPercentage = null,
        public readonly ?int $visibilityTimeout = null,
        public readonly ?int $pollingInterval = null,
    ) {}

    public function toArray(): array
    {
        return $this->filter([
            'name' => $this->name,
            'size' => $this->size,
            'scaling_type' => $this->scalingType,
            'min_replicas' => $this->minReplicas,
            'max_replicas' => $this->maxReplicas,
            'uses_scheduler' => $this->usesScheduler,
            'scaling_cpu_threshold_percentage' => $this->scalingCpuThresholdPercentage,
            'scaling_memory_threshold_percentage' => $this->scalingMemoryThresholdPercentage,
            'visibility_timeout' => $this->visibilityTimeout,
            'polling_interval' => $this->pollingInterval,
        ]);
    }
}
