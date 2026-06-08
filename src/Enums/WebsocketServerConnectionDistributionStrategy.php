<?php

namespace Foundry\LaravelCloudClient\Enums;

enum WebsocketServerConnectionDistributionStrategy: string
{
    case Evenly = 'evenly';
    case Custom = 'custom';
}
