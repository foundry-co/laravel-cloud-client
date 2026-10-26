<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Data\WebsocketCluster;
use FoundryCo\LaravelCloudClient\Http\GetRequest;
use FoundryCo\LaravelCloudClient\Http\JsonRequest;
use FoundryCo\LaravelCloudClient\Requests\CreateWebSocketClusterRequest;
use FoundryCo\LaravelCloudClient\Requests\UpdateWebSocketClusterRequest;
use Illuminate\Support\Collection;
use Saloon\Enums\Method;

class WebSocketClustersResource extends Resource
{
    public function list(?string $type = null, ?string $region = null, ?string $status = null): Collection
    {
        return $this->paginateAll(
            new GetRequest('/websocket-servers', array_filter([
                'filter[type]' => $type,
                'filter[region]' => $region,
                'filter[status]' => $status,
            ], fn ($v) => $v !== null)),
            fn ($item) => WebsocketCluster::createFromResponse($item)
        );
    }

    public function get(string $clusterId): WebsocketCluster
    {
        $response = $this->send(new GetRequest("/websocket-servers/{$clusterId}"));

        return WebsocketCluster::createFromResponse($response->json());
    }

    public function create(CreateWebSocketClusterRequest $data): WebsocketCluster
    {
        $response = $this->send(new JsonRequest(Method::POST, '/websocket-servers', $data->toArray()));

        return WebsocketCluster::createFromResponse($response->json());
    }

    public function update(UpdateWebSocketClusterRequest $data): WebsocketCluster
    {
        $response = $this->send(new JsonRequest(Method::PATCH, "/websocket-servers/{$data->clusterId}", $data->toArray()));

        return WebsocketCluster::createFromResponse($response->json());
    }

    public function delete(string $clusterId): void
    {
        $this->send(new JsonRequest(Method::DELETE, "/websocket-servers/{$clusterId}"));
    }

    public function metrics(string $clusterId, ?string $period = null): array
    {
        $response = $this->send(new GetRequest("/websocket-servers/{$clusterId}/metrics", array_filter(['period' => $period], fn ($v) => $v !== null)));

        return $response->json();
    }
}
