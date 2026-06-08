<?php

namespace Foundry\LaravelCloudClient\Resources;

use Foundry\LaravelCloudClient\Data\Domain;
use Foundry\LaravelCloudClient\Http\GetRequest;
use Foundry\LaravelCloudClient\Http\JsonRequest;
use Foundry\LaravelCloudClient\Requests\CreateDomainRequest;
use Foundry\LaravelCloudClient\Requests\UpdateDomainRequest;
use Illuminate\Support\Collection;
use Saloon\Enums\Method;

class DomainsResource extends Resource
{
    public function list(string $environmentId, ?string $name = null, ?string $hostnameStatus = null, ?string $sslStatus = null): Collection
    {
        return $this->paginateAll(
            new GetRequest("/environments/{$environmentId}/domains", array_filter([
                'filter[name]' => $name,
                'filter[hostname_status]' => $hostnameStatus,
                'filter[ssl_status]' => $sslStatus,
            ], fn ($v) => $v !== null)),
            fn ($item) => Domain::createFromResponse($item)
        );
    }

    public function get(string $domainId): Domain
    {
        $response = $this->send(new GetRequest("/domains/{$domainId}"));

        return Domain::createFromResponse($response->json());
    }

    public function create(CreateDomainRequest $data): Domain
    {
        $response = $this->send(new JsonRequest(Method::POST, "/environments/{$data->environmentId}/domains", $data->toArray()));

        return Domain::createFromResponse($response->json());
    }

    public function update(UpdateDomainRequest $data): Domain
    {
        $response = $this->send(new JsonRequest(Method::PATCH, "/domains/{$data->domainId}", $data->toArray()));

        return Domain::createFromResponse($response->json());
    }

    public function delete(string $domainId): void
    {
        $this->send(new JsonRequest(Method::DELETE, "/domains/{$domainId}"));
    }

    public function verify(string $domainId): Domain
    {
        $response = $this->send(new JsonRequest(Method::POST, "/domains/{$domainId}/verify"));

        return Domain::createFromResponse($response->json());
    }
}
