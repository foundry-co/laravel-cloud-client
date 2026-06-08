<?php

namespace Foundry\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Application implements Arrayable, Jsonable
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly string $region,
        public readonly ?string $repositoryFullName,
        public readonly ?string $repositoryBranch,
        public readonly ?string $slackChannel,
        public readonly ?string $avatarUrl,
        public readonly ?CarbonImmutable $createdAt,
        public readonly ?string $organizationId,
        public readonly ?string $defaultEnvironmentId,
        public readonly array $environmentIds,
        public readonly ?Organization $organization,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? [];
        $relationships = $data['relationships'] ?? [];
        $included = $response['included'] ?? [];

        $organizationId = $relationships['organization']['data']['id'] ?? null;

        $organization = null;
        if ($organizationId) {
            $orgData = collect($included)->first(fn ($item) => $item['type'] === 'organizations' && $item['id'] === $organizationId);
            if ($orgData) {
                $organization = Organization::createFromResponse(['data' => $orgData]);
            }
        }

        $environmentIds = collect($relationships['environments']['data'] ?? [])->pluck('id')->all();
        $defaultEnvironmentId = $relationships['defaultEnvironment']['data']['id'] ?? null;

        return new static(
            id: $data['id'] ?? '',
            name: $attrs['name'] ?? '',
            slug: $attrs['slug'] ?? '',
            region: $attrs['region'] ?? '',
            repositoryFullName: $attrs['repository_full_name'] ?? null,
            repositoryBranch: $attrs['repository_branch'] ?? null,
            slackChannel: $attrs['slack_channel'] ?? null,
            avatarUrl: $attrs['avatar_url'] ?? null,
            createdAt: isset($attrs['created_at']) ? CarbonImmutable::parse($attrs['created_at']) : null,
            organizationId: $organizationId,
            defaultEnvironmentId: $defaultEnvironmentId,
            environmentIds: $environmentIds,
            organization: $organization,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'region' => $this->region,
            'repository_full_name' => $this->repositoryFullName,
            'repository_branch' => $this->repositoryBranch,
            'slack_channel' => $this->slackChannel,
            'avatar_url' => $this->avatarUrl,
            'created_at' => $this->createdAt?->toIso8601String(),
            'organization_id' => $this->organizationId,
            'default_environment_id' => $this->defaultEnvironmentId,
            'environment_ids' => $this->environmentIds,
            'organization' => $this->organization?->toArray(),
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
