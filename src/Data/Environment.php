<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use FoundryCo\LaravelCloudClient\Concerns\DeletesItself;
use FoundryCo\LaravelCloudClient\Concerns\UpdatesItself;
use FoundryCo\LaravelCloudClient\Enums\EnvironmentStatus;
use FoundryCo\LaravelCloudClient\Resources\CommandsResource;
use FoundryCo\LaravelCloudClient\Resources\DeploymentsResource;
use FoundryCo\LaravelCloudClient\Resources\DomainsResource;
use FoundryCo\LaravelCloudClient\Resources\EnvironmentsResource;
use FoundryCo\LaravelCloudClient\Resources\InstancesResource;
use Illuminate\Support\Collection;

class Environment extends Model
{
    use DeletesItself, UpdatesItself;

    protected static string $resource = EnvironmentsResource::class;

    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?EnvironmentStatus $status,
        public readonly string $color,
        public readonly bool $createdFromAutomation,
        public readonly string $vanityDomain,
        public readonly string $framework,
        public readonly string $language,
        public readonly string $phpMajorVersion,
        public readonly ?string $serving,
        public readonly ?string $buildCommand,
        public readonly string $nodeVersion,
        public readonly ?string $deployCommand,
        public readonly ?string $startCommand,
        public readonly bool $usesOctane,
        public readonly bool $usesHibernation,
        public readonly ?int $hibernationWakeUpInterval,
        public readonly int $timeout,
        public readonly int $shutdownTimeout,
        public readonly bool $usesPushToDeploy,
        public readonly bool $usesDeployHook,
        public readonly ?array $resend,
        public readonly array $networkSettings,
        public readonly ?CarbonImmutable $createdAt,
        public readonly ?string $applicationId,
        public readonly ?string $branchId,
        public readonly array $deploymentsIds,
        public readonly ?string $currentDeploymentId,
        public readonly array $domainsIds,
        public readonly ?string $primaryDomainId,
        public readonly array $instancesIds,
        public readonly ?string $databaseId,
        public readonly ?string $cacheId,
        public readonly array $bucketsIds,
        public readonly ?string $websocketApplicationId,
        public readonly array $secretsIds,
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
            status: EnvironmentStatus::tryFrom($attrs['status'] ?? ''),
            color: $attrs['color'] ?? '',
            createdFromAutomation: (bool) ($attrs['created_from_automation'] ?? false),
            vanityDomain: $attrs['vanity_domain'] ?? '',
            framework: $attrs['framework'] ?? '',
            language: $attrs['language'] ?? '',
            phpMajorVersion: $attrs['php_major_version'] ?? '',
            serving: $attrs['serving'] ?? null,
            buildCommand: $attrs['build_command'] ?? null,
            nodeVersion: $attrs['node_version'] ?? '',
            deployCommand: $attrs['deploy_command'] ?? null,
            startCommand: $attrs['start_command'] ?? null,
            usesOctane: (bool) ($attrs['uses_octane'] ?? false),
            usesHibernation: (bool) ($attrs['uses_hibernation'] ?? false),
            hibernationWakeUpInterval: isset($attrs['hibernation_wake_up_interval']) ? (int) $attrs['hibernation_wake_up_interval'] : null,
            timeout: (int) ($attrs['timeout'] ?? 0),
            shutdownTimeout: (int) ($attrs['shutdown_timeout'] ?? 0),
            usesPushToDeploy: (bool) ($attrs['uses_push_to_deploy'] ?? false),
            usesDeployHook: (bool) ($attrs['uses_deploy_hook'] ?? false),
            resend: $attrs['resend'] ?? null,
            networkSettings: $attrs['network_settings'] ?? [],
            createdAt: isset($attrs['created_at']) ? CarbonImmutable::parse($attrs['created_at']) : null,
            applicationId: $relationships['application']['data']['id'] ?? null,
            branchId: $relationships['branch']['data']['id'] ?? null,
            deploymentsIds: collect($relationships['deployments']['data'] ?? [])->pluck('id')->all(),
            currentDeploymentId: $relationships['currentDeployment']['data']['id'] ?? null,
            domainsIds: collect($relationships['domains']['data'] ?? [])->pluck('id')->all(),
            primaryDomainId: $relationships['primaryDomain']['data']['id'] ?? null,
            instancesIds: collect($relationships['instances']['data'] ?? [])->pluck('id')->all(),
            databaseId: $relationships['database']['data']['id'] ?? null,
            cacheId: $relationships['cache']['data']['id'] ?? null,
            bucketsIds: collect($relationships['buckets']['data'] ?? [])->pluck('id')->all(),
            websocketApplicationId: $relationships['websocketApplication']['data']['id'] ?? null,
            secretsIds: collect($relationships['secrets']['data'] ?? [])->pluck('id')->all(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'status' => $this->status?->value,
            'color' => $this->color,
            'created_from_automation' => $this->createdFromAutomation,
            'vanity_domain' => $this->vanityDomain,
            'framework' => $this->framework,
            'language' => $this->language,
            'php_major_version' => $this->phpMajorVersion,
            'serving' => $this->serving,
            'build_command' => $this->buildCommand,
            'node_version' => $this->nodeVersion,
            'deploy_command' => $this->deployCommand,
            'start_command' => $this->startCommand,
            'uses_octane' => $this->usesOctane,
            'uses_hibernation' => $this->usesHibernation,
            'hibernation_wake_up_interval' => $this->hibernationWakeUpInterval,
            'timeout' => $this->timeout,
            'shutdown_timeout' => $this->shutdownTimeout,
            'uses_push_to_deploy' => $this->usesPushToDeploy,
            'uses_deploy_hook' => $this->usesDeployHook,
            'resend' => $this->resend,
            'network_settings' => $this->networkSettings,
            'created_at' => $this->createdAt?->toIso8601String(),
            'application_id' => $this->applicationId,
            'branch_id' => $this->branchId,
            'deployments_ids' => $this->deploymentsIds,
            'current_deployment_id' => $this->currentDeploymentId,
            'domains_ids' => $this->domainsIds,
            'primary_domain_id' => $this->primaryDomainId,
            'instances_ids' => $this->instancesIds,
            'database_id' => $this->databaseId,
            'cache_id' => $this->cacheId,
            'buckets_ids' => $this->bucketsIds,
            'websocket_application_id' => $this->websocketApplicationId,
            'secrets_ids' => $this->secretsIds,
        ];
    }

    public function domains(): DomainsResource
    {
        return $this->children(DomainsResource::class);
    }

    public function instances(): InstancesResource
    {
        return $this->children(InstancesResource::class);
    }

    public function deployments(): DeploymentsResource
    {
        return $this->children(DeploymentsResource::class);
    }

    public function commands(): CommandsResource
    {
        return $this->children(CommandsResource::class);
    }

    public function deploy(): Deployment
    {
        return $this->deployments()->initiate();
    }

    public function run(string $command): Command
    {
        return $this->commands()->run($command);
    }

    public function start(?bool $redeploy = null): Deployment
    {
        return $this->resource()->start($this->id, $redeploy);
    }

    public function stop(): static
    {
        return $this->resource()->stop($this->id);
    }

    public function purgeEdgeCache(?string $path = null, ?string $prefix = null, ?string $tag = null): static
    {
        return $this->resource()->purgeEdgeCache($this->id, $path, $prefix, $tag);
    }

    public function addVariables(array $variables): static
    {
        return $this->resource()->addVariables($this->id, $variables);
    }

    public function replaceVariables(array $variables): static
    {
        return $this->resource()->replaceVariables($this->id, $variables);
    }

    public function deleteVariables(array $keys): static
    {
        return $this->resource()->deleteVariables($this->id, $keys);
    }

    public function metrics(?string $period = null): array
    {
        return $this->resource()->metrics($this->id, $period);
    }

    public function logs(string $from, string $to, ?string $cursor = null, ?string $type = null, ?string $query = null, ?bool $caseSensitive = null, ?bool $wholeWord = null, ?array $instances = null): Collection
    {
        return $this->resource()->logs($this->id, $from, $to, $cursor, $type, $query, $caseSensitive, $wholeWord, $instances);
    }

    public function updateVanityDomain(string $name): static
    {
        return $this->resource()->updateVanityDomain($this->id, $name);
    }

    public function attachSecrets(array $secrets): static
    {
        return $this->resource()->attachSecrets($this->id, $secrets);
    }

    public function detachSecret(string $secretId): void
    {
        $this->resource()->detachSecret($this->id, $secretId);
    }

    public function attachResend(string $fromAddress, string $fromName, ?string $keyStrategy = null, ?string $keyName = null, ?string $reuseKeyId = null): static
    {
        return $this->resource()->attachResend($this->id, $fromAddress, $fromName, $keyStrategy, $keyName, $reuseKeyId);
    }

    public function updateResend(string $fromAddress, string $fromName): static
    {
        return $this->resource()->updateResend($this->id, $fromAddress, $fromName);
    }

    public function detachResend(): void
    {
        $this->resource()->detachResend($this->id);
    }

    public function resendKeys(): array
    {
        return $this->resource()->resendKeys($this->id);
    }
}
