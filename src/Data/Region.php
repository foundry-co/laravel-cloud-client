<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Region implements Arrayable, Jsonable
{
    public function __construct(
        public readonly string $value,
        public readonly string $label,
        public readonly string $flag,
    ) {}

    public static function createFromResponse(array $data): static
    {
        return new static(
            value: Cast::string($data['region'] ?? null),
            label: Cast::string($data['label'] ?? null),
            flag: Cast::string($data['flag'] ?? null),
        );
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'label' => $this->label,
            'flag' => $this->flag,
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
