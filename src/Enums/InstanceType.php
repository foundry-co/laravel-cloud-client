<?php

namespace FoundryCo\LaravelCloudClient\Enums;

enum InstanceType: string
{
    case Service = 'service';
    case ManagedQueue = 'managed_queue';
}
