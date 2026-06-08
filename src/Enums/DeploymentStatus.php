<?php

namespace Foundry\LaravelCloudClient\Enums;

enum DeploymentStatus: string
{
    case Pending = 'pending';
    case BuildPending = 'build.pending';
    case BuildCreated = 'build.created';
    case BuildQueued = 'build.queued';
    case BuildRunning = 'build.running';
    case BuildSucceeded = 'build.succeeded';
    case BuildFailed = 'build.failed';
    case Cancelled = 'cancelled';
    case Failed = 'failed';
    case DeploymentPending = 'deployment.pending';
    case DeploymentCreated = 'deployment.created';
    case DeploymentQueued = 'deployment.queued';
    case DeploymentRunning = 'deployment.running';
    case DeploymentSucceeded = 'deployment.succeeded';
    case DeploymentFailed = 'deployment.failed';
}
