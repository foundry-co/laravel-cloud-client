<?php

namespace Foundry\LaravelCloudClient\Requests;

class CreateDomainRequest extends CloudRequest
{
    public function __construct(
        public readonly string $environmentId,
        public readonly string $name,
        public readonly ?string $wwwRedirect = null,
        public readonly ?bool $wildcardEnabled = null,
        public readonly ?string $verificationMethod = null,
    ) {}

    public function toArray(): array
    {
        return $this->filter([
            'name' => $this->name,
            'www_redirect' => $this->wwwRedirect,
            'wildcard_enabled' => $this->wildcardEnabled,
            'verification_method' => $this->verificationMethod,
        ]);
    }
}
