<?php

use FoundryCo\LaravelCloudClient\Facades\Cloud;
use Illuminate\Http\Client\Request;

function resourceMap(): array
{
    return [
        'applications' => [fn () => Cloud::applications(), '/applications', '/applications/x'],
        'environments' => [fn () => Cloud::environments('p'), '/applications/p/environments', '/environments/x'],
        'deployments' => [fn () => Cloud::deployments('p'), '/environments/p/deployments', '/deployments/x'],
        'domains' => [fn () => Cloud::domains('p'), '/environments/p/domains', '/domains/x'],
        'instances' => [fn () => Cloud::instances('p'), '/environments/p/instances', '/instances/x'],
        'commands' => [fn () => Cloud::commands('p'), '/environments/p/commands', '/commands/x'],
        'background processes' => [fn () => Cloud::backgroundProcesses('p'), '/instances/p/background-processes', '/background-processes/x'],
        'database clusters' => [fn () => Cloud::databaseClusters(), '/databases/clusters', '/databases/clusters/x'],
        'databases' => [fn () => Cloud::databases('p'), '/databases/clusters/p/databases', '/databases/clusters/p/databases/x'],
        'database snapshots' => [fn () => Cloud::databaseSnapshots('p'), '/databases/clusters/p/snapshots', '/database-snapshots/x'],
        'buckets' => [fn () => Cloud::objectStorageBuckets(), '/buckets', '/buckets/x'],
        'bucket keys' => [fn () => Cloud::bucketKeys('p'), '/buckets/p/keys', '/bucket-keys/x'],
        'caches' => [fn () => Cloud::caches(), '/caches', '/caches/x'],
        'websocket clusters' => [fn () => Cloud::websocketClusters(), '/websocket-servers', '/websocket-servers/x'],
        'websocket applications' => [fn () => Cloud::websocketApplications('p'), '/websocket-servers/p/applications', '/websocket-applications/x'],
        'scheduled commands' => [fn () => Cloud::scheduledCommands('p'), '/instances/p/scheduled-commands', '/scheduled-commands/x'],
        'secrets' => [fn () => Cloud::secrets(), '/secrets', null],
        'dedicated clusters' => [fn () => Cloud::dedicatedClusters(), '/dedicated-clusters', null],
        'edge networks' => [fn () => Cloud::edgeNetworks(), '/edge-networks', null],
        'resend domains' => [fn () => Cloud::resendDomains(), '/resend/domains', null],
    ];
}

dataset('resources', fn () => resourceMap());

dataset('findable', fn () => array_filter(resourceMap(), fn (array $resource) => $resource[2] !== null));

it('lists records from the collection endpoint', function (Closure $resource, string $collection) {
    fakeCloud([$collection.'*' => ['data' => [payload('a'), payload('b')], 'links' => ['next' => null]]]);

    $records = $resource()->list();

    expect($records->pluck('id')->all())->toBe(['a', 'b']);
    assertCloudSent('GET', $collection);
})->with('resources');

it('finds a record by id', function (Closure $resource, string $collection, string $item) {
    fakeCloud([$item => ['data' => payload('x')]]);

    expect($resource()->find('x')->id)->toBe('x');
    assertCloudSent('GET', $item);
})->with('findable');

it('creates, updates and deletes through the generic endpoints', function () {
    fakeCloud([
        '/environments/p/domains' => ['data' => payload('x', ['name' => 'example.com'])],
        '/domains/x' => ['data' => payload('x', ['name' => 'new.example.com'])],
    ]);

    $domain = Cloud::domains('p')->create('example.com', wwwRedirect: 'www', wildcardEnabled: true);
    assertCloudSent('POST', '/environments/p/domains', fn (Request $request) => $request->data() === [
        'name' => 'example.com',
        'www_redirect' => 'www',
        'wildcard_enabled' => true,
    ]);

    $updated = $domain->update(name: 'new.example.com');
    assertCloudSent('PATCH', '/domains/x', fn (Request $request) => $request->data() === ['name' => 'new.example.com']);
    expect($updated->name)->toBe('new.example.com');

    $domain->delete();
    assertCloudSent('DELETE', '/domains/x');
});

it('applies create defaults and lets callers override them', function () {
    fakeCloud(['/buckets' => ['data' => payload('x')]]);

    Cloud::objectStorageBuckets()->create(name: 'assets', keyName: 'uploads', visibility: 'public');

    assertCloudSent('POST', '/buckets', fn (Request $request) => $request->data() === [
        'name' => 'assets',
        'key_name' => 'uploads',
        'visibility' => 'public',
        'jurisdiction' => 'default',
        'key_permission' => 'read_write',
    ]);
});

it('rejects unexpected positional arguments', function () {
    Cloud::applications()->create('a', 'b', 'c', 'd');
})->throws(InvalidArgumentException::class);

it('does not expose verbs a resource does not support', function () {
    Cloud::deployments('env-1')->delete('dep-1');
})->throws(BadMethodCallException::class);
