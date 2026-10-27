<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use FoundryCo\LaravelCloudClient\Concerns\DeletesItself;
use FoundryCo\LaravelCloudClient\Concerns\UpdatesItself;
use FoundryCo\LaravelCloudClient\Resources\DomainsResource;

class Domain extends Model
{
    use DeletesItself, UpdatesItself;

    protected static string $resource = DomainsResource::class;

    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $type,
        public readonly string $stage,
        public readonly string $hostnameStatus,
        public readonly string $sslStatus,
        public readonly string $originStatus,
        public readonly mixed $redirect,
        public readonly mixed $cloudflareStrategy,
        public readonly ?bool $downtime,
        public readonly bool $wildcardEnabled,
        public readonly array $dnsRecords,
        public readonly ?array $wildcard,
        public readonly ?array $www,
        public readonly mixed $actionRequired,
        public readonly ?CarbonImmutable $lastVerifiedAt,
        public readonly ?CarbonImmutable $createdAt,
        public readonly ?string $environmentId,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? [];
        $relationships = $data['relationships'] ?? [];

        return new static(
            id: Cast::string($data['id'] ?? null),
            name: Cast::string($attrs['name'] ?? null),
            type: Cast::string($attrs['type'] ?? null),
            stage: Cast::string($attrs['stage'] ?? null),
            hostnameStatus: Cast::string($attrs['hostname_status'] ?? null),
            sslStatus: Cast::string($attrs['ssl_status'] ?? null),
            originStatus: Cast::string($attrs['origin_status'] ?? null),
            redirect: $attrs['redirect'] ?? null,
            cloudflareStrategy: $attrs['cloudflare_strategy'] ?? null,
            downtime: Cast::nullableBool($attrs['downtime'] ?? null),
            wildcardEnabled: Cast::bool($attrs['wildcard_enabled'] ?? null),
            dnsRecords: Cast::array($attrs['dns_records'] ?? null),
            wildcard: Cast::nullableArray($attrs['wildcard'] ?? null),
            www: Cast::nullableArray($attrs['www'] ?? null),
            actionRequired: $attrs['action_required'] ?? null,
            lastVerifiedAt: Cast::date($attrs['last_verified_at'] ?? null),
            createdAt: Cast::date($attrs['created_at'] ?? null),
            environmentId: Cast::relation($relationships, 'environment'),
        );
    }

    public function isPrimary(): bool
    {
        return $this->type === 'root';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'stage' => $this->stage,
            'hostname_status' => $this->hostnameStatus,
            'ssl_status' => $this->sslStatus,
            'origin_status' => $this->originStatus,
            'redirect' => $this->redirect,
            'cloudflare_strategy' => $this->cloudflareStrategy,
            'downtime' => $this->downtime,
            'wildcard_enabled' => $this->wildcardEnabled,
            'dns_records' => $this->dnsRecords,
            'wildcard' => $this->wildcard,
            'www' => $this->www,
            'action_required' => $this->actionRequired,
            'last_verified_at' => $this->lastVerifiedAt?->toIso8601String(),
            'created_at' => $this->createdAt?->toIso8601String(),
            'environment_id' => $this->environmentId,
        ];
    }

    public function verify(): static
    {
        return $this->resource()->verify($this->id);
    }
}
