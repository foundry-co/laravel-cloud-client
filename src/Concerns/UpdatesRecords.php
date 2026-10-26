<?php

namespace FoundryCo\LaravelCloudClient\Concerns;

use FoundryCo\LaravelCloudClient\Data\Model;

trait UpdatesRecords
{
    protected function updateRecord(string $id, mixed ...$attributes): Model
    {
        return $this->hydrate($this->client->patch($this->path($this->item, $id), $this->attributes($attributes)));
    }
}
