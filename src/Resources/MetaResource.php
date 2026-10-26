<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Data\Organization;
use FoundryCo\LaravelCloudClient\Data\Region;
use Illuminate\Support\Collection;

class MetaResource extends Resource
{
    public function organization(): Organization
    {
        return Organization::createFromResponse($this->client->get('/meta/organization'));
    }

    public function regions(): Collection
    {
        $body = $this->client->get('/meta/regions');

        return collect($body['data'] ?? $body)->map(fn ($region) => Region::createFromResponse($region));
    }
}
