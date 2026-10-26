<?php

namespace FoundryCo\LaravelCloudClient\Exceptions;

use RuntimeException;

class CloudApiException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly array $errors = [],
    ) {
        parent::__construct($message);
    }
}
