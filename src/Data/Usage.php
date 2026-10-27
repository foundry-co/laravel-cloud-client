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
        $data = Cast::array($response['data'] ?? null);
        $meta = Cast::array($response['meta'] ?? null);
        $summary = Cast::array($data['summary'] ?? null);

        return new static(
            currency: Cast::nullableString($meta['currency'] ?? null) ?? 'USD',
            period: Cast::int($meta['period'] ?? null),
            availablePeriods: Cast::array($meta['available_periods'] ?? null),
            lastUpdatedAt: Cast::date($meta['last_updated_at'] ?? null),
            currentSpendCents: Cast::int($summary['current_spend_cents'] ?? null),
            bandwidth: Cast::nullableArray($summary['bandwidth'] ?? null),
            credits: Cast::nullableArray($summary['credits'] ?? null),
            alert: Cast::nullableArray($summary['alert'] ?? null),
            resources: Cast::array($data['resources'] ?? null),
            addons: Cast::array($data['addons'] ?? null),
            applicationTotals: Cast::array($data['application_totals'] ?? null),
            environmentUsage: Cast::nullableArray($data['environment_usage'] ?? null),
            privateCloud: Cast::nullableArray($data['private_cloud'] ?? null),
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
