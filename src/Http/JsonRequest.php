<?php

namespace Foundry\LaravelCloudClient\Http;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

class JsonRequest extends CloudRequest implements HasBody
{
    use HasJsonBody;

    public function __construct(
        protected Method $method,
        private readonly string $endpoint,
        private readonly array $bodyData = [],
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

    protected function defaultBody(): array
    {
        return $this->bodyData;
    }
}
