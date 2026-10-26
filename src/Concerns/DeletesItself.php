<?php

namespace FoundryCo\LaravelCloudClient\Concerns;

trait DeletesItself
{
    public function delete(): void
    {
        $this->resource()->delete($this->id);
    }
}
