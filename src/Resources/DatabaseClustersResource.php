<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Concerns\CreatesRecords;
use FoundryCo\LaravelCloudClient\Concerns\DeletesRecords;
use FoundryCo\LaravelCloudClient\Concerns\FindsRecords;
use FoundryCo\LaravelCloudClient\Concerns\ListsRecords;
use FoundryCo\LaravelCloudClient\Concerns\UpdatesRecords;
use FoundryCo\LaravelCloudClient\Data\DatabaseCluster;
use Illuminate\Support\Collection;

/**
 * @method Collection<int, DatabaseCluster> list(?string $type = null, ?string $region = null, ?string $status = null)
 * @method DatabaseCluster find(string $id)
 * @method DatabaseCluster create(string $type, string $name, string $region, string $version, mixed $config, ?bool $createDefaultDatabase = null, ?string $clusterId = null)
 * @method DatabaseCluster update(string $id, mixed $config)
 * @method void delete(string $id)
 */
class DatabaseClustersResource extends Resource
{
    use CreatesRecords, DeletesRecords, FindsRecords, ListsRecords, UpdatesRecords;

    protected string $model = DatabaseCluster::class;

    protected string $collection = '/databases/clusters';

    protected string $item = '/databases/clusters/{id}';

    protected array $creating = ['type', 'name', 'region', 'version', 'config'];

    public function types(): array
    {
        return $this->client->get('/databases/types')['data'] ?? [];
    }

    public function presets(string $type, string $region): array
    {
        return $this->client->get('/databases/presets', compact('type', 'region'))['data'] ?? [];
    }

    public function metrics(string $id, ?string $period = null): array
    {
        return $this->client->get("/databases/clusters/{$id}/metrics", $this->withoutNulls(['period' => $period]));
    }

    public function restore(string $id, string $name, ?string $snapshotId = null, ?string $restoreTime = null): array
    {
        return $this->client->post("/databases/clusters/{$id}/restore", $this->withoutNulls([
            'name' => $name,
            'database_snapshot_id' => $snapshotId,
            'restore_time' => $restoreTime,
        ]));
    }
}
