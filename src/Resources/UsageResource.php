<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Data\Usage;

class UsageResource extends Resource
{
    public function get(int $period = 0, ?string $environment = null): Usage
    {
        return Usage::createFromResponse($this->client->get('/usage', $this->withoutNulls([
            'period' => $period ?: null,
            'environment' => $environment,
        ])));
    }
}
