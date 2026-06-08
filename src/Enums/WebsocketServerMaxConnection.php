<?php

namespace Foundry\LaravelCloudClient\Enums;

enum WebsocketServerMaxConnection: int
{
    case OneHundred = 100;
    case TwoHundred = 200;
    case FiveHundred = 500;
    case TwoThousand = 2000;
    case FiveThousand = 5000;
    case TenThousand = 10000;
}
