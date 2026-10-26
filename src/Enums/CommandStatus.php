<?php

namespace FoundryCo\LaravelCloudClient\Enums;

enum CommandStatus: string
{
    case Pending = 'pending';
    case CommandCreated = 'command.created';
    case CommandRunning = 'command.running';
    case CommandFailure = 'command.failure';
    case CommandSuccess = 'command.success';
}
