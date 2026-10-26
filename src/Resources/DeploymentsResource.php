<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Concerns\FindsRecords;
use FoundryCo\LaravelCloudClient\Concerns\ListsRecords;
use FoundryCo\LaravelCloudClient\Data\Deployment;
use Illuminate\Support\Collection;

/**
 * @method Collection<int, Deployment> list(?string $status = null, ?string $branchName = null, ?string $commitHash = null)
 * @method Deployment find(string $id)
 */
class DeploymentsResource extends Resource
{
    use FindsRecords, ListsRecords;

    protected string $model = Deployment::class;

    protected string $collection = '/environments/{parent}/deployments';

    protected string $item = '/deployments/{id}';

    public function initiate(): Deployment
    {
        return $this->hydrate($this->client->post($this->path('/environments/{parent}/deployments')));
    }

    public function logs(string $id): array
    {
        return $this->client->get("/deployments/{$id}/logs");
    }
}
