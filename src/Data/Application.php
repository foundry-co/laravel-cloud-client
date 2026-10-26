<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use FoundryCo\LaravelCloudClient\Concerns\DeletesItself;
use FoundryCo\LaravelCloudClient\Concerns\UpdatesItself;
use FoundryCo\LaravelCloudClient\Resources\ApplicationsResource;
use FoundryCo\LaravelCloudClient\Resources\EnvironmentsResource;

class Application extends Model
{
    use DeletesItself, UpdatesItself;

    protected static string $resource = ApplicationsResource::class;

    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly string $region,
        public readonly ?string $rootDirectory,
        public readonly ?string $slackChannel,
        public readonly string $avatarUrl,
        public readonly ?string $notes,
        public readonly string $framework,
        public readonly string $language,
        public readonly ?CarbonImmutable $createdAt,
        public readonly ?array $repository,
        public readonly ?string $repositoryId,
        public readonly ?string $organizationId,
        public readonly array $environmentsIds,
        public readonly array $deploymentsIds,
        public readonly ?string $defaultEnvironmentId,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? [];
        $relationships = $data['relationships'] ?? [];

        return new static(
            id: $data['id'] ?? '',
            name: $attrs['name'] ?? '',
            slug: $attrs['slug'] ?? '',
            region: $attrs['region'] ?? '',
            rootDirectory: $attrs['root_directory'] ?? null,
            slackChannel: $attrs['slack_channel'] ?? null,
            avatarUrl: $attrs['avatar_url'] ?? '',
            notes: $attrs['notes'] ?? null,
            framework: $attrs['framework'] ?? '',
            language: $attrs['language'] ?? '',
            createdAt: isset($attrs['created_at']) ? CarbonImmutable::parse($attrs['created_at']) : null,
            repository: $attrs['repository'] ?? null,
            repositoryId: $relationships['repository']['data']['id'] ?? null,
            organizationId: $relationships['organization']['data']['id'] ?? null,
            environmentsIds: collect($relationships['environments']['data'] ?? [])->pluck('id')->all(),
            deploymentsIds: collect($relationships['deployments']['data'] ?? [])->pluck('id')->all(),
            defaultEnvironmentId: $relationships['defaultEnvironment']['data']['id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'region' => $this->region,
            'root_directory' => $this->rootDirectory,
            'slack_channel' => $this->slackChannel,
            'avatar_url' => $this->avatarUrl,
            'notes' => $this->notes,
            'framework' => $this->framework,
            'language' => $this->language,
            'created_at' => $this->createdAt?->toIso8601String(),
            'repository' => $this->repository,
            'repository_id' => $this->repositoryId,
            'organization_id' => $this->organizationId,
            'environments_ids' => $this->environmentsIds,
            'deployments_ids' => $this->deploymentsIds,
            'default_environment_id' => $this->defaultEnvironmentId,
        ];
    }

    public function environments(): EnvironmentsResource
    {
        return $this->children(EnvironmentsResource::class);
    }

    public function uploadAvatar(string $file): static
    {
        return $this->resource()->uploadAvatar($this->id, $file);
    }

    public function deleteAvatar(): void
    {
        $this->resource()->deleteAvatar($this->id);
    }
}
