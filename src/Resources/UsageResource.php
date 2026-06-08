<?php

namespace Foundry\LaravelCloudClient\Resources;

use Foundry\LaravelCloudClient\Data\Usage;
use Foundry\LaravelCloudClient\Http\GetRequest;

class UsageResource extends Resource
{
    public function get(int $period = 0, ?string $environment = null): Usage
    {
        $response = $this->send(new GetRequest('/usage', array_filter([
            'period' => $period ?: null,
            'environment' => $environment,
        ], fn ($v) => $v !== null)));

        return Usage::createFromResponse($response->json());
    }
}
