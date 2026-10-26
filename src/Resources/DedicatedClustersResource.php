<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Data\DedicatedCluster;
use FoundryCo\LaravelCloudClient\Http\GetRequest;
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
