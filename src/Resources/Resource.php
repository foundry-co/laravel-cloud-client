<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use BackedEnum;
use FoundryCo\LaravelCloudClient\CloudClient;
use FoundryCo\LaravelCloudClient\Data\Model;
use Illuminate\Support\Str;

abstract class Resource
{
    private const VERBS = [
        'list' => 'listRecords',
        'find' => 'findRecord',
        'create' => 'createRecord',
        'update' => 'updateRecord',
        'delete' => 'deleteRecord',
    ];

    protected string $model;

    protected string $collection;

    protected string $item;

    protected array $creating = [];

    protected array $defaults = [];

    public function __construct(
        protected CloudClient $client,
        protected ?string $parent = null,
    ) {}

    public function __call(string $method, array $arguments): mixed
    {
        $handler = self::VERBS[$method] ?? null;

        if ($handler === null || ! method_exists($this, $handler)) {
            throw new \BadMethodCallException(static::class."::{$method}() does not exist.");
        }

        return $this->{$handler}(...$arguments);
    }

    protected function hydrate(array $payload): Model
    {
        return $this->model::createFromResponse($payload)->attach($this->client, $this->parent);
    }

    protected function path(string $template, ?string $id = null): string
    {
        if ($this->parent === null && str_contains($template, '{parent}')) {
            throw new \LogicException(static::class.' must be scoped to its parent.');
        }

        return str_replace(['{parent}', '{id}'], [$this->parent, $id], $template);
    }

    protected function attributes(array $input): array
    {
        $attributes = [];

        foreach ($input as $key => $value) {
            $name = is_int($key)
                ? ($this->creating[$key] ?? throw new \InvalidArgumentException('Unexpected positional argument.'))
                : Str::snake($key);

            $attributes[$name] = $value instanceof BackedEnum ? $value->value : $value;
        }

        return $this->withoutNulls($attributes);
    }

    protected function filters(array $filters): array
    {
        $query = [];

        foreach ($filters as $key => $value) {
            $query['filter['.Str::snake($key).']'] = $value instanceof BackedEnum ? $value->value : $value;
        }

        return $this->withoutNulls($query);
    }

    protected function withoutNulls(array $values): array
    {
        return array_filter($values, fn ($value) => $value !== null);
    }
}
