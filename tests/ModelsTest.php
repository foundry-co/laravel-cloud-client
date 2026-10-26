<?php

use FoundryCo\LaravelCloudClient\Data\Deployment;
use FoundryCo\LaravelCloudClient\Data\Domain;
use FoundryCo\LaravelCloudClient\Data\Environment;
use FoundryCo\LaravelCloudClient\Enums\DeploymentStatus;
use FoundryCo\LaravelCloudClient\Facades\Cloud;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

function environment(): Environment
{
    fakeCloud(['/environments/env-1' => ['data' => payload('env-1', ['name' => 'production'])]]);

    return Cloud::environments()->find('env-1');
}

it('navigates from a model to its children', function () {
    $environment = environment();

    fakeCloud([
        '/environments/env-1/domains' => ['data' => payload('dom-1', ['name' => 'example.com'])],
        '/environments/env-1/deployments' => ['data' => payload('dep-1', ['status' => 'pending'])],
        '/environments/env-1/commands' => ['data' => payload('cmd-1')],
    ]);

    expect($environment->domains()->create('example.com'))->toBeInstanceOf(Domain::class)
        ->and($environment->deploy())->toBeInstanceOf(Deployment::class)
        ->and($environment->run('php artisan about')->id)->toBe('cmd-1');

    assertCloudSent('POST', '/environments/env-1/domains', fn (Request $request) => $request->data() === ['name' => 'example.com']);
    assertCloudSent('POST', '/environments/env-1/deployments');
    assertCloudSent('POST', '/environments/env-1/commands', fn (Request $request) => $request->data() === ['command' => 'php artisan about']);
});

it('updates and refreshes itself', function () {
    Http::fake(['cloud.laravel.com/api/environments/env-1' => Http::sequence()
        ->push(['data' => payload('env-1', ['name' => 'production'])])
        ->push(['data' => payload('env-1', ['name' => 'staging'])])
        ->push(['data' => payload('env-1', ['name' => 'staging'])])]);

    $environment = Cloud::environments()->find('env-1');

    expect($environment->update(phpVersion: '8.3')->name)->toBe('staging')
        ->and($environment->refresh()->name)->toBe('staging');

    assertCloudSent('PATCH', '/environments/env-1', fn (Request $request) => $request->data() === ['php_version' => '8.3']);
});

it('starts, stops and purges the edge cache', function () {
    $environment = environment();

    fakeCloud([
        '/environments/env-1/start' => ['data' => payload('dep-1', ['status' => 'build.running'])],
        '/environments/env-1/stop' => ['data' => payload('env-1')],
        '/environments/env-1/purge-edge-cache' => ['data' => payload('env-1')],
    ]);

    $deployment = $environment->start(redeploy: true);
    $environment->stop();
    $environment->purgeEdgeCache(path: '/assets');

    expect($deployment->status)->toBe(DeploymentStatus::BuildRunning)->and($deployment->isInProgress())->toBeTrue();

    assertCloudSent('POST', '/environments/env-1/start', fn (Request $request) => $request->data() === ['redeploy' => true]);
    assertCloudSent('POST', '/environments/env-1/stop');
    assertCloudSent('POST', '/environments/env-1/purge-edge-cache', fn (Request $request) => $request->data() === ['path' => '/assets']);
});

it('manages environment variables', function () {
    $environment = environment();

    fakeCloud([
        '/environments/env-1/variables' => ['data' => payload('env-1')],
        '/environments/env-1/variables/delete' => ['data' => payload('env-1')],
    ]);

    $environment->addVariables(['APP_DEBUG' => 'false']);
    $environment->replaceVariables([['key' => 'APP_ENV', 'value' => 'production']]);
    $environment->deleteVariables(['OLD_KEY']);

    assertCloudSent('POST', '/environments/env-1/variables', fn (Request $request) => $request->data() === [
        'method' => 'append',
        'variables' => [['key' => 'APP_DEBUG', 'value' => 'false']],
    ]);
    assertCloudSent('POST', '/environments/env-1/variables', fn (Request $request) => $request->data() === [
        'method' => 'set',
        'variables' => [['key' => 'APP_ENV', 'value' => 'production']],
    ]);
    assertCloudSent('POST', '/environments/env-1/variables/delete', fn (Request $request) => $request->data() === ['keys' => ['OLD_KEY']]);
});

it('manages vanity domain, secrets and Resend', function () {
    $environment = environment();

    fakeCloud([
        '/environments/env-1/vanity-domain' => ['data' => payload('env-1')],
        '/environments/env-1/secrets' => ['data' => payload('env-1')],
        '/environments/env-1/secrets/sec-1' => [],
        '/environments/env-1/resend' => ['data' => payload('env-1')],
        '/environments/env-1/resend/keys' => ['data' => [['id' => 'key-1', 'name' => 'Sending', 'environment_count' => 2]]],
    ]);

    $environment->updateVanityDomain('my-app');
    $environment->attachSecrets(['sec-1']);
    $environment->detachSecret('sec-1');
    $environment->attachResend('hi@example.com', 'Example');
    $environment->updateResend('hello@example.com', 'Example');
    $environment->detachResend();

    expect($environment->resendKeys())->toBe([['id' => 'key-1', 'name' => 'Sending', 'environment_count' => 2]]);

    assertCloudSent('PUT', '/environments/env-1/vanity-domain', fn (Request $request) => $request->data() === ['name' => 'my-app']);
    assertCloudSent('POST', '/environments/env-1/secrets', fn (Request $request) => $request->data() === ['secrets' => ['sec-1']]);
    assertCloudSent('DELETE', '/environments/env-1/secrets/sec-1');
    assertCloudSent('POST', '/environments/env-1/resend', fn (Request $request) => $request->data() === ['from_address' => 'hi@example.com', 'from_name' => 'Example']);
    assertCloudSent('PATCH', '/environments/env-1/resend');
    assertCloudSent('DELETE', '/environments/env-1/resend');
});

it('fetches environment logs', function () {
    $environment = environment();

    fakeCloud(['/environments/env-1/logs*' => ['data' => [['message' => 'Booted', 'level' => 'info', 'type' => 'application', 'logged_at' => '2026-01-01T00:00:00Z', 'data' => null]]]]);

    $logs = $environment->logs(from: '2026-01-01T00:00:00Z', to: '2026-01-02T00:00:00Z', query: 'Booted', caseSensitive: true);

    expect($logs->first()->message)->toBe('Booted')->and($logs->first()->loggedAt->year)->toBe(2026);

    Http::assertSent(fn (Request $request) => str_contains($request->url(), 'query=Booted') && str_contains($request->url(), 'case_sensitive=1'));
});

it('manages managed queue instances', function () {
    fakeCloud([
        '/instances/ins-1' => ['data' => payload('ins-1', ['type' => 'managed_queue'])],
        '/instances/ins-1/pause' => ['data' => payload('ins-1', ['paused' => true])],
        '/instances/ins-1/resume' => ['data' => payload('ins-1')],
        '/instances/ins-1/purge' => ['data' => payload('ins-1')],
        '/instances/ins-1/default' => ['data' => payload('ins-1')],
        '/instances/ins-1/failed-jobs*' => ['data' => [['id' => 'job-1']]],
        '/instances/ins-1/failed-jobs/job-1' => [],
        '/instances/ins-1/failed-jobs/job-1/retry' => [],
    ]);

    $instance = Cloud::instances()->find('ins-1');

    expect($instance->pause()->paused)->toBeTrue();
    $instance->resume();
    $instance->purge();
    $instance->setDefault();
    expect($instance->failedJobs()->all())->toBe([['id' => 'job-1']]);
    $instance->retryFailedJob('job-1');
    $instance->deleteFailedJob('job-1');
    $instance->deleteFailedJobs(jobIds: ['job-1', 'job-2'], failedBefore: '2026-01-01');

    assertCloudSent('POST', '/instances/ins-1/pause');
    assertCloudSent('POST', '/instances/ins-1/failed-jobs/job-1/retry');
    assertCloudSent('DELETE', '/instances/ins-1/failed-jobs/job-1');
    Http::assertSent(fn (Request $request) => $request->method() === 'DELETE'
        && str_ends_with(parse_url($request->url(), PHP_URL_PATH), '/instances/ins-1/failed-jobs')
        && str_contains(urldecode($request->url()), 'jobIds=job-1,job-2'));
});

it('creates scheduled commands on an instance', function () {
    fakeCloud(['/instances/ins-1' => ['data' => payload('ins-1')], '/instances/ins-1/scheduled-commands' => ['data' => payload('sc-1')]]);

    Cloud::instances()->find('ins-1')->scheduledCommands()->create('php artisan queue:prune-batches', '0 * * * *');

    assertCloudSent('POST', '/instances/ins-1/scheduled-commands', fn (Request $request) => $request->data() === ['command' => 'php artisan queue:prune-batches', 'schedule' => '0 * * * *']);
});

it('verifies a domain from the model', function () {
    fakeCloud(['/domains/dom-1' => ['data' => payload('dom-1', ['type' => 'root'])], '/domains/dom-1/verify' => ['data' => payload('dom-1')]]);

    $domain = Cloud::domains()->find('dom-1');

    expect($domain->isPrimary())->toBeTrue()
        ->and($domain->verify())->toBeInstanceOf(Domain::class);

    assertCloudSent('POST', '/domains/dom-1/verify');
});

it('snapshots and restores a database cluster', function () {
    fakeCloud([
        '/databases/clusters/db-1' => ['data' => payload('db-1')],
        '/databases/clusters/db-1/snapshots' => ['data' => payload('snap-1', [], ['database' => ['data' => ['id' => 'db-1']]])],
        '/databases/clusters/db-1/restore' => ['data' => ['id' => 'db-2']],
    ]);

    $snapshot = Cloud::databaseClusters()->find('db-1')->snapshot('nightly');
    $snapshot->restore('restored');

    assertCloudSent('POST', '/databases/clusters/db-1/snapshots', fn (Request $request) => $request->data() === ['name' => 'nightly']);
    assertCloudSent('POST', '/databases/clusters/db-1/restore', fn (Request $request) => $request->data() === ['name' => 'restored', 'database_snapshot_id' => 'snap-1']);
});

it('deletes nested databases through their parent cluster', function () {
    fakeCloud(['/databases/clusters/db-1/databases*' => ['data' => [payload('d-1')], 'links' => ['next' => null]], '/databases/clusters/db-1/databases/d-1' => []]);

    Cloud::databases('db-1')->list()->first()->delete();

    assertCloudSent('DELETE', '/databases/clusters/db-1/databases/d-1');
});

it('uploads and deletes an application avatar', function () {
    fakeCloud(['/applications/app-1' => ['data' => payload('app-1')], '/applications/app-1/avatar' => ['data' => payload('app-1')]]);

    $avatar = tempnam(sys_get_temp_dir(), 'avatar');
    $application = Cloud::applications()->find('app-1');

    $application->uploadAvatar($avatar);
    $application->deleteAvatar();

    assertCloudSent('POST', '/applications/app-1/avatar', fn (Request $request) => $request->isMultipart());
    assertCloudSent('DELETE', '/applications/app-1/avatar');
});

it('cannot act when detached from a client', function () {
    $domain = Domain::createFromResponse(payload('dom-1'));

    $domain->delete();
})->throws(LogicException::class);
