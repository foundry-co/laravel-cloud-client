<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Concerns\FindsRecords;
use FoundryCo\LaravelCloudClient\Concerns\ListsRecords;
use FoundryCo\LaravelCloudClient\Data\Command;
use Illuminate\Support\Collection;

/**
 * @method Collection<int, Command> list(?string $status = null, ?string $command = null)
 * @method Command find(string $id)
 */
class CommandsResource extends Resource
{
    use FindsRecords, ListsRecords;

    protected string $model = Command::class;

    protected string $collection = '/environments/{parent}/commands';

    protected string $item = '/commands/{id}';

    public function run(string $command): Command
    {
        return $this->hydrate($this->client->post($this->path('/environments/{parent}/commands'), ['command' => $command]));
    }
}
