<?php

namespace FoundryCo\LaravelCloudClient\Requests;

class UpdateApplicationRequest extends CloudRequest
{
    public function __construct(
        public readonly string $applicationId,
        public readonly ?string $name = null,
        public readonly ?string $slackChannel = null,
    ) {}

    public function toArray(): array
    {
        return $this->filter([
            'name' => $this->name,
            'slack_channel' => $this->slackChannel,
        ]);
    }
}
