<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;
use FoundryCo\LaravelCloudClient\Concerns\DeletesItself;
use FoundryCo\LaravelCloudClient\Concerns\UpdatesItself;
use FoundryCo\LaravelCloudClient\Resources\ScheduledCommandsResource;

class ScheduledCommand extends Model
{
    use DeletesItself, UpdatesItself;

    protected static string $resource = ScheduledCommandsResource::class;

    public function __construct(
        public readonly string $id,
        public readonly ?string $name,
        public readonly string $command,
        public readonly string $schedule,
        public readonly ?CarbonImmutable $createdAt,
        public readonly ?string $instanceId,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? [];
        $relationships = $data['relationships'] ?? [];

        return new static(
            id: Cast::string($data['id'] ?? null),
            name: Cast::nullableString($attrs['name'] ?? null),
            command: Cast::string($attrs['command'] ?? null),
            schedule: Cast::string($attrs['schedule'] ?? null),
            createdAt: Cast::date($attrs['created_at'] ?? null),
            instanceId: Cast::relation($relationships, 'instance'),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'command' => $this->command,
            'schedule' => $this->schedule,
            'created_at' => $this->createdAt?->toIso8601String(),
            'instance_id' => $this->instanceId,
        ];
    }
}
