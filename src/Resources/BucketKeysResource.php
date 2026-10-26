<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Concerns\CreatesRecords;
use FoundryCo\LaravelCloudClient\Concerns\DeletesRecords;
use FoundryCo\LaravelCloudClient\Concerns\FindsRecords;
use FoundryCo\LaravelCloudClient\Concerns\ListsRecords;
use FoundryCo\LaravelCloudClient\Concerns\UpdatesRecords;
use FoundryCo\LaravelCloudClient\Data\BucketKey;
use Illuminate\Support\Collection;

/**
 * @method Collection<int, BucketKey> list()
 * @method BucketKey find(string $id)
 * @method BucketKey create(string $name, string $permission = 'read_write')
 * @method BucketKey update(string $id, string $name)
 * @method void delete(string $id)
 */
class BucketKeysResource extends Resource
{
    use CreatesRecords, DeletesRecords, FindsRecords, ListsRecords, UpdatesRecords;

    protected string $model = BucketKey::class;

    protected string $collection = '/buckets/{parent}/keys';

    protected string $item = '/bucket-keys/{id}';

    protected array $creating = ['name'];

    protected array $defaults = ['permission' => 'read_write'];
}
