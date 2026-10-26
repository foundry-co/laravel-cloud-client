<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Concerns\CreatesRecords;
use FoundryCo\LaravelCloudClient\Concerns\DeletesRecords;
use FoundryCo\LaravelCloudClient\Concerns\ListsRecords;
use FoundryCo\LaravelCloudClient\Concerns\UpdatesRecords;
use FoundryCo\LaravelCloudClient\Data\Secret;
use Illuminate\Support\Collection;

/**
 * @method Collection<int, Secret> list(?string $key = null, ?array $sort = null)
 * @method Secret create(string $keyPairId, string $key, string $value, ?string $notes = null)
 * @method Secret update(string $id, string $key, ?string $keyPairId = null, ?string $value = null, ?string $notes = null)
 * @method void delete(string $id)
 */
class SecretsResource extends Resource
{
    use CreatesRecords, DeletesRecords, ListsRecords, UpdatesRecords;

    protected string $model = Secret::class;

    protected string $collection = '/secrets';

    protected string $item = '/secrets/{id}';

    protected array $creating = ['key_pair_id', 'key', 'value'];

    public function publicKey(): array
    {
        return $this->client->get('/secrets/public-key')['data'] ?? [];
    }
}
