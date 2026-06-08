<?php

namespace Foundry\LaravelCloudClient\Requests;

use Illuminate\Contracts\Support\Arrayable;

abstract class CloudRequest implements Arrayable
{
    protected function filter(array $array): array
    {
        return array_filter($array, fn ($value) => $value !== null);
    }

    abstract public function toArray(): array;
}
