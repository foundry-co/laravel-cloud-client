<?php

namespace Foundry\LaravelCloudClient\Resources;

use Foundry\LaravelCloudClient\Data\Organization;
use Foundry\LaravelCloudClient\Data\Region;
use Foundry\LaravelCloudClient\Http\GetRequest;
use Illuminate\Support\Collection;

class MetaResource extends Resource
{
    public function organization(): Organization
    {
        $response = $this->send(new GetRequest('/organization'));

        return Organization::createFromResponse($response->json());
    }

    public function regions(): Collection
    {
        $response = $this->send(new GetRequest('/regions'));
        $data = $response->json();

        return collect($data['data'] ?? $data)->map(fn ($item) => Region::createFromResponse($item));
    }
}
