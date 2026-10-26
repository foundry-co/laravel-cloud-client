<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Concerns\CreatesRecords;
use FoundryCo\LaravelCloudClient\Concerns\DeletesRecords;
use FoundryCo\LaravelCloudClient\Concerns\FindsRecords;
use FoundryCo\LaravelCloudClient\Concerns\ListsRecords;
use FoundryCo\LaravelCloudClient\Concerns\UpdatesRecords;
use FoundryCo\LaravelCloudClient\Data\BackgroundProcess;
use Illuminate\Support\Collection;

/**
 * @method Collection<int, BackgroundProcess> list()
 * @method BackgroundProcess find(string $id)
 * @method BackgroundProcess create(string $type, ?string $command = null, int $processes = 1, ?array $config = null)
 * @method BackgroundProcess update(string $id, ?string $type = null, ?int $processes = null, ?string $command = null, ?array $config = null)
 * @method void delete(string $id)
 */
class BackgroundProcessesResource extends Resource
{
    use CreatesRecords, DeletesRecords, FindsRecords, ListsRecords, UpdatesRecords;

    protected string $model = BackgroundProcess::class;

    protected string $collection = '/instances/{parent}/background-processes';

    protected string $item = '/background-processes/{id}';

    protected array $creating = ['type', 'command'];

    protected array $defaults = ['processes' => 1];
}
