<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Concerns\ListsRecords;
use FoundryCo\LaravelCloudClient\Data\EdgeNetwork;
use Illuminate\Support\Collection;

/**
 * @method Collection<int, EdgeNetwork> list(?string $name = null, ?string $domain = null, ?string $status = null)
 */
class EdgeNetworksResource extends Resource
{
    use ListsRecords;

    protected string $model = EdgeNetwork::class;

    protected string $collection = '/edge-networks';
}
