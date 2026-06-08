# Laravel Cloud Client

A Laravel client for the [Laravel Cloud API](https://cloud.laravel.com/docs/api/introduction), built on [Saloon](https://docs.saloon.dev).

## Installation

```bash
composer require foundry-co/laravel-cloud-client
```

Publish the config:

```bash
php artisan vendor:publish --tag=cloud-config
```

Set your API token in `.env`:

```
LARAVEL_CLOUD_TOKEN=your-token-here
```

## Usage

Use the `Cloud` facade or resolve `CloudClient` from the container.

```php
use Foundry\LaravelCloudClient\Facades\Cloud;
```

---

### Applications

```php
Cloud::applications()->list();
Cloud::applications()->list(name: 'my-app', region: 'us-east-1');

Cloud::applications()->get($applicationId);

Cloud::applications()->create(new CreateApplicationRequest(
    repository: 'my-org/my-repo',
    name: 'My App',
    region: 'us-east-1',
));

Cloud::applications()->update(new UpdateApplicationRequest(
    applicationId: $applicationId,
    name: 'New Name',
));

Cloud::applications()->delete($applicationId);

Cloud::applications()->uploadAvatar($applicationId, '/path/to/avatar.png');
```

---

### Environments

```php
Cloud::environments()->list($applicationId);
Cloud::environments()->list($applicationId, status: 'running');

Cloud::environments()->get($environmentId);

Cloud::environments()->create(new CreateEnvironmentRequest(
    applicationId: $applicationId,
    name: 'production',
    branch: 'main',
));

Cloud::environments()->update(new UpdateEnvironmentRequest(
    environmentId: $environmentId,
    phpVersion: '8.3',
    buildCommand: 'npm run build',
));

Cloud::environments()->start($environmentId);
Cloud::environments()->stop($environmentId);
Cloud::environments()->purgeCache($environmentId);

Cloud::environments()->addVariables(new AddEnvironmentVariablesRequest(
    environmentId: $environmentId,
    variables: ['APP_DEBUG' => 'false', 'QUEUE_CONNECTION' => 'redis'],
));

Cloud::environments()->replaceVariables(new ReplaceEnvironmentVariablesRequest(
    environmentId: $environmentId,
    variables: ['APP_ENV' => 'production'],
));

Cloud::environments()->metrics($environmentId, period: '24h');
Cloud::environments()->logs($environmentId, from: '2024-01-01T00:00:00Z', to: '2024-01-02T00:00:00Z');
```

---

### Deployments

```php
Cloud::deployments()->list($environmentId);
Cloud::deployments()->list($environmentId, status: 'deployment.succeeded');

Cloud::deployments()->get($deploymentId);

Cloud::deployments()->initiate(new CreateDeploymentRequest(
    environmentId: $environmentId,
));

Cloud::deployments()->logs($deploymentId);
```

---

### Domains

```php
Cloud::domains()->list($environmentId);

Cloud::domains()->get($domainId);

Cloud::domains()->create(new CreateDomainRequest(
    environmentId: $environmentId,
    name: 'example.com',
));

Cloud::domains()->update(new UpdateDomainRequest(
    domainId: $domainId,
    wwwRedirect: 'www',
));

Cloud::domains()->verify($domainId);
Cloud::domains()->delete($domainId);
```

---

### Instances

```php
Cloud::instances()->list($environmentId);
Cloud::instances()->sizes();

Cloud::instances()->get($instanceId);

Cloud::instances()->create(new CreateInstanceRequest(
    environmentId: $environmentId,
    name: 'web',
    type: InstanceType::SERVICE,
    size: 'small-1x',
    scalingType: InstanceScalingType::AUTO,
    minReplicas: 1,
    maxReplicas: 3,
));

Cloud::instances()->update(new UpdateInstanceRequest(
    instanceId: $instanceId,
    size: 'medium-1x',
));

Cloud::instances()->delete($instanceId);

// Managed queues
Cloud::instances()->pause($instanceId);
Cloud::instances()->resume($instanceId);
Cloud::instances()->purge($instanceId);
Cloud::instances()->setDefault($instanceId);
Cloud::instances()->failedJobs($instanceId);
Cloud::instances()->retryFailedJob($instanceId, $jobId);
Cloud::instances()->deleteFailedJob($instanceId, $jobId);
```

---

### Background Processes

```php
Cloud::backgroundProcesses()->list($instanceId);
Cloud::backgroundProcesses()->get($backgroundProcessId);

Cloud::backgroundProcesses()->create(new CreateBackgroundProcessRequest(
    instanceId: $instanceId,
    type: 'worker',
    command: 'php artisan queue:work',
    processes: 2,
));

Cloud::backgroundProcesses()->update(new UpdateBackgroundProcessRequest(
    backgroundProcessId: $backgroundProcessId,
    processes: 4,
));

Cloud::backgroundProcesses()->delete($backgroundProcessId);
```

---

### Commands

```php
Cloud::commands()->list($environmentId);
Cloud::commands()->get($commandId);

Cloud::commands()->run(new RunCommandRequest(
    environmentId: $environmentId,
    command: 'php artisan migrate',
));
```

---

### Database Clusters

```php
Cloud::databaseClusters()->list();
Cloud::databaseClusters()->types();

Cloud::databaseClusters()->get($clusterId);

Cloud::databaseClusters()->create(new CreateDatabaseClusterRequest(
    type: 'mysql_8',
    name: 'my-db',
    region: 'us-east-1',
    config: ['storage' => 20],
));

Cloud::databaseClusters()->metrics($clusterId, period: '24h');
Cloud::databaseClusters()->delete($clusterId);
```

---

### Databases (schemas within a cluster)

```php
Cloud::databases()->list($clusterId);
Cloud::databases()->get($clusterId, $databaseId);

Cloud::databases()->create(new CreateDatabaseRequest(
    clusterId: $clusterId,
    name: 'app_production',
));

Cloud::databases()->delete($clusterId, $databaseId);
```

---

### Database Snapshots

```php
Cloud::databaseSnapshots()->list($clusterId);
Cloud::databaseSnapshots()->get($clusterId, $snapshotId);

Cloud::databaseSnapshots()->create(new CreateDatabaseSnapshotRequest(
    clusterId: $clusterId,
));

Cloud::databaseSnapshots()->delete($clusterId, $snapshotId);
```

### Database Restores

```php
Cloud::databaseRestores()->create(new CreateDatabaseRestoreRequest(
    snapshotId: $snapshotId,
));
```

---

### Caches

```php
Cloud::caches()->list();
Cloud::caches()->types();

Cloud::caches()->get($cacheId);

Cloud::caches()->create(new CreateCacheRequest(
    type: 'redis_7',
    name: 'my-cache',
    region: 'us-east-1',
    size: 'small',
));

Cloud::caches()->metrics($cacheId, period: '24h');
Cloud::caches()->delete($cacheId);
```

---

### Object Storage Buckets

```php
Cloud::objectStorageBuckets()->list();
Cloud::objectStorageBuckets()->get($bucketId);

Cloud::objectStorageBuckets()->create(new CreateObjectStorageBucketRequest(
    name: 'my-bucket',
    region: 'us-east-1',
));

Cloud::objectStorageBuckets()->update(new UpdateObjectStorageBucketRequest(
    bucketId: $bucketId,
    visibility: 'public',
));

Cloud::objectStorageBuckets()->delete($bucketId);
```

### Bucket Keys

```php
Cloud::bucketKeys()->list($bucketId);
Cloud::bucketKeys()->get($keyId);

Cloud::bucketKeys()->create(new CreateBucketKeyRequest(
    bucketId: $bucketId,
    name: 'deploy-key',
    permission: 'read_write',
));

Cloud::bucketKeys()->delete($keyId);
```

---

### WebSocket Clusters

```php
Cloud::websocketClusters()->list();
Cloud::websocketClusters()->get($clusterId);

Cloud::websocketClusters()->create(new CreateWebSocketClusterRequest(
    name: 'my-reverb',
    region: 'us-east-1',
));

Cloud::websocketClusters()->metrics($clusterId, period: '24h');
Cloud::websocketClusters()->delete($clusterId);
```

### WebSocket Applications

```php
Cloud::websocketApplications()->list($clusterId);
Cloud::websocketApplications()->get($applicationId);

Cloud::websocketApplications()->create(new CreateWebSocketApplicationRequest(
    clusterId: $clusterId,
    name: 'my-app',
));

Cloud::websocketApplications()->metrics($applicationId, period: '24h');
Cloud::websocketApplications()->delete($applicationId);
```

---

### Dedicated Clusters

```php
Cloud::dedicatedClusters()->list();
```

---

### Meta

```php
Cloud::meta()->organization();
Cloud::meta()->regions();
```

### Usage

```php
Cloud::usage()->get();
Cloud::usage()->get(period: 1);
```

---

## Testing

In your application tests, use Saloon's `MockClient`:

```php
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Foundry\LaravelCloudClient\CloudClient;

$mock = new MockClient([
    '*' => MockResponse::make(['data' => [], 'links' => ['next' => null]]),
]);

app(CloudClient::class)->withMockClient($mock);
```

## License

MIT
