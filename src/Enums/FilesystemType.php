<?php

namespace FoundryCo\LaravelCloudClient\Enums;

enum FilesystemType: string
{
    case CloudflareR2 = 'cloudflare_r2';
    case Fake = 'fake';
}
