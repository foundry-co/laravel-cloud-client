<?php

use FoundryCo\LaravelCloudClient\CloudClient;
use FoundryCo\LaravelCloudClient\Data\Organization;
use FoundryCo\LaravelCloudClient\Data\Region;
use FoundryCo\LaravelCloudClient\Http\GetRequest;
use Illuminate\Support\Collection;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

beforeEach(function () {
    $this->client = app(CloudClient::class);
});

it('gets the organization', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make([
            'data' => [
                'id' => 'org-1',
                'type' => 'organizations',
                'attributes' => [
                    'name' => 'Acme',
                    'slug' => 'acme',
                ],
            ],
        ], 200),
    ]);
    $this->client->withMockClient($mockClient);

    $result = $this->client->meta()->organization();

    expect($result)->toBeInstanceOf(Organization::class)
        ->and($result->id)->toBe('org-1')
        ->and($result->name)->toBe('Acme');
});

it('lists regions', function () {
    $mockClient = new MockClient([
        GetRequest::class => MockResponse::make([
            'data' => [
                ['region' => 'us-east-1', 'label' => 'US East (N. Virginia)', 'flag' => '🇺🇸'],
                ['region' => 'eu-west-1', 'label' => 'EU (Ireland)', 'flag' => '🇮🇪'],
            ],
        ], 200),
    ]);
    $this->client->withMockClient($mockClient);

    $result = $this->client->meta()->regions();

    expect($result)->toBeInstanceOf(Collection::class)
        ->and($result)->toHaveCount(2)
        ->and($result->first())->toBeInstanceOf(Region::class)
        ->and($result->first()->value)->toBe('us-east-1')
        ->and($result->first()->label)->toBe('US East (N. Virginia)');
});
