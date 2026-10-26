<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Concerns\CreatesRecords;
use FoundryCo\LaravelCloudClient\Concerns\DeletesRecords;
use FoundryCo\LaravelCloudClient\Concerns\FindsRecords;
use FoundryCo\LaravelCloudClient\Concerns\ListsRecords;
use FoundryCo\LaravelCloudClient\Concerns\UpdatesRecords;
use FoundryCo\LaravelCloudClient\Data\Cache;
use Illuminate\Support\Collection;

/**
 * @method Collection<int, Cache> list(?string $type = null, ?string $region = null, ?string $status = null)
 * @method Cache find(string $id)
 * @method Cache create(string $type, string $name, string $region, string $size, bool $autoUpgradeEnabled = true, bool $isPublic = false, ?string $clusterId = null, mixed $evictionPolicy = null, ?bool $usesHibernation = null, mixed $hibernationTimeout = null)
 * @method Cache update(string $id, ?string $name = null, ?string $size = null, ?bool $autoUpgradeEnabled = null, ?bool $isPublic = null, mixed $evictionPolicy = null, ?bool $usesHibernation = null, mixed $hibernationTimeout = null)
 * @method void delete(string $id)
 */
class CachesResource extends Resource
{
    use CreatesRecords, DeletesRecords, FindsRecords, ListsRecords, UpdatesRecords;

    protected string $model = Cache::class;

    protected string $collection = '/caches';

    protected string $item = '/caches/{id}';

    protected array $creating = ['type', 'name', 'region', 'size'];

    protected array $defaults = ['auto_upgrade_enabled' => true, 'is_public' => false];

    public function types(): array
    {
        return $this->client->get('/caches/types')['data'] ?? [];
    }

    public function metrics(string $id, ?string $period = null): array
    {
        return $this->client->get("/caches/{$id}/metrics", $this->withoutNulls(['period' => $period]));
    }
}
