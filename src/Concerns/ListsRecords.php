<?php

namespace FoundryCo\LaravelCloudClient\Concerns;

use Illuminate\Support\Collection;

trait ListsRecords
{
    protected function listRecords(mixed ...$filters): Collection
    {
        return $this->client
            ->paginate($this->path($this->collection), $this->filters($filters))
            ->map(fn (array $payload) => $this->hydrate($payload));
    }
}
