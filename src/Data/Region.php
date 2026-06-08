<?php

namespace Foundry\LaravelCloudClient\Data;

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
            value: $data['region'] ?? '',
            label: $data['label'] ?? '',
            flag: $data['flag'] ?? '',
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
