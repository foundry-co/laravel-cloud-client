# Laravel Cloud Client

A fluent Laravel client for the [Laravel Cloud API](https://cloud.laravel.com/docs/api/introduction), built on Laravel's HTTP client.

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
use FoundryCo\LaravelCloudClient\Facades\Cloud;

$application = Cloud::applications()->find($applicationId);

$environment = $application->environments()->create(name: 'production', branch: 'main');

$environment->update(phpVersion: '8.3', buildCommand: 'npm run build');
$environment->addVariables(['APP_DEBUG' => 'false']);
$environment->domains()->create('example.com', wwwRedirect: 'www');
$environment->deploy();
```

Every record is an immutable model that knows its client. Calling `update()` or `refresh()` returns a fresh instance, and children are reached through relationship methods such as `$environment->domains()`.

### Conventions

- **Attributes** are passed as named arguments in camelCase (`wwwRedirect: 'www'`). They are sent as snake_case, and `null` values are omitted.
- **Required attributes** can be passed positionally: `create('example.com')`, `create('production', 'main')`.
- **Filters** are named arguments on `list()`: `list(status: 'running')` sends `filter[status]=running`.
- **Pagination** is automatic. `list()` returns a `Collection` of every page.
- **Scoping.** Child resources take their parent as an optional argument: `Cloud::domains($environmentId)`. The same resource is available from the parent model: `$environment->domains()`.
- **Errors** throw `AuthenticationException` (401), `NotFoundException` (404), `ValidationException` (422, with `->errors`) or `CloudApiException`.

---

### Applications

```php
Cloud::applications()->list();
Cloud::applications()->list(name: 'my-app', region: 'us-east-1');
Cloud::applications()->find($applicationId);
Cloud::applications()->create(repository: 'my-org/my-repo', name: 'My App', region: 'us-east-1');

$application->update(name: 'New Name');
$application->uploadAvatar('/path/to/avatar.png');
$application->deleteAvatar();
$application->environments();
$application->delete();
```

### Environments

```php
Cloud::environments($applicationId)->list(status: 'running');
Cloud::environments()->find($environmentId);

$environment->update(phpVersion: '8.3', usesOctane: true);
$environment->start(redeploy: true);
$environment->stop();
$environment->purgeEdgeCache(path: '/assets');
$environment->metrics(period: '24h');
$environment->logs(from: '2024-01-01T00:00:00Z', to: '2024-01-02T00:00:00Z', query: 'error', caseSensitive: true);
$environment->run('php artisan about');
$environment->deploy();
$environment->delete();

$environment->addVariables(['APP_DEBUG' => 'false']);
$environment->replaceVariables(['APP_ENV' => 'production']);
$environment->deleteVariables(['OLD_KEY']);

$environment->updateVanityDomain('my-app');
$environment->attachSecrets([$secretId]);
$environment->detachSecret($secretId);

$environment->attachResend(fromAddress: 'hi@example.com', fromName: 'Example');
$environment->updateResend(fromAddress: 'hello@example.com', fromName: 'Example');
$environment->resendKeys();
$environment->detachResend();
```

`start()` returns the `Deployment`. The other actions return the updated `Environment`. Variables may be given as `['KEY' => 'value']` or as the API's `[['key' => 'KEY', 'value' => 'value']]`.

Relationships: `domains()`, `instances()`, `deployments()`, `commands()`.

### Deployments

```php
$environment->deployments()->list(status: 'deployment.succeeded');
Cloud::deployments()->find($deploymentId);

$deployment = $environment->deploy();
$deployment->status;
$deployment->succeeded();
$deployment->failed();
$deployment->isInProgress();
$deployment->logs();
```

### Domains

```php
$environment->domains()->list();
$domain = $environment->domains()->create('example.com', wildcardEnabled: true);

$domain->update(verificationMethod: 'real_time');
$domain->verify();
$domain->isPrimary();
$domain->delete();
```

### Instances

```php
$environment->instances()->create(
    name: 'web',
    type: InstanceType::Service,
    size: 'flex-1gb',
    scalingType: InstanceScalingType::Auto,
    minReplicas: 1,
    maxReplicas: 3,
);

Cloud::instances()->sizes();

$instance->update(maxReplicas: 5);
$instance->delete();

$instance->backgroundProcesses();
$instance->scheduledCommands();

$instance->setDefault();
$instance->pause();
$instance->resume();
$instance->purge();
$instance->failedJobs();
$instance->retryFailedJob($jobId);
$instance->deleteFailedJob($jobId);
$instance->deleteFailedJobs(jobIds: [$jobId], failedBefore: '2024-01-01');
```

### Scheduled Commands

```php
$instance->scheduledCommands()->list();
$instance->scheduledCommands()->create('php artisan reports:send', '0 8 * * *', name: 'Daily report');

$command->update(schedule: '0 9 * * *');
$command->delete();
```

### Background Processes

```php
$instance->backgroundProcesses()->list();
$instance->backgroundProcesses()->create(type: 'worker', command: 'php artisan queue:work', processes: 2);

$process->update(processes: 4);
$process->delete();
```

### Commands

```php
$environment->commands()->list(status: 'command.success');
$command = $environment->run('php artisan migrate --force');
$command->refresh();
```

### Database Clusters

```php
Cloud::databaseClusters()->list(type: 'neon_serverless_postgres');
Cloud::databaseClusters()->types();
Cloud::databaseClusters()->presets(type: 'neon_serverless_postgres', region: 'us-east-1');

Cloud::databaseClusters()->create(
    type: 'neon_serverless_postgres',
    name: 'my-db',
    region: 'us-east-1',
    version: $version,
    config: ['cu_min' => 0.25, 'cu_max' => 1],
);

$cluster->update(config: ['cu_max' => 2]);
$cluster->metrics(period: '24h');
$cluster->databases();
$cluster->snapshots();
$cluster->snapshot('before-migration');
$cluster->restore(name: 'my-db-restored', snapshotId: $snapshotId);
$cluster->delete();
```

### Databases

Schemas within a cluster.

```php
$cluster->databases()->list();
$cluster->databases()->create('analytics');
$database->delete();
```

### Database Snapshots

```php
$cluster->snapshots()->list();
$snapshot = $cluster->snapshot('before-migration');

$snapshot->restore('my-db-restored');
$snapshot->delete();
```

### Caches

```php
Cloud::caches()->list();
Cloud::caches()->create(type: 'laravel_valkey', name: 'cache', region: 'us-east-1', size: 'valkey-flex-250mb');
Cloud::caches()->types();

$cache->update(size: 'valkey-flex-1gb');
$cache->metrics(period: '24h');
$cache->delete();
```

### Object Storage Buckets

```php
Cloud::objectStorageBuckets()->list(visibility: 'public');
Cloud::objectStorageBuckets()->create(name: 'assets', keyName: 'uploads', visibility: 'public');

$bucket->update(visibility: 'private', allowedOrigins: ['https://example.com']);
$bucket->keys();
$bucket->delete();
```

### Bucket Keys

```php
$bucket->keys()->list();
$bucket->keys()->create('uploads', permission: 'read_only');

$key->update(name: 'renamed');
$key->delete();
```

### WebSocket Clusters

```php
Cloud::websocketClusters()->list();
Cloud::websocketClusters()->types();
Cloud::websocketClusters()->create(name: 'realtime', region: 'us-east-1', maxConnections: 500);

$cluster->update(maxConnections: 2000);
$cluster->metrics(period: '24h');
$cluster->applications();
$cluster->delete();
```

### WebSocket Applications

```php
$cluster->applications()->list();
$cluster->applications()->create('chat', allowedOrigins: ['https://example.com'], pingInterval: 30);

$application->update(activityTimeout: 60);
$application->metrics(period: '24h');
$application->delete();
```

### Dedicated Clusters

```php
Cloud::dedicatedClusters()->list(region: 'us-east-1');
```

### Edge Networks

```php
Cloud::edgeNetworks()->list(status: 'available');
```

### Resend Domains

```php
Cloud::resendDomains()->list(status: 'verified');
```

### Secrets

```php
Cloud::secrets()->list(key: 'STRIPE_KEY');
Cloud::secrets()->publicKey();
Cloud::secrets()->create(keyPairId: $keyPairId, key: 'STRIPE_KEY', value: $encryptedValue, notes: 'Live key');

$secret->update(key: 'STRIPE_KEY', value: $encryptedValue);
$secret->delete();
```

Secret values are submitted as the API expects them, encrypted against the organization's public key.

### Meta

```php
Cloud::meta()->organization();
Cloud::meta()->regions();
```

### Usage

```php
Cloud::usage()->get();
Cloud::usage()->get(period: 1, environment: $environmentId);
```

## Testing

The client uses Laravel's HTTP client, so `Http::fake()` works as usual:

```php
use Illuminate\Support\Facades\Http;

Http::fake([
    'cloud.laravel.com/api/applications*' => Http::response([
        'data' => [['id' => 'app-1', 'type' => 'applications', 'attributes' => ['name' => 'My App']]],
        'links' => ['next' => null],
    ]),
]);

Cloud::applications()->list();

Http::assertSent(fn ($request) => $request->url() === 'https://cloud.laravel.com/api/applications?page=1');
```

## License

MIT
