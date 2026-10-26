<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Concerns\CreatesRecords;
use FoundryCo\LaravelCloudClient\Concerns\DeletesRecords;
use FoundryCo\LaravelCloudClient\Concerns\FindsRecords;
use FoundryCo\LaravelCloudClient\Concerns\ListsRecords;
use FoundryCo\LaravelCloudClient\Data\DatabaseSnapshot;
use Illuminate\Support\Collection;

/**
 * @method Collection<int, DatabaseSnapshot> list()
 * @method DatabaseSnapshot find(string $id)
 * @method DatabaseSnapshot create(string $name, ?string $description = null)
 * @method void delete(string $id)
 */
class DatabaseSnapshotsResource extends Resource
{
    use CreatesRecords, DeletesRecords, FindsRecords, ListsRecords;

    protected string $model = DatabaseSnapshot::class;

    protected string $collection = '/databases/clusters/{parent}/snapshots';

    protected string $item = '/database-snapshots/{id}';

    protected array $creating = ['name'];
}
