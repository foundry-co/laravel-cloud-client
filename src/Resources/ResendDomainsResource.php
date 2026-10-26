<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Concerns\ListsRecords;
use FoundryCo\LaravelCloudClient\Data\ResendDomain;
use Illuminate\Support\Collection;

/**
 * @method Collection<int, ResendDomain> list(?string $name = null, ?string $status = null)
 */
class ResendDomainsResource extends Resource
{
    use ListsRecords;

    protected string $model = ResendDomain::class;

    protected string $collection = '/resend/domains';
}
