<?php

namespace Foundry\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class EnvironmentLog implements Arrayable, Jsonable
{
    public function __construct(
        public readonly ?string $id,
        public readonly string $level,
        public readonly string $message,
        public readonly ?CarbonImmutable $timestamp,
        public readonly array $context,
    ) {}

    public static function createFromResponse(array $data): static
    {
        return new static(
            id: $data['id'] ?? null,
            level: $data['level'] ?? '',
            message: $data['message'] ?? '',
            timestamp: isset($data['timestamp']) ? CarbonImmutable::parse($data['timestamp']) : null,
            context: $data['context'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'level' => $this->level,
            'message' => $this->message,
            'timestamp' => $this->timestamp?->toIso8601String(),
            'context' => $this->context,
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
