<?php

namespace FoundryCo\LaravelCloudClient\Concerns;

use FoundryCo\LaravelCloudClient\Data\Model;

trait CreatesRecords
{
    protected function createRecord(mixed ...$attributes): Model
    {
        $body = $this->attributes($attributes) + $this->defaults;

        return $this->hydrate($this->client->post($this->path($this->collection), $body));
    }
}
