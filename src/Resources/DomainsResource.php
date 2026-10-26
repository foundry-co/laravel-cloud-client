<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Concerns\CreatesRecords;
use FoundryCo\LaravelCloudClient\Concerns\DeletesRecords;
use FoundryCo\LaravelCloudClient\Concerns\FindsRecords;
use FoundryCo\LaravelCloudClient\Concerns\ListsRecords;
use FoundryCo\LaravelCloudClient\Concerns\UpdatesRecords;
use FoundryCo\LaravelCloudClient\Data\Domain;
use Illuminate\Support\Collection;

/**
 * @method Collection<int, Domain> list(?string $name = null, ?string $hostnameStatus = null, ?string $sslStatus = null, ?string $originStatus = null)
 * @method Domain find(string $id)
 * @method Domain create(string $name, mixed $wwwRedirect = null, ?bool $wildcardEnabled = null, ?bool $allowDowntime = null, mixed $cloudflareStrategy = null, mixed $verificationMethod = null)
 * @method Domain update(string $id, string $verificationMethod)
 * @method void delete(string $id)
 */
class DomainsResource extends Resource
{
    use CreatesRecords, DeletesRecords, FindsRecords, ListsRecords, UpdatesRecords;

    protected string $model = Domain::class;

    protected string $collection = '/environments/{parent}/domains';

    protected string $item = '/domains/{id}';

    protected array $creating = ['name'];

    public function verify(string $id): Domain
    {
        return $this->hydrate($this->client->post("/domains/{$id}/verify"));
    }
}
