<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Data\WebsocketApplication;
use FoundryCo\LaravelCloudClient\Http\GetRequest;
use FoundryCo\LaravelCloudClient\Http\JsonRequest;
use FoundryCo\LaravelCloudClient\Requests\CreateWebSocketApplicationRequest;
use FoundryCo\LaravelCloudClient\Requests\UpdateWebSocketApplicationRequest;
use Illuminate\Support\Collection;
use Saloon\Enums\Method;

class WebSocketApplicationsResource extends Resource
{
    public function list(string $clusterId): Collection
    {
        return $this->paginateAll(
            new GetRequest("/websocket-servers/{$clusterId}/applications"),
            fn ($item) => WebsocketApplication::createFromResponse($item)
        );
    }

    public function get(string $applicationId): WebsocketApplication
    {
        $response = $this->send(new GetRequest("/websocket-applications/{$applicationId}"));

        return WebsocketApplication::createFromResponse($response->json());
    }

    public function create(CreateWebSocketApplicationRequest $data): WebsocketApplication
    {
        $response = $this->send(new JsonRequest(Method::POST, "/websocket-servers/{$data->clusterId}/applications", $data->toArray()));

        return WebsocketApplication::createFromResponse($response->json());
    }

    public function update(UpdateWebSocketApplicationRequest $data): WebsocketApplication
    {
        $response = $this->send(new JsonRequest(Method::PATCH, "/websocket-applications/{$data->applicationId}", $data->toArray()));

        return WebsocketApplication::createFromResponse($response->json());
    }

    public function delete(string $applicationId): void
    {
        $this->send(new JsonRequest(Method::DELETE, "/websocket-applications/{$applicationId}"));
    }

    public function metrics(string $applicationId, ?string $period = null): array
    {
        $response = $this->send(new GetRequest("/websocket-applications/{$applicationId}/metrics", array_filter(['period' => $period], fn ($v) => $v !== null)));

        return $response->json();
    }
}
