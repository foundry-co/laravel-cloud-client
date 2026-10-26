<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Concerns\CreatesRecords;
use FoundryCo\LaravelCloudClient\Concerns\DeletesRecords;
use FoundryCo\LaravelCloudClient\Concerns\FindsRecords;
use FoundryCo\LaravelCloudClient\Concerns\ListsRecords;
use FoundryCo\LaravelCloudClient\Concerns\UpdatesRecords;
use FoundryCo\LaravelCloudClient\Data\ScheduledCommand;
use Illuminate\Support\Collection;

/**
 * @method Collection<int, ScheduledCommand> list()
 * @method ScheduledCommand find(string $id)
 * @method ScheduledCommand create(string $command, string $schedule, ?string $name = null)
 * @method ScheduledCommand update(string $id, ?string $name = null, ?string $command = null, ?string $schedule = null)
 * @method void delete(string $id)
 */
class ScheduledCommandsResource extends Resource
{
    use CreatesRecords, DeletesRecords, FindsRecords, ListsRecords, UpdatesRecords;

    protected string $model = ScheduledCommand::class;

    protected string $collection = '/instances/{parent}/scheduled-commands';

    protected string $item = '/scheduled-commands/{id}';

    protected array $creating = ['command', 'schedule'];
}
