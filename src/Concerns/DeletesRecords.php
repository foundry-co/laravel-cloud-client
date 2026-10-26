<?php

namespace FoundryCo\LaravelCloudClient\Concerns;

trait DeletesRecords
{
    protected function deleteRecord(string $id): void
    {
        $this->client->delete($this->path($this->item, $id));
    }
}
