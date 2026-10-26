<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use FoundryCo\LaravelCloudClient\Enums\DeploymentStatus;
use FoundryCo\LaravelCloudClient\Resources\DeploymentsResource;

class Deployment extends Model
{
    protected static string $resource = DeploymentsResource::class;

    public function __construct(
        public readonly string $id,
        public readonly string $slug,
        public readonly ?DeploymentStatus $status,
        public readonly string $branchName,
        public readonly string $commitHash,
        public readonly string $commitMessage,
        public readonly ?string $commitAuthor,
        public readonly ?string $failureReason,
        public readonly string $phpMajorVersion,
        public readonly ?string $buildCommand,
        public readonly string $nodeVersion,
        public readonly bool $usesOctane,
        public readonly bool $usesHibernation,
        public readonly ?int $hibernationWakeUpInterval,
        public readonly ?CarbonImmutable $startedAt,
        public readonly ?CarbonImmutable $finishedAt,
        public readonly ?string $environmentId,
        public readonly ?string $initiatorId,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? [];
        $relationships = $data['relationships'] ?? [];

        return new static(
            id: $data['id'] ?? '',
            slug: $attrs['slug'] ?? '',
            status: DeploymentStatus::tryFrom($attrs['status'] ?? ''),
            branchName: $attrs['branch_name'] ?? '',
            commitHash: $attrs['commit_hash'] ?? '',
            commitMessage: $attrs['commit_message'] ?? '',
            commitAuthor: $attrs['commit_author'] ?? null,
            failureReason: $attrs['failure_reason'] ?? null,
            phpMajorVersion: $attrs['php_major_version'] ?? '',
            buildCommand: $attrs['build_command'] ?? null,
            nodeVersion: $attrs['node_version'] ?? '',
            usesOctane: (bool) ($attrs['uses_octane'] ?? false),
            usesHibernation: (bool) ($attrs['uses_hibernation'] ?? false),
            hibernationWakeUpInterval: isset($attrs['hibernation_wake_up_interval']) ? (int) $attrs['hibernation_wake_up_interval'] : null,
            startedAt: isset($attrs['started_at']) ? CarbonImmutable::parse($attrs['started_at']) : null,
            finishedAt: isset($attrs['finished_at']) ? CarbonImmutable::parse($attrs['finished_at']) : null,
            environmentId: $relationships['environment']['data']['id'] ?? null,
            initiatorId: $relationships['initiator']['data']['id'] ?? null,
        );
    }

    public function succeeded(): bool
    {
        return $this->status === DeploymentStatus::DeploymentSucceeded;
    }

    public function failed(): bool
    {
        return in_array($this->status, [
            DeploymentStatus::Failed,
            DeploymentStatus::BuildFailed,
            DeploymentStatus::DeploymentFailed,
            DeploymentStatus::Cancelled,
        ]);
    }

    public function isFinished(): bool
    {
        return $this->succeeded() || $this->failed();
    }

    public function isInProgress(): bool
    {
        return in_array($this->status, [
            DeploymentStatus::BuildRunning,
            DeploymentStatus::DeploymentRunning,
            DeploymentStatus::BuildQueued,
            DeploymentStatus::DeploymentQueued,
        ]);
    }

    public function isPending(): bool
    {
        return in_array($this->status, [
            DeploymentStatus::Pending,
            DeploymentStatus::BuildPending,
            DeploymentStatus::BuildCreated,
            DeploymentStatus::DeploymentPending,
            DeploymentStatus::DeploymentCreated,
        ]);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'status' => $this->status?->value,
            'branch_name' => $this->branchName,
            'commit_hash' => $this->commitHash,
            'commit_message' => $this->commitMessage,
            'commit_author' => $this->commitAuthor,
            'failure_reason' => $this->failureReason,
            'php_major_version' => $this->phpMajorVersion,
            'build_command' => $this->buildCommand,
            'node_version' => $this->nodeVersion,
            'uses_octane' => $this->usesOctane,
            'uses_hibernation' => $this->usesHibernation,
            'hibernation_wake_up_interval' => $this->hibernationWakeUpInterval,
            'started_at' => $this->startedAt?->toIso8601String(),
            'finished_at' => $this->finishedAt?->toIso8601String(),
            'environment_id' => $this->environmentId,
            'initiator_id' => $this->initiatorId,
        ];
    }

    public function logs(): array
    {
        return $this->resource()->logs($this->id);
    }
}
