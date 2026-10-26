<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Concerns\CreatesRecords;
use FoundryCo\LaravelCloudClient\Concerns\DeletesRecords;
use FoundryCo\LaravelCloudClient\Concerns\FindsRecords;
use FoundryCo\LaravelCloudClient\Concerns\ListsRecords;
use FoundryCo\LaravelCloudClient\Concerns\UpdatesRecords;
use FoundryCo\LaravelCloudClient\Data\WebsocketApplication;
use Illuminate\Support\Collection;

/**
 * @method Collection<int, WebsocketApplication> list()
 * @method WebsocketApplication find(string $id)
 * @method WebsocketApplication create(string $name, ?array $allowedOrigins = null, ?int $pingInterval = null, ?int $activityTimeout = null)
 * @method WebsocketApplication update(string $id, ?string $name = null, ?array $allowedOrigins = null, ?int $pingInterval = null, ?int $activityTimeout = null)
 * @method void delete(string $id)
 */
class WebSocketApplicationsResource extends Resource
{
    use CreatesRecords, DeletesRecords, FindsRecords, ListsRecords, UpdatesRecords;

    protected string $model = WebsocketApplication::class;

    protected string $collection = '/websocket-servers/{parent}/applications';

    protected string $item = '/websocket-applications/{id}';

    protected array $creating = ['name'];

    public function metrics(string $id, ?string $period = null): array
    {
        return $this->client->get("/websocket-applications/{$id}/metrics", $this->withoutNulls(['period' => $period]));
    }
}
