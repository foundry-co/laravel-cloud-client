<?php

use FoundryCo\LaravelCloudClient\CloudClient;
use FoundryCo\LaravelCloudClient\Data\Application;
use FoundryCo\LaravelCloudClient\Exceptions\AuthenticationException;
use FoundryCo\LaravelCloudClient\Exceptions\CloudApiException;
use FoundryCo\LaravelCloudClient\Exceptions\NotFoundException;
use FoundryCo\LaravelCloudClient\Exceptions\ValidationException;
use FoundryCo\LaravelCloudClient\Facades\Cloud;
use FoundryCo\LaravelCloudClient\Resources\DomainsResource;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

it('resolves from the container, alias and facade', function () {
    expect(app(CloudClient::class))->toBeInstanceOf(CloudClient::class)
        ->and(app('cloud'))->toBe(app(CloudClient::class))
        ->and(Cloud::getFacadeRoot())->toBe(app(CloudClient::class));
});

it('sends the bearer token and JSON:API headers', function () {
    fakeCloud(['/applications/app-1' => ['data' => payload('app-1')]]);

    Cloud::applications()->find('app-1');

    Http::assertSent(fn (Request $request) => $request->hasHeader('Authorization', 'Bearer test-token')
        && $request->hasHeader('Accept', 'application/vnd.api+json'));
});

it('sends write requests as JSON', function () {
    fakeCloud(['/applications' => ['data' => payload('app-1')]]);

    Cloud::applications()->create(repository: 'org/repo', name: 'App', region: 'us-east-1');

    assertCloudSent('POST', '/applications', fn (Request $request) => $request->hasHeader('Content-Type', 'application/json')
        && $request->data() === ['repository' => 'org/repo', 'name' => 'App', 'region' => 'us-east-1']);
});

it('auto-paginates until there is no next link', function () {
    Http::fakeSequence('cloud.laravel.com/api/applications*')
        ->push(['data' => [payload('app-1')], 'links' => ['next' => 'page-2']])
        ->push(['data' => [payload('app-2')], 'links' => ['next' => null]]);

    $applications = Cloud::applications()->list();

    expect($applications)->toHaveCount(2)
        ->and($applications->first())->toBeInstanceOf(Application::class)
        ->and($applications->pluck('id')->all())->toBe(['app-1', 'app-2']);

    Http::assertSentCount(2);
});

it('turns named arguments into filters', function () {
    fakeCloud(['/environments/env-1/domains*' => ['data' => [], 'links' => ['next' => null]]]);

    Cloud::domains('env-1')->list(hostnameStatus: 'active', name: null);

    Http::assertSent(fn (Request $request) => str_contains(urldecode($request->url()), 'filter[hostname_status]=active')
        && ! str_contains($request->url(), 'filter%5Bname'));
});

it('requires a parent for scoped resources', function () {
    Cloud::domains()->list();
})->throws(LogicException::class);

it('scopes resources to a parent', function () {
    expect(Cloud::domains('env-1'))->toBeInstanceOf(DomainsResource::class);
});

it('maps error responses to exceptions', function (int $status, string $exception) {
    fakeCloud(['/applications/app-1' => ['__status' => $status, 'body' => ['message' => 'Nope.', 'errors' => ['name' => ['Required.']]]]]);

    Cloud::applications()->find('app-1');
})->with([
    [401, AuthenticationException::class],
    [404, NotFoundException::class],
    [422, ValidationException::class],
    [500, CloudApiException::class],
])->throws(CloudApiException::class);

it('exposes validation errors', function () {
    fakeCloud(['/applications' => ['__status' => 422, 'body' => ['message' => 'Invalid.', 'errors' => ['name' => ['Required.']]]]]);

    try {
        Cloud::applications()->create(name: 'x');
    } catch (ValidationException $e) {
        expect($e->errors)->toBe(['name' => ['Required.']])->and($e->getMessage())->toBe('Invalid.');
    }
});
