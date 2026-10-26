<?php

use FoundryCo\LaravelCloudClient\Data\Organization;
use FoundryCo\LaravelCloudClient\Data\Region;
use FoundryCo\LaravelCloudClient\Data\Usage;
use FoundryCo\LaravelCloudClient\Facades\Cloud;

it('gets the organization', function () {
    fakeCloud(['/meta/organization' => ['data' => ['id' => 'org-1', 'type' => 'organizations', 'attributes' => ['name' => 'Acme', 'slug' => 'acme']]]]);

    expect(Cloud::meta()->organization())->toBeInstanceOf(Organization::class)->and(Cloud::meta()->organization()->slug)->toBe('acme');
});

it('lists regions', function () {
    fakeCloud(['/meta/regions' => ['data' => [['region' => 'us-east-1', 'label' => 'US East', 'flag' => 'us']]]]);

    $regions = Cloud::meta()->regions();

    expect($regions->first())->toBeInstanceOf(Region::class)->and($regions->first()->value)->toBe('us-east-1');
});

it('reads usage from data and meta', function () {
    fakeCloud(['/usage*' => [
        'data' => ['summary' => ['current_spend_cents' => 1200, 'bandwidth' => null, 'credits' => ['used_cents' => 1, 'total_cents' => 5], 'alert' => null], 'resources' => ['total_cost_cents' => 900], 'addons' => ['total_cost_cents' => 300, 'items' => []], 'application_totals' => ['total_cost_cents' => 0, 'application_count' => 2, 'applications' => []], 'environment_usage' => null, 'private_cloud' => null],
        'meta' => ['currency' => 'USD', 'period' => 1, 'available_periods' => [0, 1], 'last_updated_at' => '2026-01-01T00:00:00Z'],
    ]]);

    $usage = Cloud::usage()->get(period: 1);

    expect($usage)->toBeInstanceOf(Usage::class)
        ->and($usage->currentSpendCents)->toBe(1200)
        ->and($usage->period)->toBe(1)
        ->and($usage->credits)->toBe(['used_cents' => 1, 'total_cents' => 5])
        ->and($usage->lastUpdatedAt->year)->toBe(2026);
});

it('lists secrets and the public key', function () {
    fakeCloud(['/secrets*' => ['data' => [payload('sec-1', ['key' => 'STRIPE_KEY'])], 'links' => ['next' => null]], '/secrets/public-key' => ['data' => ['id' => 'kp-1', 'attributes' => ['public_key' => 'abc']]]]);

    expect(Cloud::secrets()->list()->first()->key)->toBe('STRIPE_KEY');
});

it('fetches the secrets public key', function () {
    fakeCloud(['/secrets/public-key' => ['data' => ['id' => 'kp-1', 'type' => 'keys', 'attributes' => ['public_key' => 'abc']]]]);

    expect(Cloud::secrets()->publicKey())->toBe(['id' => 'kp-1', 'type' => 'keys', 'attributes' => ['public_key' => 'abc']]);
});
