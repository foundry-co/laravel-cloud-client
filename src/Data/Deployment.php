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
            id: Cast::string($data['id'] ?? null),
            slug: Cast::string($attrs['slug'] ?? null),
            status: Cast::enum(DeploymentStatus::class, $attrs['status'] ?? null),
            branchName: Cast::string($attrs['branch_name'] ?? null),
            commitHash: Cast::string($attrs['commit_hash'] ?? null),
            commitMessage: Cast::string($attrs['commit_message'] ?? null),
            commitAuthor: Cast::nullableString($attrs['commit_author'] ?? null),
            failureReason: Cast::nullableString($attrs['failure_reason'] ?? null),
            phpMajorVersion: Cast::string($attrs['php_major_version'] ?? null),
            buildCommand: Cast::nullableString($attrs['build_command'] ?? null),
            nodeVersion: Cast::string($attrs['node_version'] ?? null),
            usesOctane: Cast::bool($attrs['uses_octane'] ?? null),
            usesHibernation: Cast::bool($attrs['uses_hibernation'] ?? null),
            hibernationWakeUpInterval: Cast::nullableInt($attrs['hibernation_wake_up_interval'] ?? null),
            startedAt: Cast::date($attrs['started_at'] ?? null),
            finishedAt: Cast::date($attrs['finished_at'] ?? null),
            environmentId: Cast::relation($relationships, 'environment'),
            initiatorId: Cast::relation($relationships, 'initiator'),
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
