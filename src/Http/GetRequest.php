<?php

namespace FoundryCo\LaravelCloudClient\Http;

use Saloon\Enums\Method;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class GetRequest extends CloudRequest implements Paginatable
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $endpoint,
        private readonly array $queryParams = [],
    ) {}

    public function resolveEndpoint(): string
    {
        return $this->endpoint;
    }

    protected function defaultQuery(): array
    {
        return $this->queryParams;
    }
}
