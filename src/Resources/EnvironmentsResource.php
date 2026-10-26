<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Concerns\CreatesRecords;
use FoundryCo\LaravelCloudClient\Concerns\DeletesRecords;
use FoundryCo\LaravelCloudClient\Concerns\FindsRecords;
use FoundryCo\LaravelCloudClient\Concerns\ListsRecords;
use FoundryCo\LaravelCloudClient\Concerns\UpdatesRecords;
use FoundryCo\LaravelCloudClient\Data\Deployment;
use FoundryCo\LaravelCloudClient\Data\Environment;
use FoundryCo\LaravelCloudClient\Data\EnvironmentLog;
use Illuminate\Support\Collection;

/**
 * @method Collection<int, Environment> list(?string $name = null, ?string $status = null, ?string $slug = null)
 * @method Environment find(string $id)
 * @method Environment create(string $name, string $branch, ?string $buildCommand = null, ?string $clusterId = null, ?string $edgeNetworkId = null)
 * @method Environment update(string $id, ?array $serving = null, ?string $name = null, ?string $slug = null, ?string $color = null, ?string $branch = null, ?bool $usesPushToDeploy = null, ?bool $usesDeployHook = null, ?int $timeout = null, ?string $phpVersion = null, ?string $buildCommand = null, ?string $nodeVersion = null, ?string $deployCommand = null, ?string $startCommand = null, ?bool $usesVanityDomain = null, ?string $databaseSchemaId = null, ?string $cacheId = null, ?string $websocketApplicationId = null, ?array $filesystemKeys = null, ?bool $usesOctane = null, ?int $sleepTimeout = null, ?int $hibernationWakeUpInterval = null, ?int $shutdownTimeout = null, ?bool $usesPurgeEdgeCacheOnDeploy = null, ?string $nightwatchToken = null, ?string $cacheStrategy = null, ?string $responseHeadersFrame = null, ?string $responseHeadersContentType = null, ?string $responseHeadersRobotsTag = null, ?array $responseHeadersHsts = null, ?bool $firewallBlockPath = null, ?bool $firewallBrowserIntegrityCheck = null)
 * @method void delete(string $id)
 */
class EnvironmentsResource extends Resource
{
    use CreatesRecords, DeletesRecords, FindsRecords, ListsRecords, UpdatesRecords;

    protected string $model = Environment::class;

    protected string $collection = '/applications/{parent}/environments';

    protected string $item = '/environments/{id}';

    protected array $creating = ['name', 'branch'];

    public function logs(string $id, string $from, string $to, ?string $cursor = null, ?string $type = null, ?string $query = null, ?bool $caseSensitive = null, ?bool $wholeWord = null, ?array $instances = null): Collection
    {
        $body = $this->client->get("/environments/{$id}/logs", $this->withoutNulls([
            'from' => $from,
            'to' => $to,
            'cursor' => $cursor,
            'type' => $type,
            'query' => $query,
            'case_sensitive' => $caseSensitive,
            'whole_word' => $wholeWord,
            'instances' => $instances,
        ]));

        return collect($body['data'] ?? [])->map(fn ($log) => EnvironmentLog::createFromResponse($log));
    }

    public function addVariables(string $id, array $variables): Environment
    {
        return $this->changeVariables($id, 'append', $variables);
    }

    public function replaceVariables(string $id, array $variables): Environment
    {
        return $this->changeVariables($id, 'set', $variables);
    }

    public function deleteVariables(string $id, array $keys): Environment
    {
        return $this->hydrate($this->client->post("/environments/{$id}/variables/delete", ['keys' => array_values($keys)]));
    }

    public function start(string $id, ?bool $redeploy = null): Deployment
    {
        return Deployment::createFromResponse(
            $this->client->post("/environments/{$id}/start", $this->withoutNulls(['redeploy' => $redeploy]))
        )->attach($this->client, $id);
    }

    public function stop(string $id): Environment
    {
        return $this->hydrate($this->client->post("/environments/{$id}/stop"));
    }

    public function purgeEdgeCache(string $id, ?string $path = null, ?string $prefix = null, ?string $tag = null): Environment
    {
        return $this->hydrate($this->client->post("/environments/{$id}/purge-edge-cache", $this->withoutNulls(compact('path', 'prefix', 'tag'))));
    }

    public function metrics(string $id, ?string $period = null): array
    {
        return $this->client->get("/environments/{$id}/metrics", $this->withoutNulls(['period' => $period]));
    }

    public function updateVanityDomain(string $id, string $name): Environment
    {
        return $this->hydrate($this->client->put("/environments/{$id}/vanity-domain", ['name' => $name]));
    }

    public function attachSecrets(string $id, array $secrets): Environment
    {
        return $this->hydrate($this->client->post("/environments/{$id}/secrets", ['secrets' => array_values($secrets)]));
    }

    public function detachSecret(string $id, string $secretId): void
    {
        $this->client->delete("/environments/{$id}/secrets/{$secretId}");
    }

    public function attachResend(string $id, string $fromAddress, string $fromName, ?string $keyStrategy = null, ?string $keyName = null, ?string $reuseKeyId = null): Environment
    {
        return $this->hydrate($this->client->post("/environments/{$id}/resend", $this->withoutNulls([
            'from_address' => $fromAddress,
            'from_name' => $fromName,
            'key_strategy' => $keyStrategy,
            'key_name' => $keyName,
            'reuse_key_id' => $reuseKeyId,
        ])));
    }

    public function updateResend(string $id, string $fromAddress, string $fromName): Environment
    {
        return $this->hydrate($this->client->patch("/environments/{$id}/resend", ['from_address' => $fromAddress, 'from_name' => $fromName]));
    }

    public function detachResend(string $id): void
    {
        $this->client->delete("/environments/{$id}/resend");
    }

    public function resendKeys(string $id): array
    {
        return $this->client->get("/environments/{$id}/resend/keys")['data'] ?? [];
    }

    protected function changeVariables(string $id, string $method, array $variables): Environment
    {
        $pairs = array_is_list($variables)
            ? $variables
            : collect($variables)->map(fn ($value, $key) => ['key' => $key, 'value' => (string) $value])->values()->all();

        return $this->hydrate($this->client->post("/environments/{$id}/variables", ['method' => $method, 'variables' => $pairs]));
    }
}
