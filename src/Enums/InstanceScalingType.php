<?php

namespace Foundry\LaravelCloudClient\Enums;

enum InstanceScalingType: string
{
    case None = 'none';
    case Custom = 'custom';
    case Auto = 'auto';
}
