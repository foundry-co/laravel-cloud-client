<?php

namespace Foundry\LaravelCloudClient\Data;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class CacheType implements Arrayable, Jsonable
{
    public function __construct(
        public readonly string $type,
        public readonly string $label,
        public readonly array $regions,
        public readonly array $configSchema,
    ) {}

    public static function createFromResponse(array $response): static
    {
        $data = $response['data'] ?? $response;
        $attrs = $data['attributes'] ?? $data;

        return new static(
            type: $attrs['type'] ?? '',
            label: $attrs['label'] ?? '',
            regions: $attrs['regions'] ?? [],
            configSchema: $attrs['config_schema'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'label' => $this->label,
            'regions' => $this->regions,
            'config_schema' => $this->configSchema,
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
