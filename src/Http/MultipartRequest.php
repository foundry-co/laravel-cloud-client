<?php

namespace Foundry\LaravelCloudClient\Http;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasMultipartBody;

class MultipartRequest extends CloudRequest implements HasBody
{
    use HasMultipartBody;

    protected Method $method = Method::POST;

    public function __construct(
        private readonly string $endpoint,
        private readonly array $parts = [],
    ) {}

    public function resolveEndpoint(): string
    {
        return $this->endpoint;
    }

    protected function defaultBody(): array
    {
        return $this->parts;
    }
}
