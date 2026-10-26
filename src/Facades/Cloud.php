<?php

namespace FoundryCo\LaravelCloudClient\Facades;

use FoundryCo\LaravelCloudClient\CloudClient;
use FoundryCo\LaravelCloudClient\Resources\ApplicationsResource;
use FoundryCo\LaravelCloudClient\Resources\BackgroundProcessesResource;
use FoundryCo\LaravelCloudClient\Resources\BucketKeysResource;
use FoundryCo\LaravelCloudClient\Resources\CachesResource;
use FoundryCo\LaravelCloudClient\Resources\CommandsResource;
use FoundryCo\LaravelCloudClient\Resources\DatabaseClustersResource;
use FoundryCo\LaravelCloudClient\Resources\DatabaseSnapshotsResource;
use FoundryCo\LaravelCloudClient\Resources\DatabasesResource;
use FoundryCo\LaravelCloudClient\Resources\DedicatedClustersResource;
use FoundryCo\LaravelCloudClient\Resources\DeploymentsResource;
use FoundryCo\LaravelCloudClient\Resources\DomainsResource;
use FoundryCo\LaravelCloudClient\Resources\EdgeNetworksResource;
use FoundryCo\LaravelCloudClient\Resources\EnvironmentsResource;
use FoundryCo\LaravelCloudClient\Resources\InstancesResource;
use FoundryCo\LaravelCloudClient\Resources\MetaResource;
use FoundryCo\LaravelCloudClient\Resources\ObjectStorageBucketsResource;
use FoundryCo\LaravelCloudClient\Resources\ResendDomainsResource;
use FoundryCo\LaravelCloudClient\Resources\ScheduledCommandsResource;
use FoundryCo\LaravelCloudClient\Resources\SecretsResource;
use FoundryCo\LaravelCloudClient\Resources\UsageResource;
use FoundryCo\LaravelCloudClient\Resources\WebSocketApplicationsResource;
use FoundryCo\LaravelCloudClient\Resources\WebSocketClustersResource;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ApplicationsResource applications()
 * @method static EnvironmentsResource environments(?string $applicationId = null)
 * @method static DeploymentsResource deployments(?string $environmentId = null)
 * @method static DomainsResource domains(?string $environmentId = null)
 * @method static InstancesResource instances(?string $environmentId = null)
 * @method static BackgroundProcessesResource backgroundProcesses(?string $instanceId = null)
 * @method static CommandsResource commands(?string $environmentId = null)
 * @method static DatabaseClustersResource databaseClusters()
 * @method static DatabasesResource databases(?string $clusterId = null)
 * @method static DatabaseSnapshotsResource databaseSnapshots(?string $clusterId = null)
 * @method static CachesResource caches()
 * @method static ObjectStorageBucketsResource objectStorageBuckets()
 * @method static BucketKeysResource bucketKeys(?string $bucketId = null)
 * @method static WebSocketClustersResource websocketClusters()
 * @method static WebSocketApplicationsResource websocketApplications(?string $clusterId = null)
 * @method static DedicatedClustersResource dedicatedClusters()
 * @method static MetaResource meta()
 * @method static ScheduledCommandsResource scheduledCommands(?string $instanceId = null)
 * @method static SecretsResource secrets()
 * @method static EdgeNetworksResource edgeNetworks()
 * @method static ResendDomainsResource resendDomains()
 * @method static UsageResource usage()
 *
 * @see CloudClient
 */
class Cloud extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CloudClient::class;
    }
}
