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
        public readonly int $currentSpendCents,
        public readonly int $resourcesTotalCostCents,
        public readonly int $addonsTotalCostCents,
        public readonly int $applicationCount,
        public readonly int $applicationsTotalCostCents,
        public readonly ?CarbonImmutable $lastUpdatedAt,
        public readonly array $raw,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $summary = $response['data']['summary'] ?? $response['summary'] ?? $response;

        return new static(
            currency: $summary['currency'] ?? 'USD',
            period: (int) ($summary['period'] ?? 0),
            currentSpendCents: (int) ($summary['current_spend_cents'] ?? 0),
            resourcesTotalCostCents: (int) ($summary['resources_total_cost_cents'] ?? 0),
            addonsTotalCostCents: (int) ($summary['addons_total_cost_cents'] ?? 0),
            applicationCount: (int) ($summary['application_count'] ?? 0),
            applicationsTotalCostCents: (int) ($summary['applications_total_cost_cents'] ?? 0),
            lastUpdatedAt: isset($summary['last_updated_at']) ? CarbonImmutable::parse($summary['last_updated_at']) : null,
            raw: $response,
        );
    }

    public function toArray(): array
    {
        return [
            'currency' => $this->currency,
            'period' => $this->period,
            'current_spend_cents' => $this->currentSpendCents,
            'resources_total_cost_cents' => $this->resourcesTotalCostCents,
            'addons_total_cost_cents' => $this->addonsTotalCostCents,
            'application_count' => $this->applicationCount,
            'applications_total_cost_cents' => $this->applicationsTotalCostCents,
            'last_updated_at' => $this->lastUpdatedAt?->toIso8601String(),
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
