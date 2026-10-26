<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use FoundryCo\LaravelCloudClient\Enums\DeploymentStatus;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Deployment implements Arrayable, Jsonable
{
    public function __construct(
        public readonly string $id,
        public readonly DeploymentStatus $status,
        public readonly ?string $commitHash,
        public readonly ?string $commitMessage,
        public readonly ?string $commitAuthor,
        public readonly ?CarbonImmutable $startedAt,
        public readonly ?CarbonImmutable $finishedAt,
        public readonly ?CarbonImmutable $createdAt,
        public readonly ?CarbonImmutable $updatedAt,
        public readonly ?string $failureReason,
        public readonly string $branchName,
        public readonly string $phpMajorVersion,
        public readonly ?string $buildCommand,
        public readonly ?string $nodeVersion,
        public readonly bool $usesOctane,
        public readonly bool $usesHibernation,
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
            status: DeploymentStatus::from($attrs['status'] ?? 'pending'),
            commitHash: $attrs['commit_hash'] ?? null,
            commitMessage: $attrs['commit_message'] ?? null,
            commitAuthor: $attrs['commit_author'] ?? null,
            startedAt: isset($attrs['started_at']) ? CarbonImmutable::parse($attrs['started_at']) : null,
            finishedAt: isset($attrs['finished_at']) ? CarbonImmutable::parse($attrs['finished_at']) : null,
            createdAt: isset($attrs['created_at']) ? CarbonImmutable::parse($attrs['created_at']) : null,
            updatedAt: isset($attrs['updated_at']) ? CarbonImmutable::parse($attrs['updated_at']) : null,
            failureReason: $attrs['failure_reason'] ?? null,
            branchName: $attrs['branch_name'] ?? '',
            phpMajorVersion: $attrs['php_major_version'] ?? '',
            buildCommand: $attrs['build_command'] ?? null,
            nodeVersion: $attrs['node_version'] ?? null,
            usesOctane: (bool) ($attrs['uses_octane'] ?? false),
            usesHibernation: (bool) ($attrs['uses_hibernation'] ?? false),
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
            'status' => $this->status->value,
            'commit_hash' => $this->commitHash,
            'commit_message' => $this->commitMessage,
            'commit_author' => $this->commitAuthor,
            'started_at' => $this->startedAt?->toIso8601String(),
            'finished_at' => $this->finishedAt?->toIso8601String(),
            'created_at' => $this->createdAt?->toIso8601String(),
            'updated_at' => $this->updatedAt?->toIso8601String(),
            'failure_reason' => $this->failureReason,
            'branch_name' => $this->branchName,
            'php_major_version' => $this->phpMajorVersion,
            'build_command' => $this->buildCommand,
            'node_version' => $this->nodeVersion,
            'uses_octane' => $this->usesOctane,
            'uses_hibernation' => $this->usesHibernation,
            'environment_id' => $this->environmentId,
            'initiator_id' => $this->initiatorId,
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
