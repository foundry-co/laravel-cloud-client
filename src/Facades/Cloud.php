<?php

namespace Foundry\LaravelCloudClient\Facades;

use Foundry\LaravelCloudClient\CloudClient;
use Foundry\LaravelCloudClient\Resources\ApplicationsResource;
use Foundry\LaravelCloudClient\Resources\BackgroundProcessesResource;
use Foundry\LaravelCloudClient\Resources\BucketKeysResource;
use Foundry\LaravelCloudClient\Resources\CachesResource;
use Foundry\LaravelCloudClient\Resources\CommandsResource;
use Foundry\LaravelCloudClient\Resources\DatabaseClustersResource;
use Foundry\LaravelCloudClient\Resources\DatabaseRestoresResource;
use Foundry\LaravelCloudClient\Resources\DatabaseSnapshotsResource;
use Foundry\LaravelCloudClient\Resources\DatabasesResource;
use Foundry\LaravelCloudClient\Resources\DedicatedClustersResource;
use Foundry\LaravelCloudClient\Resources\DeploymentsResource;
use Foundry\LaravelCloudClient\Resources\DomainsResource;
use Foundry\LaravelCloudClient\Resources\EnvironmentsResource;
use Foundry\LaravelCloudClient\Resources\InstancesResource;
use Foundry\LaravelCloudClient\Resources\MetaResource;
use Foundry\LaravelCloudClient\Resources\ObjectStorageBucketsResource;
use Foundry\LaravelCloudClient\Resources\UsageResource;
use Foundry\LaravelCloudClient\Resources\WebSocketApplicationsResource;
use Foundry\LaravelCloudClient\Resources\WebSocketClustersResource;
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
