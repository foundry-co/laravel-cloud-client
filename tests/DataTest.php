<?php

use FoundryCo\LaravelCloudClient\Data\Environment;
use FoundryCo\LaravelCloudClient\Data\WebsocketApplication;
use FoundryCo\LaravelCloudClient\Facades\Cloud;

function dataClasses(): array
{
    return collect(glob(__DIR__.'/../src/Data/*.php'))
        ->map(fn (string $file) => 'FoundryCo\\LaravelCloudClient\\Data\\'.basename($file, '.php'))
        ->reject(fn (string $class) => in_array(class_basename($class), ['Model', 'Cast']))
        ->mapWithKeys(fn (string $class) => [class_basename($class) => [$class]])
        ->all();
}

it('hydrates an environment whose serving is an array', function () {
    fakeCloud(['/applications/app-1/environments' => ['data' => payload('env-1', [
        'serving' => ['mode' => 'process', 'root' => 'public'],
        'environment_variables' => [['key' => 'APP_ENV', 'value' => 'production']],
        'status' => 'running',
    ])]]);

    $environment = Cloud::environments('app-1')->create(name: 'Test', branch: 'main');

    expect($environment)->toBeInstanceOf(Environment::class)
        ->and($environment->serving)->toBe(['mode' => 'process', 'root' => 'public'])
        ->and($environment->environmentVariables)->toBe([['key' => 'APP_ENV', 'value' => 'production']]);
});

it('reads conditional attributes the schemas flatten into unnamed unions', function () {
    $application = WebsocketApplication::createFromResponse(payload('ws-1', ['key' => 'abc', 'secret' => 'shh']));

    expect($application->key)->toBe('abc')->and($application->secret)->toBe('shh');
});

it('never throws on unexpected attribute types', function (string $class) {
    $keys = collect(['id', 'name', 'status', 'type', 'created_at', 'serving', 'config', 'redirect', 'max_connections', 'allowed_origins', 'region', 'label', 'flag', 'summary', 'data', 'meta'])
        ->merge(preg_match_all("/\\['(\\w+)'\\]/", file_get_contents((new ReflectionClass($class))->getFileName()), $m) ? $m[1] : [])
        ->unique();

    foreach ([[], 'text', 7, 1.5, true, ['nested' => ['value' => 1]], [[1]], null] as $junk) {
        $attributes = $keys->mapWithKeys(fn ($key) => [$key => $junk])->all();
        $relationships = $keys->mapWithKeys(fn ($key) => [$key => ['data' => $junk]])->all();

        foreach ([['data' => ['id' => $junk, 'attributes' => $attributes, 'relationships' => $relationships]], $attributes] as $payload) {
            $model = $class::createFromResponse($payload);

            expect($model->toArray())->toBeArray();
        }
    }
})->with(dataClasses());
