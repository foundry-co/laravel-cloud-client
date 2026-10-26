<?php

namespace FoundryCo\LaravelCloudClient\Requests;

class UpdateDomainRequest extends CloudRequest
{
    public function __construct(
        public readonly string $domainId,
        public readonly ?string $name = null,
        public readonly ?string $wwwRedirect = null,
        public readonly ?bool $wildcardEnabled = null,
    ) {}

    public function toArray(): array
    {
        return $this->filter([
            'name' => $this->name,
            'www_redirect' => $this->wwwRedirect,
            'wildcard_enabled' => $this->wildcardEnabled,
        ]);
    }
}
