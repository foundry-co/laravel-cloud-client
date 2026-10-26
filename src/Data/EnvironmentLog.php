<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class EnvironmentLog implements Arrayable, Jsonable
{
    public function __construct(
        public readonly string $level,
        public readonly string $type,
        public readonly string $message,
        public readonly ?CarbonImmutable $loggedAt,
        public readonly mixed $data,
    ) {}

    public static function createFromResponse(array $data): static
    {
        return new static(
            level: $data['level'] ?? '',
            type: $data['type'] ?? '',
            message: $data['message'] ?? '',
            loggedAt: isset($data['logged_at']) ? CarbonImmutable::parse($data['logged_at']) : null,
            data: $data['data'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'level' => $this->level,
            'type' => $this->type,
            'message' => $this->message,
            'logged_at' => $this->loggedAt?->toIso8601String(),
            'data' => $this->data,
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
