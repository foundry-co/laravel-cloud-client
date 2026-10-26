<?php

namespace FoundryCo\LaravelCloudClient\Concerns;

use FoundryCo\LaravelCloudClient\Data\Model;

trait FindsRecords
{
    protected function findRecord(string $id): Model
    {
        return $this->hydrate($this->client->get($this->path($this->item, $id)));
    }
}
