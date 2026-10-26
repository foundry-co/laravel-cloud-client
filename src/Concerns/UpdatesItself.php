<?php

namespace FoundryCo\LaravelCloudClient\Concerns;

trait UpdatesItself
{
    public function update(mixed ...$attributes): static
    {
        return $this->resource()->update($this->id, ...$attributes);
    }
}
