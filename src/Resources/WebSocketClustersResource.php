<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Concerns\CreatesRecords;
use FoundryCo\LaravelCloudClient\Concerns\DeletesRecords;
use FoundryCo\LaravelCloudClient\Concerns\FindsRecords;
use FoundryCo\LaravelCloudClient\Concerns\ListsRecords;
use FoundryCo\LaravelCloudClient\Concerns\UpdatesRecords;
use FoundryCo\LaravelCloudClient\Data\WebsocketCluster;
use Illuminate\Support\Collection;

/**
 * @method Collection<int, WebsocketCluster> list(?string $type = null, ?string $region = null, ?string $status = null)
 * @method WebsocketCluster find(string $id)
 * @method WebsocketCluster create(string $name, string $region, string $type = 'reverb', int $maxConnections = 100)
 * @method WebsocketCluster update(string $id, ?string $name = null, ?int $maxConnections = null)
 * @method void delete(string $id)
 */
class WebSocketClustersResource extends Resource
{
    use CreatesRecords, DeletesRecords, FindsRecords, ListsRecords, UpdatesRecords;

    protected string $model = WebsocketCluster::class;

    protected string $collection = '/websocket-servers';

    protected string $item = '/websocket-servers/{id}';

    protected array $creating = ['name', 'region'];

    protected array $defaults = ['type' => 'reverb', 'max_connections' => 100];

    public function types(): array
    {
        return $this->client->get('/websocket-servers/types')['data'] ?? [];
    }

    public function metrics(string $id, ?string $period = null): array
    {
        return $this->client->get("/websocket-servers/{$id}/metrics", $this->withoutNulls(['period' => $period]));
    }
}
