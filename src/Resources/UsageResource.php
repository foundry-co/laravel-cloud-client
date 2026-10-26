<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Data\Usage;
use FoundryCo\LaravelCloudClient\Http\GetRequest;

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
