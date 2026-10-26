<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Usage implements Arrayable, Jsonable
{
    public function __construct(
        public readonly string $currency,
        public readonly int $period,
        public readonly array $availablePeriods,
        public readonly ?CarbonImmutable $lastUpdatedAt,
        public readonly int $currentSpendCents,
        public readonly ?array $bandwidth,
        public readonly ?array $credits,
        public readonly ?array $alert,
        public readonly array $resources,
        public readonly array $addons,
        public readonly array $applicationTotals,
        public readonly ?array $environmentUsage,
        public readonly ?array $privateCloud,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? [];
        $meta = $response['meta'] ?? [];
        $summary = $data['summary'] ?? [];

        return new static(
            currency: $meta['currency'] ?? 'USD',
            period: (int) ($meta['period'] ?? 0),
            availablePeriods: $meta['available_periods'] ?? [],
            lastUpdatedAt: isset($meta['last_updated_at']) ? CarbonImmutable::parse($meta['last_updated_at']) : null,
            currentSpendCents: (int) ($summary['current_spend_cents'] ?? 0),
            bandwidth: $summary['bandwidth'] ?? null,
            credits: $summary['credits'] ?? null,
            alert: $summary['alert'] ?? null,
            resources: $data['resources'] ?? [],
            addons: $data['addons'] ?? [],
            applicationTotals: $data['application_totals'] ?? [],
            environmentUsage: $data['environment_usage'] ?? null,
            privateCloud: $data['private_cloud'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'currency' => $this->currency,
            'period' => $this->period,
            'available_periods' => $this->availablePeriods,
            'last_updated_at' => $this->lastUpdatedAt?->toIso8601String(),
            'current_spend_cents' => $this->currentSpendCents,
            'bandwidth' => $this->bandwidth,
            'credits' => $this->credits,
            'alert' => $this->alert,
            'resources' => $this->resources,
            'addons' => $this->addons,
            'application_totals' => $this->applicationTotals,
            'environment_usage' => $this->environmentUsage,
            'private_cloud' => $this->privateCloud,
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
