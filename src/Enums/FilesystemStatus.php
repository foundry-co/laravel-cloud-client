<?php

namespace FoundryCo\LaravelCloudClient\Enums;

enum FilesystemStatus: string
{
    case Creating = 'creating';
    case Updating = 'updating';
    case Available = 'available';
    case Deleting = 'deleting';
    case Deleted = 'deleted';
    case Unknown = 'unknown';
}
