<?php

namespace Foundry\LaravelCloudClient\Resources;

use Foundry\LaravelCloudClient\Data\DedicatedCluster;
use Foundry\LaravelCloudClient\Http\GetRequest;
use Illuminate\Support\Collection;

class DedicatedClustersResource extends Resource
{
    public function list(): Collection
    {
        return $this->paginateAll(
            new GetRequest('/dedicated-servers'),
            fn ($item) => DedicatedCluster::createFromResponse($item)
        );
    }
}
