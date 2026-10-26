<?php

namespace FoundryCo\LaravelCloudClient\Facades;

use FoundryCo\LaravelCloudClient\CloudClient;
use FoundryCo\LaravelCloudClient\Resources\ApplicationsResource;
use FoundryCo\LaravelCloudClient\Resources\BackgroundProcessesResource;
use FoundryCo\LaravelCloudClient\Resources\BucketKeysResource;
use FoundryCo\LaravelCloudClient\Resources\CachesResource;
use FoundryCo\LaravelCloudClient\Resources\CommandsResource;
use FoundryCo\LaravelCloudClient\Resources\DatabaseClustersResource;
use FoundryCo\LaravelCloudClient\Resources\DatabaseRestoresResource;
use FoundryCo\LaravelCloudClient\Resources\DatabaseSnapshotsResource;
use FoundryCo\LaravelCloudClient\Resources\DatabasesResource;
use FoundryCo\LaravelCloudClient\Resources\DedicatedClustersResource;
use FoundryCo\LaravelCloudClient\Resources\DeploymentsResource;
use FoundryCo\LaravelCloudClient\Resources\DomainsResource;
use FoundryCo\LaravelCloudClient\Resources\EnvironmentsResource;
use FoundryCo\LaravelCloudClient\Resources\InstancesResource;
use FoundryCo\LaravelCloudClient\Resources\MetaResource;
use FoundryCo\LaravelCloudClient\Resources\ObjectStorageBucketsResource;
use FoundryCo\LaravelCloudClient\Resources\UsageResource;
use FoundryCo\LaravelCloudClient\Resources\WebSocketApplicationsResource;
use FoundryCo\LaravelCloudClient\Resources\WebSocketClustersResource;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ApplicationsResource applications()
 * @method static EnvironmentsResource environments()
 * @method static DeploymentsResource deployments()
 * @method static DomainsResource domains()
 * @method static InstancesResource instances()
 * @method static BackgroundProcessesResource backgroundProcesses()
 * @method static CommandsResource commands()
 * @method static DatabaseClustersResource databaseClusters()
 * @method static DatabasesResource databases()
 * @method static DatabaseSnapshotsResource databaseSnapshots()
 * @method static DatabaseRestoresResource databaseRestores()
 * @method static CachesResource caches()
 * @method static ObjectStorageBucketsResource objectStorageBuckets()
 * @method static BucketKeysResource bucketKeys()
 * @method static WebSocketClustersResource websocketClusters()
 * @method static WebSocketApplicationsResource websocketApplications()
 * @method static DedicatedClustersResource dedicatedClusters()
 * @method static MetaResource meta()
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
