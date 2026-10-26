<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Concerns\CreatesRecords;
use FoundryCo\LaravelCloudClient\Concerns\DeletesRecords;
use FoundryCo\LaravelCloudClient\Concerns\FindsRecords;
use FoundryCo\LaravelCloudClient\Concerns\ListsRecords;
use FoundryCo\LaravelCloudClient\Data\Database;
use Illuminate\Support\Collection;

/**
 * @method Collection<int, Database> list()
 * @method Database find(string $id)
 * @method Database create(string $name)
 * @method void delete(string $id)
 */
class DatabasesResource extends Resource
{
    use CreatesRecords, DeletesRecords, FindsRecords, ListsRecords;

    protected string $model = Database::class;

    protected string $collection = '/databases/clusters/{parent}/databases';

    protected string $item = '/databases/clusters/{parent}/databases/{id}';

    protected array $creating = ['name'];
}
