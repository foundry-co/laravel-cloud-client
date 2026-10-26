<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Concerns\CreatesRecords;
use FoundryCo\LaravelCloudClient\Concerns\DeletesRecords;
use FoundryCo\LaravelCloudClient\Concerns\FindsRecords;
use FoundryCo\LaravelCloudClient\Concerns\ListsRecords;
use FoundryCo\LaravelCloudClient\Concerns\UpdatesRecords;
use FoundryCo\LaravelCloudClient\Data\Application;
use Illuminate\Support\Collection;

/**
 * @method Collection<int, Application> list(?string $name = null, ?string $region = null, ?string $slug = null)
 * @method Application find(string $id)
 * @method Application create(string $repository, string $name, string $region, mixed $starterKit = null, ?string $buildCommand = null, ?string $sourceControlProviderType = null, ?string $rootDirectory = null, ?string $clusterId = null)
 * @method Application update(string $id, ?string $sourceControlProviderType = null, ?string $name = null, ?string $slug = null, ?string $defaultEnvironmentId = null, ?string $repository = null, ?string $slackChannel = null)
 * @method void delete(string $id)
 */
class ApplicationsResource extends Resource
{
    use CreatesRecords, DeletesRecords, FindsRecords, ListsRecords, UpdatesRecords;

    protected string $model = Application::class;

    protected string $collection = '/applications';

    protected string $item = '/applications/{id}';

    protected array $creating = ['repository', 'name', 'region'];

    public function uploadAvatar(string $id, string $file): Application
    {
        return $this->hydrate($this->client->upload("/applications/{$id}/avatar", 'avatar', $file));
    }

    public function deleteAvatar(string $id): void
    {
        $this->client->delete("/applications/{$id}/avatar");
    }
}
