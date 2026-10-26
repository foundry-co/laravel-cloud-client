<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Environment implements Arrayable, Jsonable
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $status,
        public readonly ?string $branch,
        public readonly ?string $buildCommand,
        public readonly ?string $deployCommand,
        public readonly string $vanityDomain,
        public readonly string $phpMajorVersion,
        public readonly ?string $nodeVersion,
        public readonly bool $usesOctane,
        public readonly bool $usesHibernation,
        public readonly bool $usesPushToDeploy,
        public readonly bool $usesDeployHook,
        public readonly array $environmentVariables,
        public readonly array $networkSettings,
        public readonly ?CarbonImmutable $createdAt,
        public readonly ?CarbonImmutable $updatedAt,
        public readonly ?string $applicationId,
        public readonly array $deploymentIds,
        public readonly ?string $currentDeploymentId,
        public readonly array $domainIds,
        public readonly ?string $primaryDomainId,
        public readonly ?string $databaseSchemaId,
        public readonly ?string $cacheId,
        public readonly ?string $websocketApplicationId,
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
            status: $attrs['status'] ?? null,
            branch: $attrs['branch'] ?? null,
            buildCommand: $attrs['build_command'] ?? null,
            deployCommand: $attrs['deploy_command'] ?? null,
            vanityDomain: $attrs['vanity_domain'] ?? '',
            phpMajorVersion: $attrs['php_major_version'] ?? '',
            nodeVersion: $attrs['node_version'] ?? null,
            usesOctane: (bool) ($attrs['uses_octane'] ?? false),
            usesHibernation: (bool) ($attrs['uses_hibernation'] ?? false),
            usesPushToDeploy: (bool) ($attrs['uses_push_to_deploy'] ?? false),
            usesDeployHook: (bool) ($attrs['uses_deploy_hook'] ?? false),
            environmentVariables: $attrs['environment_variables'] ?? [],
            networkSettings: $attrs['network_settings'] ?? [],
            createdAt: isset($attrs['created_at']) ? CarbonImmutable::parse($attrs['created_at']) : null,
            updatedAt: isset($attrs['updated_at']) ? CarbonImmutable::parse($attrs['updated_at']) : null,
            applicationId: $relationships['application']['data']['id'] ?? null,
            deploymentIds: collect($relationships['deployments']['data'] ?? [])->pluck('id')->all(),
            currentDeploymentId: $relationships['currentDeployment']['data']['id'] ?? null,
            domainIds: collect($relationships['domains']['data'] ?? [])->pluck('id')->all(),
            primaryDomainId: $relationships['primaryDomain']['data']['id'] ?? null,
            databaseSchemaId: $relationships['databaseSchema']['data']['id'] ?? null,
            cacheId: $relationships['cache']['data']['id'] ?? null,
            websocketApplicationId: $relationships['websocketApplication']['data']['id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'status' => $this->status,
            'branch' => $this->branch,
            'build_command' => $this->buildCommand,
            'deploy_command' => $this->deployCommand,
            'vanity_domain' => $this->vanityDomain,
            'php_major_version' => $this->phpMajorVersion,
            'node_version' => $this->nodeVersion,
            'uses_octane' => $this->usesOctane,
            'uses_hibernation' => $this->usesHibernation,
            'uses_push_to_deploy' => $this->usesPushToDeploy,
            'uses_deploy_hook' => $this->usesDeployHook,
            'environment_variables' => $this->environmentVariables,
            'network_settings' => $this->networkSettings,
            'created_at' => $this->createdAt?->toIso8601String(),
            'updated_at' => $this->updatedAt?->toIso8601String(),
            'application_id' => $this->applicationId,
            'deployment_ids' => $this->deploymentIds,
            'current_deployment_id' => $this->currentDeploymentId,
            'domain_ids' => $this->domainIds,
            'primary_domain_id' => $this->primaryDomainId,
            'database_schema_id' => $this->databaseSchemaId,
            'cache_id' => $this->cacheId,
            'websocket_application_id' => $this->websocketApplicationId,
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
