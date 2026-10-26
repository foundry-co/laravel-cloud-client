<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Concerns\ListsRecords;
use FoundryCo\LaravelCloudClient\Data\DedicatedCluster;
use Illuminate\Support\Collection;

/**
 * @method Collection<int, DedicatedCluster> list(?string $region = null, ?string $type = null, ?string $status = null)
 */
class DedicatedClustersResource extends Resource
{
    use ListsRecords;

    protected string $model = DedicatedCluster::class;

    protected string $collection = '/dedicated-clusters';
}
