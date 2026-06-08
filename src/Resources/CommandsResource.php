<?php

namespace Foundry\LaravelCloudClient\Resources;

use Foundry\LaravelCloudClient\Data\Command;
use Foundry\LaravelCloudClient\Http\GetRequest;
use Foundry\LaravelCloudClient\Http\JsonRequest;
use Foundry\LaravelCloudClient\Requests\RunCommandRequest;
use Illuminate\Support\Collection;
use Saloon\Enums\Method;

class CommandsResource extends Resource
{
    public function list(string $environmentId, ?string $status = null, ?string $command = null): Collection
    {
        return $this->paginateAll(
            new GetRequest("/environments/{$environmentId}/commands", array_filter([
                'filter[status]' => $status,
                'filter[command]' => $command,
            ], fn ($v) => $v !== null)),
            fn ($item) => Command::createFromResponse($item)
        );
    }

    public function get(string $commandId): Command
    {
        $response = $this->send(new GetRequest("/commands/{$commandId}"));

        return Command::createFromResponse($response->json());
    }

    public function run(RunCommandRequest $data): Command
    {
        $response = $this->send(new JsonRequest(Method::POST, "/environments/{$data->environmentId}/commands", $data->toArray()));

        return Command::createFromResponse($response->json());
    }
}
