<?php

namespace Foundry\LaravelCloudClient\Tests;

use Foundry\LaravelCloudClient\CloudServiceProvider;
use Foundry\LaravelCloudClient\Facades\Cloud;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [CloudServiceProvider::class];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Cloud' => Cloud::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('cloud.token', 'test-token');
        $app['config']->set('cloud.base_url', 'https://cloud.laravel.com/api');
    }
}
