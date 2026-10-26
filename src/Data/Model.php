<?php

namespace FoundryCo\LaravelCloudClient\Data;

use FoundryCo\LaravelCloudClient\CloudClient;
use FoundryCo\LaravelCloudClient\Resources\Resource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

abstract class Model implements Arrayable, Jsonable
{
    protected static string $resource;

    protected ?CloudClient $client = null;

    protected ?string $parent = null;

    public function attach(CloudClient $client, ?string $parent = null): static
    {
        $this->client = $client;
        $this->parent = $parent;

        return $this;
    }

    public function refresh(): static
    {
        return $this->resource()->find($this->id);
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    protected function resource(): Resource
    {
        return $this->scoped(static::$resource, $this->parent);
    }

    protected function scoped(string $resource, ?string $parent): Resource
    {
        return new $resource($this->client ?? throw new \LogicException('Model is not attached to a client.'), $parent);
    }

    protected function children(string $resource): Resource
    {
        return $this->scoped($resource, $this->id);
    }
}
