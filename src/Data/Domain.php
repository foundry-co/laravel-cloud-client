<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Domain implements Arrayable, Jsonable
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $type,
        public readonly string $hostnameStatus,
        public readonly string $sslStatus,
        public readonly string $originStatus,
        public readonly ?string $redirect,
        public readonly array $dnsRecords,
        public readonly ?array $wildcard,
        public readonly ?array $www,
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
            id: $data['id'] ?? '',
            name: $attrs['name'] ?? '',
            type: $attrs['type'] ?? '',
            hostnameStatus: $attrs['hostname_status'] ?? '',
            sslStatus: $attrs['ssl_status'] ?? '',
            originStatus: $attrs['origin_status'] ?? '',
            redirect: $attrs['redirect'] ?? null,
            dnsRecords: $attrs['dns_records'] ?? [],
            wildcard: $attrs['wildcard'] ?? null,
            www: $attrs['www'] ?? null,
            lastVerifiedAt: isset($attrs['last_verified_at']) ? CarbonImmutable::parse($attrs['last_verified_at']) : null,
            createdAt: isset($attrs['created_at']) ? CarbonImmutable::parse($attrs['created_at']) : null,
            environmentId: $relationships['environment']['data']['id'] ?? null,
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
            'hostname_status' => $this->hostnameStatus,
            'ssl_status' => $this->sslStatus,
            'origin_status' => $this->originStatus,
            'redirect' => $this->redirect,
            'dns_records' => $this->dnsRecords,
            'wildcard' => $this->wildcard,
            'www' => $this->www,
            'last_verified_at' => $this->lastVerifiedAt?->toIso8601String(),
            'created_at' => $this->createdAt?->toIso8601String(),
            'environment_id' => $this->environmentId,
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
