<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Concerns\CreatesRecords;
use FoundryCo\LaravelCloudClient\Concerns\DeletesRecords;
use FoundryCo\LaravelCloudClient\Concerns\FindsRecords;
use FoundryCo\LaravelCloudClient\Concerns\ListsRecords;
use FoundryCo\LaravelCloudClient\Concerns\UpdatesRecords;
use FoundryCo\LaravelCloudClient\Data\ObjectStorageBucket;
use Illuminate\Support\Collection;

/**
 * @method Collection<int, ObjectStorageBucket> list(?string $type = null, ?string $status = null, ?string $visibility = null)
 * @method ObjectStorageBucket find(string $id)
 * @method ObjectStorageBucket create(string $name, string $keyName, string $visibility = 'private', string $jurisdiction = 'default', mixed $locationHint = null, ?array $allowedOrigins = null, ?array $corsSettings = null, string $keyPermission = 'read_write')
 * @method ObjectStorageBucket update(string $id, ?string $name = null, ?string $visibility = null, ?array $allowedOrigins = null, ?array $corsSettings = null)
 * @method void delete(string $id)
 */
class ObjectStorageBucketsResource extends Resource
{
    use CreatesRecords, DeletesRecords, FindsRecords, ListsRecords, UpdatesRecords;

    protected string $model = ObjectStorageBucket::class;

    protected string $collection = '/buckets';

    protected string $item = '/buckets/{id}';

    protected array $creating = ['name'];

    protected array $defaults = ['visibility' => 'private', 'jurisdiction' => 'default', 'key_permission' => 'read_write'];
}
